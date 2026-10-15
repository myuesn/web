"""Create a complete cPanel ZIP with no enclosing directory or build dependencies."""
from pathlib import Path
from zipfile import ZipFile, ZipInfo, ZIP_DEFLATED

ROOT = Path(__file__).resolve().parents[1]
OUTPUT = ROOT / 'downloads' / 'wlf-tam-site.zip'
CORE = ['index.html', 'products.html', 'about.html', 'quote.html', 'contact.html',
        'style.css', 'app.js', 'catalog-data.js']
images = sorted(p for p in (ROOT / 'assets').rglob('*') if p.is_file())
files = [ROOT / name for name in CORE] + images
assert all(p.exists() and not p.is_symlink() for p in files)
assert all(p.suffix.lower() in {'.png', '.jpg', '.svg', '.webp', '.ico', '.jpeg'} for p in images)
OUTPUT.parent.mkdir(exist_ok=True)

with ZipFile(OUTPUT, 'w', compression=ZIP_DEFLATED) as archive:
    for directory in ['assets/', 'assets/parts/']:
        entry = ZipInfo(directory)
        entry.create_system = 3
        entry.external_attr = (0o40755 << 16) | 0x10
        archive.writestr(entry, b'')
    for path in files:
        name = path.relative_to(ROOT).as_posix()
        entry = ZipInfo.from_file(path, arcname=name)
        entry.create_system = 3
        entry.external_attr = 0o100644 << 16
        entry.compress_type = ZIP_DEFLATED
        archive.writestr(entry, path.read_bytes())

with ZipFile(OUTPUT) as archive:
    assert archive.testzip() is None
    assert all(name in archive.namelist() for name in CORE)
    assert len(archive.namelist()) == len(files) + 2
    for path in files:
        name = path.relative_to(ROOT).as_posix()
        assert archive.read(name) == path.read_bytes()
        assert (archive.getinfo(name).external_attr >> 16) & 0o777 == 0o644
print(f'Complete package: {len(files)} files + 2 folders, {OUTPUT.stat().st_size} bytes')
print(OUTPUT)
