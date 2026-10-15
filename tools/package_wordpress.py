"""Build separate WordPress theme/plugin ZIPs for native dashboard installation."""
from pathlib import Path
from shutil import copy2, copytree
from zipfile import ZipFile, ZipInfo, ZIP_DEFLATED
import json

ROOT = Path(__file__).resolve().parents[1]
WP = ROOT / 'wordpress'
THEME = WP / 'wlf-agri-theme'
CORE = WP / 'wlf-agri-core'
for filename in ['style.css', 'app.js']:
    copy2(ROOT / filename, THEME / 'assets' / filename)
copytree(ROOT / 'assets', THEME / 'assets/images', dirs_exist_ok=True)

source = (ROOT / 'catalog-data.js').read_text()
data = json.loads(source[source.index('[', source.index('window.WLF_PRODUCTS')):].strip().removesuffix(';'))
(CORE / 'samples').mkdir(exist_ok=True)
(CORE / 'samples/products.json').write_text(json.dumps(data, ensure_ascii=False, indent=2) + '\n')

required = {
    'wlf-agri-theme': ['style.css','functions.php','header.php','footer.php','index.php','page.php','theme.json','assets/style.css','assets/theme.css','assets/app.js','assets/editor.js','inc/starter.php','inc/customizer.php','inc/shortcodes.php','inc/partials/catalogue.php','inc/partials/enquiry.php'],
    'wlf-agri-core': ['wlf-agri-core.php','includes/products.php','includes/enquiries.php','assets/admin.js','samples/products.json'],
}
out = ROOT / 'downloads'
out.mkdir(exist_ok=True)
for name, expected in required.items():
    folder = WP / name
    assert all((folder / path).is_file() for path in expected)
    files = sorted(p for p in folder.rglob('*') if p.is_file())
    assert all(not p.is_symlink() for p in files)
    target = out / (name + '.zip')
    with ZipFile(target, 'w', compression=ZIP_DEFLATED) as archive:
        directories = sorted({folder, *(p.parent for p in files)})
        for directory in directories:
            directory.chmod(0o755)
            entry = ZipInfo(name + '/' + (directory.relative_to(folder).as_posix().strip('.') + '/' if directory != folder else ''))
            entry.create_system = 3
            entry.external_attr = (0o40755 << 16) | 0x10
            archive.writestr(entry, b'')
        for path in files:
            path.chmod(0o644)
            entry = ZipInfo.from_file(path, arcname=name + '/' + path.relative_to(folder).as_posix())
            entry.create_system = 3
            entry.external_attr = 0o100644 << 16
            entry.compress_type = ZIP_DEFLATED
            archive.writestr(entry, path.read_bytes())
    with ZipFile(target) as archive:
        assert archive.testzip() is None
        for path in files:
            assert archive.read(name + '/' + path.relative_to(folder).as_posix()) == path.read_bytes()
    print(f'{target.name}: {len(files)} files, {target.stat().st_size} bytes; integrity verified')
