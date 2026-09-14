# Current build / engineering QA entry points

- `npm ci --ignore-scripts && npm run build`: reproducible asset build; no Node runtime on hosting.
- `python3 -m venv .venv && .venv/bin/pip install -r tools/requirements.txt`: optional image-build tooling only.
- `php tools/migrate.php`: additive schema upgrades after backup.
- `php tools/worker.php --max-jobs=20 --max-seconds=45`: bounded mail worker; schedule in cron with real SMTP.
- `php tools/ops.php status 0 25`: private cursor-paginated queue metadata. See runbook for prune/requeue.
- `php tools/qa-engineering.php`, `php tools/qa-contact.php`, `python3 tools/qa-concurrency.py`: isolated local safety tests.
- `npm run test:ui`: non-destructive browser UI regressions against an already-running local server.
- `qa-contact-http.py` and `qa-security-browser.cjs`: **write test data**; require a separate marked copy. See `docs/OPERATIONS.md` and script headers. Never run against production.
- `python3 tools/package_complete.py [ZIP outside project]`: complete source distribution, migrated clean DB, empty logs and verified manifest.

See `docs/BOOK_AUDIT.md`, `.github/workflows/qa.yml` and `.skills/release-qa/SKILL.md`. Older design-tool notes below remain useful but do not supersede the current security/deployment runbook.

---

# Build tooling

No frontend build step is required to run the site — generated artifacts are
committed. These scripts only need re-running when inputs change.

## Platforms (`build_platforms.py`)

Single source of truth for every social platform shown across the site
(marquee, integrations ring, the dark platform matrix, mocks, FAQ counts).

- Platform list, display names, brand colors and available/planned status live
  in the `PLATFORMS` table at the top of `tools/build_platforms.py`.
- Brand glyphs are sourced from [Simple Icons](https://simpleicons.org/)
  (CC0) into `storage/build/si/<simple-icons-slug>.svg`.
- Platforms without a Simple Icon (e.g. Skool, Whop, Nostr, Gab, Truth Social)
  render a centered monogram letter — set `si=None` and provide the letter.

Regenerate after editing the table or adding icons:

```bash
# add/update an icon
curl -sL -o storage/build/si/bluesky.svg \
  https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/bluesky.svg
python3 tools/build_platforms.py
```

Outputs (do not hand-edit; each has an AUTO-GENERATED header):

- `config/platforms.generated.php` — slug / name / color / available
- `views/partials/icons-platforms.php` — SVG sprite symbols `#i-<slug>`
- `public/assets/css/platforms.css` — circular brand-tile colors

`config/app.php` requires the generated config under the `platforms` key.
Platforms with `available => false` render with a "Soon" marker in the
integrations matrix and are never described as live connections.

## Brand assets

The authoritative source is the 3D logo `storage/build/logo-source.png`
(a charcoal L-ribbon with a glossy red chevron on a black square, copied
from `/home/user/uploads/logo.png`).

- `tools/build_logo.py [source.png]` **removes the black background** from
  every mark. Dark pixels (luminance ≤ 12) are flood-filled from the edges so
  only the connected background is removed — the 3D L fades to near-black at
  its base, and a higher threshold (or a plain luminance key) tears that
  curved base away. The binary silhouette is median-denoised (3×3) and
  feathered 0.5px for anti-aliased edges. Note: Pillow 12's
  `ImageDraw.floodfill` silently no-ops on numpy-backed images, so the mask is
  built with PIL pixel access.
  - `logo-mark.png/.webp` — transparent cut in the original charcoal + red
    3D colors, for light surfaces (header, auth forms, 404, white hubs).
  - `logo-mark-light.png/.webp` — transparent cut with the slate ribbon
    recolored white (red chevron kept), for dark surfaces (footer, final
    CTA, dark hubs, auth aside).
  - `favicon.ico` (16/32/48/64) and `favicon-32.png` use the same
    transparent cut; `favicon-180.png` (apple-touch) and
    `favicon-192/512.png` (PWA) are opaque full-bleed black tiles because
    those platforms require an opaque square.
- `tools/build_og.py` rebuilds `public/assets/img/og-image.png` (1200×630)
  from the dark-surface (white/red) mark and brand colors.
- Media-library and composer mock imagery lives in
  `public/assets/img/media/` (real, vendored JPEG thumbnails with
  `loading="lazy"`) — never flat gradient placeholder boxes.
- Glyph-color safety: `platforms.css` forces `span.platform-avatar/.platform-chip/
  .matrix-tile/.integration-icon` to the tile color so a parent's green
  "Connected" state never tints the platform glyph.

## Frontend asset build and regression tests

Run `sh tools/build_assets.sh` after editing CSS or JavaScript. Templates serve
the generated `.min.css` / `.min.js` assets. The build uses pinned esbuild via
`npx`; there is no browser or Hostinger runtime dependency.

With Playwright installed locally, run `node tools/qa-public.cjs` and
`node tools/qa-studio.cjs`. Both accept an optional base URL. The studio checks
cover contracting headers, keyboard tabs, responsive views and motion preferences.
