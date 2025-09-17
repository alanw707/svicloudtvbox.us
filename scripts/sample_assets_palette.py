import os, sys
from collections import Counter
from statistics import mean

# Simple palette sampler for PNGs in /assets
# Heuristic: downscale and take top saturated hues for accent, plus neutrals for background/border.

ASSETS_DIR = 'assets'

try:
    from PIL import Image
except Exception as e:
    import subprocess
    subprocess.check_call([sys.executable, '-m', 'pip', 'install', '--quiet', 'Pillow'])
    from PIL import Image

def rgb_to_hex(rgb):
    r,g,b = rgb
    return '#%02x%02x%02x' % (r,g,b)

def sample_image(path):
    img = Image.open(path).convert('RGB')
    img = img.resize((64, 64))
    pixels = list(img.getdata())
    return pixels

def sat(r,g,b):
    mx = max(r,g,b); mn=min(r,g,b)
    if mx==0: return 0.0
    return (mx-mn)/mx

def pick_palette(pixels):
    # Count colors
    cnt = Counter(pixels)
    # Split into saturated vs neutral
    sats = [(c, n) for c,n in cnt.items() if sat(*c) >= 0.35 and sum(c)/3 < 230 and sum(c)/3 > 30]
    neuts = [(c, n) for c,n in cnt.items() if sat(*c) < 0.15]
    sats.sort(key=lambda x: x[1], reverse=True)
    neuts.sort(key=lambda x: x[1], reverse=True)
    # Take a few
    accents = [rgb_to_hex(c) for c,_ in sats[:4]]
    neutral_dark = rgb_to_hex(neuts[0][0]) if neuts else '#0a0a0a'
    neutral_light = '#f8fafc'
    border = '#e5e7eb'
    return accents, neutral_dark, neutral_light, border

all_pixels = []
for fn in os.listdir(ASSETS_DIR):
    if fn.lower().endswith('.png'):
        all_pixels.extend(sample_image(os.path.join(ASSETS_DIR, fn)))

accents, neutral_dark, neutral_light, border = pick_palette(all_pixels)
print('ACCENTS', accents)
print('NEUTRAL_DARK', neutral_dark)
print('NEUTRAL_LIGHT', neutral_light)
print('BORDER', border)
