# SVICLOUD 15P gallery visual review

Status: local corrective gallery ready for production approval.

The previous lifestyle derivatives were rejected because embedded promotional copy remained visible. These replacements crop out the copy. The previous flat packaging artwork is replaced by a watermarked 3D presentation mockup so the packaging role reads as a retail package rather than a flat source graphic.

| Role | Delivered file | SHA-256 | Review |
|---|---|---|---|
| Primary/front | `svicloud-15p-primary-ai-watermarked.webp` | `1de18f50a56488fce5be5d509f899d6bdcc2803688c313847ffd7250b5062317` | Supplied AI primary; wordmark + domain watermark bottom-right |
| Angle/rear ports | `svicloud-15p-angle-watermarked.webp` | `db3b9b10b212416227e0ad52cedf2b2344136b6ed46132e0a8f848dce530da52` | Product angle/ports; watermark present |
| Packaging | `svicloud-15p-packaging-mockup-watermarked.webp` | `ec4c53e50629ca863f0e24cd79c087875a53a7b216fc151a1fdffbe010408aa9` | Retail packaging mockup based on supplied 15P packaging artwork; watermark present |
| Lifestyle/AI | `svicloud-15p-lifestyle-clean-watermarked.webp` | `4588cb00834fc1957dd947a2f97c5373622fda80e0371ddb2c51ce7b5f1ccf01` | Product/environment crop; embedded marketing copy removed; watermark present |
| Lifestyle/AI 2 | `svicloud-15p-lifestyle-clean-2-watermarked.webp` | `5ee389b81c689f5932679744c4e5dd6181852165f937f4982514c254995fef7a` | Product/environment crop; embedded marketing copy removed; watermark present |

## Acceptance checks

- Exactly five files and five roles are wired by `scripts/import_public_theme_fixture.php`.
- The former copy-heavy lifestyle files and flat `package-watermarked` derivative are removed from the repository.
- Every delivered file is WebP and includes the existing SVICLOUD wordmark plus `svicloudtvbox.us` watermark at bottom-right.
- Production deployment remains gated: this local correction has not been uploaded yet.
