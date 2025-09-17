import sys, os, io, colorsys
from statistics import mean

PDFS = [
    'docs/SviCloud 10p+ 新品发布会.pdf',
    'docs/SviCloud 10P+ 规格书.pdf',
]

hex_colors = []

try:
    import fitz  # PyMuPDF
    from PIL import Image
except Exception as e:
    print('Installing dependencies...', e, file=sys.stderr)
    import subprocess, sys as _sys
    subprocess.check_call([_sys.executable, '-m', 'pip', 'install', '--quiet', 'pymupdf', 'Pillow'])
    import fitz
    from PIL import Image


def sample_pdf_blue(pdf_path):
    if not os.path.exists(pdf_path):
        return None
    doc = fitz.open(pdf_path)
    if doc.page_count == 0:
        return None
    page = doc.load_page(0)
    # Render at moderate zoom
    mat = fitz.Matrix(2, 2)
    pix = page.get_pixmap(matrix=mat, alpha=False)
    img = Image.open(io.BytesIO(pix.tobytes("ppm")))
    # Downscale for analysis
    img = img.convert('RGB')
    max_w = 480
    if img.width > max_w:
        ratio = max_w / img.width
        img = img.resize((max_w, int(img.height * ratio)), Image.LANCZOS)
    pixels = img.getdata()

    # Collect HSVs and filter likely brand blue: hue ~ 200-240°, high saturation
    candidates = []
    for r, g, b in pixels:
        # normalize
        h, s, v = colorsys.rgb_to_hsv(r / 255.0, g / 255.0, b / 255.0)
        hue_deg = h * 360
        # Exclude near white/black/gray
        if s < 0.25 or v < 0.20 or v > 0.98:
            continue
        # blue region with some tolerance
        if 195 <= hue_deg <= 245:
            candidates.append((r, g, b))
    if not candidates:
        # fallback: take top saturated color overall
        sat_candidates = []
        for r, g, b in pixels:
            h, s, v = colorsys.rgb_to_hsv(r / 255.0, g / 255.0, b / 255.0)
            if s > 0.5 and 0.2 < v < 0.98:
                sat_candidates.append((r, g, b))
        if not sat_candidates:
            return None
        candidates = sat_candidates
    # Average candidates to get representative color
    rr = mean([c[0] for c in candidates])
    gg = mean([c[1] for c in candidates])
    bb = mean([c[2] for c in candidates])
    hexv = '#%02x%02x%02x' % (int(rr + 0.5), int(gg + 0.5), int(bb + 0.5))
    return hexv


for p in PDFS:
    try:
        c = sample_pdf_blue(p)
        print(os.path.basename(p), '->', c)
        if c:
            hex_colors.append(c)
    except Exception as e:
        print('Error sampling', p, e, file=sys.stderr)

# Pick the median-ish color if multiple
if hex_colors:
    # choose one closest to the average in RGB space
    def hex_to_rgb(h):
        h = h.lstrip('#'); return tuple(int(h[i:i+2], 16) for i in (0, 2, 4))
    rgbs = [hex_to_rgb(h) for h in hex_colors]
    avg = tuple(int(mean(cs)) for cs in zip(*rgbs))
    def dist2(a, b):
        return sum((x - y) ** 2 for x, y in zip(a, b))
    best = min(rgbs, key=lambda c: dist2(c, avg))
    final = '#%02x%02x%02x' % best
else:
    final = '#0a84ff'

print('FINAL_ACCENT', final)
