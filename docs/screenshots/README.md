# README screenshot gallery

These images are committed documentation assets for GitHub. They show the actual rendered public website from the September 2026 QA pass. The dashboard's numbers are labelled sample data; no customer accounts or private form submissions are shown.

| Documentation image | Existing QA source | Viewport width |
|---|---|---:|
| `homepage-desktop.jpg` | `qa/refined-hero-1440.jpg` | 1440px |
| `homepage-mobile.jpg` | `qa/refined-hero-390.jpg` | 390px |
| `connected-workflow-desktop.png` | `qa/flow-1440.png` | 1440px |
| `setup-quote-desktop.jpg` | `qa/refined-quote-1440.jpg` | 1440px |
| `setup-quote-mobile.jpg` | `qa/refined-quote-390.jpg` | 390px |
| `about-desktop.jpg` | `qa/refined-about-1440.jpg` | 1440px |
| `about-mobile.jpg` | `qa/refined-about-390.jpg` | 390px |

The hero captures show the first screen rather than the entire long homepage. The About and quote captures are full-page images. All seven are referenced from the root README using repository-relative paths, which work in GitHub's rendering. They are not served as customer-uploaded media.

## Refreshing the gallery

1. Start a local PHP preview and install the locked Playwright tooling.
2. Run `node tools/qa-refinements.cjs http://127.0.0.1:8080 --screenshots` to refresh the six `refined-*` images above.
3. Visually inspect them for layout, typography and motion/reduced-motion behavior. Copy approved results into this directory using the names above.
4. If the Flow section changes, capture the current `.flow-map` after it enters the viewport using Playwright, and replace the workflow image.
5. Check every README image path and commit the documentation images alongside README changes. Do not capture credentials, authenticated customer data or populated private enquiries.
