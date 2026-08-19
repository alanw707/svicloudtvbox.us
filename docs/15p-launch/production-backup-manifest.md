# Corrective production backup manifest

Pre-update checkpoint for the corrected 15P gallery and pre-order copy release.

- Backup directory: `/home/alanw/.pi/backups/svicloudtvbox.us/2026-08-18-15p-preorder-corrective-preflight`
- Generated before any corrective production write.
- Raw DB/uploads/theme archives remain outside Git because they contain private site data; this committed manifest and `scripts/verify_release_backup.py` make their identities and integrity independently checkable.
- Verification command: `python3 scripts/verify_release_backup.py /home/alanw/.pi/backups/svicloudtvbox.us/2026-08-18-15p-preorder-corrective-preflight`
- Verification result: `pass=true`, gzip readable, 802 uploads archive members, 212 remote theme files, all listed hashes matched.

| Artifact | Bytes | SHA-256 |
|---|---:|---|
| `local-wordpress.sql.gz` | 545227 | `e7b8687725829a5696ac06dc31681bbcb7b52442bb7180cf6631095b272358b4` |
| `uploads.tar.gz` | 163463080 | `12a336a3a01f60767b466da8cc82efffecc14b4247f81cde2b04927adc50372a` |
| `remote-theme-current.tar.gz` | 14598977 | `fea31f033c791cfd3e44bc6378cf5b8a80a8c395700c59ca421f2c3148d8d6d3` |
| `remote-final.htaccess` | 3612 | `4ac00e41189d1c59620cd261b26a5340a34677e22fab7784a8ecf6cf86834f70` |

## Prior production reference captured

The read-only product reference is `production-product-before.json` in the same directory. It records product `1204`, prior media `1210–1214`, `$379/$288`, `notify`, `onbackorder`, and purchasability. The live REST reference reported `stock_quantity=-1`; this was corrected to `0` during the approved deployment. Deployment ordering and post-write verification are recorded in `docs/15p-launch/production-deployment-log.md` and the committed production evidence directory.
