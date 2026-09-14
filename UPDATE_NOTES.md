# Latest update — full book-guided engineering QA

Read `docs/BOOK_AUDIT.md` for the complete 47-block mapping to the supplied 72-page book. `docs/OPERATIONS.md` explains installation, migrations, worker/cron, data handling and scale boundaries.

- Fixed empty/malformed CSRF handling, state-changing GET checkout, unsafe local redirect variants, unverified Google identity/email linking, reset races and old-session persistence.
- Added database-backed atomic rate limits, additive checksummed migrations, transaction rollback/retry, stricter ownership/plan binding and safer billing event ordering/replays. Tested on SQLite and MariaDB's MySQL-compatible path.
- Moved contact/reset mail to a transactional outbox: bounded CLI worker, token-owned leases, retry backoff, expiry/dead letters, payload cleanup and private CLI inspection/requeue. **Configure SMTP + cron before expecting mail delivery.**
- Added trusted-provider HTTP adapter/circuit breaker, bounded network/body handling, nonce CSP/security headers, explicit proxy trust, no-store session pages, boot configuration validation and allowlisted structured logs.
- Split CSS into ordered modules, moved dashboard queries out of its view, added pinned dependency locks, CI gates, Git ignore/baseline guidance, project memory/rules and a release-QA skill. Optional Pillow updated after advisory scanning.
- Expanded local unit/integration, SQL-dialect, concurrent-process, browser/auth/security, package and dependency checks. Exact test evidence lives under `qa/book-audit`.
- No social engine, credential vault, actual provider credentials, remote deployment or production capacity is invented. Payments stay disabled pending real sandbox acceptance; legal/hosting/alerting/provider work remains explicit.
- Full source package retains `.env`, all application/support files and source assets. Installed dependency/cache/Git directories are excluded; database/log paths are included with sanitized contents and migration history. Preserve existing production data when updating.

---

# Previous update — benefit-led hero, setup quotes & our purpose

## Hero

- New headline: **Make time for the ideas. Not the busywork.** Supporting copy focuses on planning, showing up and audience connection—not category, open-source or free labels in the headline.
- Six floating brand-icon tiles surround the copy and CTAs on larger screens. On phones/tablets, they become a staggered floating row below the copy and **above** the dashboard. No icons cover the text or controls.
- Lightweight CSS-transform animations reuse existing SVG symbols and the existing pause, offscreen and reduced-motion controls. No animation library or extra image requests.
- The rich Command dashboard and connected Flow section are preserved. “See it in action” now links directly to the dashboard preview.

## Contact & setup quotes

- General enquiries stay short. **One-Time Setup** and **Request a quote** expose a dedicated brief. Pricing links preselect the correct topic.
- Required quote details: use case, total accounts, API readiness, selected available platforms and help requested, plus the standard name/email/message.
- Optional context: brand, public URL, current tool, timing, budget/currency, country/time zone and phone/WhatsApp.
- Shared definitions in `config/contact.php`; available platforms come from the existing platform registry. `src/ContactInquiry.php` validates allowlists, lengths, URLs, account totals and malformed values on the server.
- All accepted details are saved as a structured plain-text message in the existing `les_contact_messages.message` column and included in the operator's email notification. **No database migration.** Notification errors are logged; the saved request remains in the database. There is no new admin inbox UI.
- CSRF, honeypot and 6/hour throttling remain. Too-fast legitimate submissions now show a retry error with retained details instead of a false success. Errors are escaped, retained and linked to their fields.
- JavaScript hides irrelevant quote fields; without JavaScript, the form still works and explains which section to complete. No credentials or attachments are requested. The privacy draft now describes optional quote data.
- Supplied `.env` still uses `MAIL_DRIVER=log`: persistence and the email-log output were tested, **not live email delivery**. Configure SMTP and monitor the private contact records before launch.

## About LinkEasy Social

- Replaced the generic stock landscape with a product-direction diagram: idea → plan → publish → learn.
- New purpose, vision and guiding thoughts explain why LinkEasy Social is being built, whose everyday coordination it addresses, and how choice and clear expectations shape the product.
- Team approach describes product/workflow, design/experience, engineering/integrations and setup/support functions. No named staff, biographies, photos, headcount or invented company history.
- Hosted BYO API, custom-quoted One-Time Setup and provider-limit caveats remain explicit. Free/Managed are not described as having unlimited accounts.

## Verification / delivery

- Public/Command regression suites pass at 360, 390, 768, 1024 and 1440px.
- New refinement suite passes at 360, 390, 768, 900, 1024 and 1440px: no icon/copy overlap, no page overflow or missing SVG symbols, motion preferences, conditional fields, preselection, no-JS fallback and four About functions.
- PHP validation tests plus isolated HTTP tests from an extracted candidate archive passed: accepted general/quote submissions, every quote detail in SQLite and email-log output, CSRF, honeypot, malformed fields, escaping, retained errors, minimum-fill time and rate limiting.
- Full package includes `.env`, editable and minified assets, configs, schemas, source artwork, documentation, screenshots and test tools. Database/mail log are included with clean contents; checksums cover the archive inventory.
- See `qa/REFINEMENT_QA.md` and `PRODUCTION_CHECKLIST.md`. The social backend integration and real provider/email credentials are still launch requirements, not solved by this visual/form update.

---

# Previous update — vertical analytics hero & connected workflow

This version supersedes the Studio tabs described in the history below.

- Restored a richer analytics-style hero: headline and CTAs **above** the full dashboard. Inside the dashboard, two columns show a selectable performance graph, sample metrics, queued content, connected social accounts and a real-image post preview. Sample figures are explicitly labeled—not represented as customer results or live data.
- Replaced the six uniform feature cards with a central branded workspace and three connected lanes. Each of the six tools has its own compact visual (media, calendar slots, account icons, messages, chart or report). Connection particles, hub halo and icon motion are paused offscreen and respect reduced motion.
- Desktop keeps the connected layout; tablet collapses the center hub to a top row; phones use a non-overlapping stack.
- Retained the contracting header, light theme, existing plan model, public pages, footer socials and motion controls.
- Fixed the feature-chart geometry so it stays inside its viewbox. Fixed the dashboard session-watch selector and optional plan-limit display warnings.

## This time: the complete package, including `.env`

`linkeasy-social-complete.zip` includes every regular project file, including `.env`, `.env.example`, `public/.htaccess`, sources, generated assets, schemas, tools and QA files. A checksum manifest makes the inventory verifiable. The included `.env` is portable and set to production mode/debug off with no machine-specific DSN. Real provider credentials are blank because they have not been supplied.

Runtime paths are included with sanitized content: an empty, initialized SQLite schema and an empty mail log. No development accounts, password reset tokens or contact messages are shipped. Existing deployments must retain their own `.env`, database and uploads.

All existing platform SVG geometry, the original logo and the Inter font used by the graphics tooling are included as offline build inputs under `tools/sources/`.

## Verification and readiness

`tools/qa-public.cjs` and `tools/qa-command.cjs` pass at 360, 390, 768, 1024 and 1440px: no page overflow, no missing images/SVGs, no feature-node overlap; metric selection, keyboard controls, motion pause and reduced-motion behavior work. PHP syntax checks pass.

Read `README.md` and `PRODUCTION_CHECKLIST.md` before deployment. The package is complete **for this website/auth/billing project**, not a new social publishing backend. `/dashboard` still mounts the existing application. Email/provider credentials, live-service tests, legal review and integration with that backend remain necessary before a full product launch. Run `php tools/check_production.php` for a configuration audit.

---

# Previous version history (superseded where noted)

# LinkEasy Social — Studio & motion update

## Latest refinement

- Header contracts after 64px of scrolling: 1280 → 1060px at a 1440px viewport. Tablet and phone widths contract proportionally, with navigation switching to its menu layout before links could collide.
- New centered hero with concise messaging and a purpose-built, interactive Create / Plan / Publish illustration. All three views work with mouse, touch and keyboard arrows/Home/End. Content is labeled as illustrative, not live account activity.
- All 25 landing sections have subtle icon accents. Publishing, connection and analytics accents use different small transform animations. Existing diagram and marquee motion is retained and paused offscreen.
- Animation runs only while the section is visible. The browser visibility API pauses motion in background tabs. Pause/Enable controls in the hero and footer remember a local preference. OS reduced-motion preferences take priority. Paused entry animations resolve to readable content, not a partially faded card.
- Unified the recent cards around red, ink and pale neutral surfaces, removed the mismatched dark-panel pastel blocks and reduced excessive button/card shadows. Scoped an overly broad `.is-selected` rule that had affected the hero tabs.
- The platform strip now draws only from currently available connections; planned connections remain in the labeled integrations matrix.

## Performance and test results

Runtime remains plain PHP, CSS and vanilla JavaScript. No animation framework, autoplay video, external font request or new frontend dependency was introduced. Existing local photographs are reused. The Latin font is preloaded; secondary imagery remains lazy-loaded. The headline does not wait for a scroll-reveal animation.

Production templates load `landing.min.css`, `platforms.min.css` and `main.min.js`. The runtime JavaScript is approximately **10 KiB uncompressed / 3 KiB gzip estimate**. Text compression is enabled in `.htaccess` where the host supports `mod_deflate`.

After editing source CSS/JS, run `sh tools/build_assets.sh` locally to refresh the minified files. esbuild is pinned as a build-time tool; Node is not needed on Hostinger. Upload both the templates and generated assets together.

Passed browser checks at 360, 390, 768, 1024 and 1440px:
- No document overflow, broken images or missing SVG symbols.
- Header contraction, all three hero views and keyboard tab navigation.
- Motion pause persistence, static content when paused, live reduced-motion changes, and offscreen animation suspension.
- Existing pricing, mobile navigation, new public pages and internal links.
- PHP and JS syntax validation.

Local Chromium lab check at 390×844, cache disabled, simulated 4 Mbps download / 40ms latency / 4× CPU slowdown: **LCP ~1.08 seconds; CLS 0** during initial load. This is a single local lab observation, not a Hostinger measurement or a field-performance guarantee. Re-test your deployed origin after clearing the host/CDN cache.

Research references consulted for product-first hero composition and restrained visual hierarchy: [Linear](https://linear.app/), [Notion](https://www.notion.com/), and this [Raycast hero review](https://hero.gallery/hero-gallery/raycast). These informed the direction; their copy, identity and layouts were not copied.

## Installing this latest refinement

Preserve `.env`, your database and runtime storage. Upload updated `views/`, `public/assets/`, `public/.htaccess` and `config/public-pages.php`. No new database migration or credentials are needed. The cookie policy was updated to describe the local motion preference. If you are upgrading from a version before the previous refresh, follow the full deployment notes below as well.

---

# Previous public experience refresh

## What changed

1. Floating, rounded header with a compact scrolled state, active navigation, accessible mobile menu, Escape handling and keyboard focus containment.
2. Editorial hero layout: oversized headline, separate introduction/CTA column, then a full-width product stage with real media. Mobile uses a stacked layout. The dashboard illustration is explicitly labeled; hero performance totals are not fabricated.
3. Replaced the fragile radial feature diagram with a responsive six-module workspace overview. Color-coded icon surfaces replace identical black tiles.
4. Composer steps are one ordered group: six columns when space permits, two rows of three in narrow containers. No orphan arrow or Publish pill.
5. Publishing source card now includes an actual image. Corrected its pricing link.
6. Timeline icons have opaque surfaces above the progress line. Track endpoints are calculated from the first and last icon centers; numbers remain on top.
7. Redesigned the right-hand Before/After panel into a compact workspace with three horizontal task rows, rather than stretched empty columns.
8. Rewrote pricing, comparison, FAQs, contact copy and final CTA around the confirmed hosted BYO API model. New label: **Bring Your Own API**. One-Time Setup is a **custom quote**, not server installation.
9. Added the shared header to Contact, policies, applicable billing and error pages. Split-screen authentication intentionally retains its own navigation.
10. Footer social icons are always visible. Unconfigured profiles are non-clickable, labeled as coming soon; configured profiles become accessible external links. No invented official URLs.
11. Added a detailed About page and Help / Getting Started page.
12. Added a Trust & Policies hub and original policy content covering privacy, terms, cookies, acceptable use, copyright reporting, disclosures, data access/deletion, billing/refunds and security. Sitemap includes the new canonical pages. `/dmca`, `/privacy-policy` and `/terms-of-service` redirect to their canonical pages.

## Confirmed plan rules

| Option | Hosting | Connected accounts | Posting allowance | Cost model |
|---|---|---|---|---|
| Bring Your Own API | Our platform | Unlimited | No platform daily/weekly/monthly quota | No monthly platform fee; user's API credentials |
| Free | Our platform | 3 | 30 queued posts | Free |
| One-Time Setup | Our platform | Unlimited | No platform daily/weekly/monthly quota | Custom-quoted onboarding; no recurring platform fee while app operates |
| Managed | Our platform | 25 | Unlimited queued posts | Existing $19/month or $190/year defaults |

All options remain subject to provider API eligibility, quotas, charges and usage rules. “While the app operates” is not a perpetual-service promise. Managed prices can be changed in environment configuration.

The internal plan ID `opensource` is retained to avoid breaking existing records. Its public label and allowances now describe hosted BYO API. `setup` has the same explicit unlimited account/post gates. Free and Managed gates are unchanged. Requests for BYO API and setup go to the contact form; signup still creates a Free account and does not trust a client-supplied plan. Provision BYO API through your existing authorized account-management process. This refresh does not implement a new social API credential vault, publishing engine or provisioning UI.

## Update an existing Hostinger installation

1. Back up your existing application files and database.
2. Preserve the server's `.env`, uploaded media, SQLite database (if used), logs and other runtime storage. **Do not overwrite them with development data.**
3. Replace `config/`, `views/`, `routes/web.php`, `src/bootstrap.php`, `src/Services/Mailer.php` and `public/assets/` from the update. Keep the deployment layout from `DEPLOYMENT.md`; if public files are split into `public_html`, preserve your adjusted `public_html/index.php` bootstrap path.
4. No new database migration is required for this refresh. **Do not re-import a schema into an existing installation just to deploy these UI changes.**
5. Add the optional profile settings below to your server `.env`. CSS/JS assets already use file modification timestamps for cache-busting; purge any Hostinger/CDN full-page cache after uploading.
6. Check `/`, `/about`, `/contact`, `/help`, `/legal`, all policy links, the mobile menu and the pricing toggle.
7. Review and approve legal drafts before making them final. Configure and test real email, Google login and PayPal separately; local UI tests do not prove live delivery or payments.

### Official social links

```env
SOCIAL_INSTAGRAM_URL=""
SOCIAL_FACEBOOK_URL=""
SOCIAL_TIKTOK_URL=""
SOCIAL_LINKEDIN_URL=""
SOCIAL_YOUTUBE_URL=""
SOCIAL_X_URL=""
```

Enter full official `https://…` profile URLs. These are public settings, not secrets. Blank/invalid values leave the corresponding icon non-clickable. Update these settings on the server, not in frontend JavaScript.

## Policy research and review

Scope was informed by the categories used by other social-management products, not copied policy language:

- Buffer's policy hub includes privacy, service terms and data-protection/API material. [1](https://buffer.com/legal)
- Metricool publishes separate refund guidance, including its own first-purchase and renewal rules. Those commercial rules are **not** adopted here. [2](https://help.metricool.com/en/article/metricool-refund-policy-es7zcp/)
- Postiz's privacy policy distinguishes controller/processor roles and explains connected-platform data handling. [2](https://postiz.com/privacy-policy)
- Postiz's terms cover acceptable use, third-party platform rules and data-protection provisions. [3](https://postiz.com/terms-of-service)

### Operator/legal review required

The following are intentionally not invented: legal entity/address, governing law, statutory DMCA-agent registration, a refund window, a retention schedule, a subprocessor inventory, international-transfer safeguards, or security certifications. Relevant legal pages carry a visible draft notice. Original content is in `config/public-pages.php`; the shared template is `views/pages/policy.php`.

Before adopting final policies, confirm:

- Operator identity, business/contact address, jurisdiction, eligibility and dispute provisions.
- Actual data flows, legal bases, retention periods, backup deletion and connected-platform disclosures.
- Hosting, email, payment and any analytics/AI vendors; evaluate whether a DPA, subprocessor page, transfer documentation or consent controls are needed for your customers/jurisdictions.
- A refund/withdrawal and setup-cancellation policy consistent with applicable consumer law.
- Whether a registered DMCA agent and formal counter-notice process are applicable. The copyright page does not claim agent registration or DMCA safe-harbor qualification.
- Operational ownership of security, deletion and copyright requests. The contact flow creates a support request, not an automated deletion or takedown.
- Actual functionality and security of the existing social-management backend; do not infer encryption-at-rest or a certification from landing-page styling.

The Help page is practical getting-started guidance, not fabricated API documentation. No fake status uptime, certifications, customer counts or named employees were added.

## Additional maintenance fixes

- Environment loader accepts comments outside quoted values while preserving `#` inside quoted secrets; example settings no longer use ambiguous inline comments.
- Asset versioning falls back to the active document root for split `public_html` deployments.
- SMTP refuses unknown/insecure transport modes, validates email/header inputs and normalizes message body line endings. Live SMTP delivery still requires a real mailbox test.
- Contact subject selection is exact and preserved on validation errors; pricing yearly equivalent shows cents rather than rounding the annual charge misleadingly.

## Verification

Browser checks at 360, 390, 768, 1024 and 1440px: no document horizontal overflow, no missing image loads and no missing SVG symbols on the homepage. Checked all 13 public/subpages at 360 and 1440px: HTTP 200, one shared header each, no document overflow. Mobile menu opens and About navigation works. PHP and JavaScript syntax checks pass.

See the live preview for the updated version. External Google/PayPal/SMTP operations require production credentials and have not been verified against your live services in this refresh.
