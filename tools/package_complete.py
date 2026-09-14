#!/usr/bin/env python3
"""Package every regular project file, including .env; sanitize runtime data.
The ZIP contains configuration and must NOT be placed in the public web root.
Usage: python3 tools/package_complete.py [destination.zip]
"""
from pathlib import Path
import sys, sqlite3, hashlib, datetime
from zipfile import ZipFile, ZIP_DEFLATED, ZipInfo

root = Path(__file__).resolve().parent.parent
destination = Path(sys.argv[1]).resolve() if len(sys.argv) > 1 else root.parent / 'releases/linkeasy-social-complete.zip'
if root in destination.parents:
    raise SystemExit('Write the ZIP outside the project to avoid recursive packaging and public exposure.')
destination.parent.mkdir(parents=True, exist_ok=True)
clean = sqlite3.connect(':memory:')
clean.executescript((root/'database/schema.sqlite.sql').read_text())
clean.execute('CREATE TABLE les_migrations (version VARCHAR(100) PRIMARY KEY, checksum VARCHAR(64) NOT NULL, applied_at VARCHAR(30) NOT NULL)')
for file in sorted((root/'database/migrations').glob('*.sqlite.sql')):
    clean.executescript(file.read_text())
    clean.execute('INSERT INTO les_migrations VALUES (?,?,?)',(file.name,hashlib.sha256(file.read_bytes()).hexdigest(),datetime.datetime.now(datetime.timezone.utc).isoformat(timespec='seconds')))
clean.commit()
replacement = {'storage/linkeasy.sqlite': clean.serialize(), 'storage/logs/mail.log': b'', 'storage/logs/app.jsonl': b''}
clean.close()
# Reproducible source distribution, not an installed tooling cache or VCS database.
excluded_dirs={'.git','node_modules','.venv','__pycache__','.cache','.pytest_cache'}
entries={}
for p in sorted(root.rglob('*')):
    rel=p.relative_to(root)
    if set(rel.parts)&excluded_dirs or not p.is_file() or p.is_symlink(): continue
    if p.name.endswith(('.sqlite-wal','.sqlite-shm','.sqlite-journal')): continue
    entries[str(rel)]=b'' if str(rel).startswith('storage/logs/') else p.read_bytes()
entries.update(replacement)
entries.pop('MANIFEST.sha256', None)
assert '.env' in entries and 'public/.htaccess' in entries
manifest = ''.join(hashlib.sha256(data).hexdigest()+'  '+name+'\n' for name,data in sorted(entries.items()))
entries['MANIFEST.sha256'] = manifest.encode()
with ZipFile(destination, 'w', ZIP_DEFLATED) as package:
    for name,data in sorted(entries.items()):
        info = ZipInfo('linkeasy-social/'+name)
        info.compress_type = ZIP_DEFLATED
        mode = 0o600 if name == '.env' else (0o755 if name.endswith('.sh') else 0o644)
        info.external_attr = (0o100000 | mode) << 16
        package.writestr(info, data)
with ZipFile(destination) as package:
    assert package.testzip() is None
    for line in manifest.splitlines():
        digest,name=line.split('  ',1)
        assert hashlib.sha256(package.read('linkeasy-social/'+name)).hexdigest()==digest
print(f'Complete package: {destination}\nFiles: {len(entries)}\nBytes: {destination.stat().st_size:,}\n.env: included; .htaccess: included; SQLite: migrated schema, no user data; mail log: empty; checksums: verified.')
