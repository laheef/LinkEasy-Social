# Hero / Contact / About refinement QA

Tested 14 September 2026 on local PHP 8.4.24 / Chromium (Playwright 1.58.2).

## Checks completed

- PHP syntax: all project PHP files passed.
- `php tools/qa-contact.php`: general vs quote requirements, all serialized details, allowlists, malformed arrays, count boundaries, URL/header/length validation, escaped HTML, 13 available platforms.
- `node tools/qa-public.cjs`: PASS 360 / 390 / 768 / 1024 / 1440. Public pages, shared header, SVG symbols, images, mobile navigation, pricing, anchors, aliases, sitemap and private-path protections.
- `node tools/qa-command.cjs`: PASS same five widths. Rich dashboard metric mouse/keyboard controls, six-node Flow layout, header contraction, motion persistence, reduced motion and offscreen pause.
- `node tools/qa-refinements.cjs`: PASS 360 / 390 / 768 / 900 / 1024 / 1440. Six floating social tiles remain above the dashboard and clear of copy/CTAs; no page overflow; pause/reduced motion; quote preselection, field visibility/required-state changes and retained values; About functions and symbols. No-JavaScript quote fallback also passed.
- `python3 tools/qa-contact-http.py BASE ISOLATED_COPY`: PASS on a clean extracted candidate package, with a separate PHP temp directory and log-only mail. CSRF rejected; honeypot did not store; general enquiry stored; full quote stored and serialized to email log; invalid quote retained escaped input; minimum-time error visible; invalid subject/email rejected; seventh rate-limited attempt blocked. Only two valid messages were written to the isolated database. No live credentials/services used.
- Visual screenshots reviewed: `refined-hero-*`, `refined-quote-*`, `refined-about-*` at 390 and 1440. Older screenshots are historical and remain included intentionally.

## Local performance sample

390×844 viewport, cold cache, 8 resource requests, 636,908 encoded response-body bytes including the document.

| Local lab condition | LCP | CLS | Load event |
|---|---:|---:|---:|
| Unthrottled | 232 ms | 0 | 209 ms |
| 4 Mbps / 40 ms latency / 4× CPU | 1,108 ms | 0.01075 | 1,821 ms |

Single-run development-server measurements, not production/field guarantees. Float animations reuse inline SVGs and add no network requests.

## Reproducing safely

Install Playwright in a tooling/parent directory (not inside the distributable project), then install Chromium and its OS dependencies. Start PHP separately using the documented preview command. Run the non-destructive PHP/browser tests above.

The HTTP test intentionally writes and throttles contact requests. Extract an archive into a **temporary separate directory**, add `.contact-qa-isolated` in that extracted project, create a separate PHP temp directory, and start that copy with `MAIL_DRIVER=log php -d sys_temp_dir=/absolute/test/tmp -S 0.0.0.0:8081 -t public public/router.php`. Run the Python HTTP test against local port 8081 and that extracted project path. It refuses the source directory, requires an empty contact database and never needs real customer information. Stop the test server and delete the temporary extraction afterwards. Do not run it on a deployed production service.

## Remaining production work

Live SMTP delivery, legal adoption, hosting/TLS, payment/provider credentials and the existing social backend integration are not verified by these tests. The archive supplies a complete website project, not a newly implemented publishing/analytics engine. Follow `PRODUCTION_CHECKLIST.md`.
