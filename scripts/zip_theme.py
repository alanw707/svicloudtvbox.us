import os
import sys
import zipfile
from pathlib import Path

src = Path('theme/svicloudtvbox-lumen')
if not src.is_dir():
    print('Theme dir not found at expected location:', src, file=sys.stderr)
    sys.exit(1)

zip_name = src.name + '.zip'
dst = src.with_name(zip_name)

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
