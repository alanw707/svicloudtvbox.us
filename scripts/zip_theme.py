import os, zipfile, sys

src = 'theme/svicloudtvbox'
dst = 'theme/svicloudtvbox.zip'

if not os.path.isdir(src):
    print('Theme dir not found:', src, file=sys.stderr)
    sys.exit(1)

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
            zf.write(p, os.path.relpath(p, 'theme'))

print('Wrote', dst, os.path.getsize(dst), 'bytes')
