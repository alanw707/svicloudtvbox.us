import os, zipfile, sys

src_candidates = ['theme/svicloudtvbox-lumen', 'theme/svicloudtvbox']
for candidate in src_candidates:
    if os.path.isdir(candidate):
        src = candidate
        break
else:
    print('Theme dir not found in candidates:', ', '.join(src_candidates), file=sys.stderr)
    sys.exit(1)

zip_name = os.path.basename(src) + '.zip'
dst = os.path.join('theme', zip_name)

if os.path.exists(dst):
    os.remove(dst)

with zipfile.ZipFile(dst, 'w', zipfile.ZIP_DEFLATED) as zf:
    for root, dirs, files in os.walk(src):
        # Skip junk
        dirs[:] = [d for d in dirs if d not in {'.git', '__pycache__', '.idea', '.vscode'}]
        for f in files:
            if f in {'.DS_Store', 'Thumbs.db'}:
                continue
            p = os.path.join(root, f)
            arcname = os.path.relpath(p, 'theme')
            zf.write(p, arcname)

print('Wrote', dst, os.path.getsize(dst), 'bytes')
