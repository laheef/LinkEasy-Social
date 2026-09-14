# LinkEasy Social — book-guided engineering QA

**Audit date:** 14 September 2026  
**Reference:** Hasan, *Vibe Engineering Blocks*, edition 1.0 (2026), supplied as `prompt engineering book.pdf`. **All 72 PDF pages were read**, including the 47 blocks, rules/skills guidance and index. References below use PDF page numbers. The book is an introductory engineering checklist, not a security standard or certification.

## Executive conclusion

**The previous project followed some recommendations, but not all.** The responsive UI, prepared queries, password hashing, CSRF form tokens, configuration files and browser tests were useful foundations. They did not establish production reliability or security on their own.

This release fixes material issues in the **included PHP website/account/contact/billing scaffold**: stricter input boundaries and CSRF, safer sessions/redirects/OAuth, atomic data updates, database-backed concurrency controls, an email outbox/worker, bounded provider HTTP, redacted structured telemetry, migrations, reproducible tooling and broader automated tests.

**It is still not a completed or certified social publishing SaaS.** `/dashboard` remains the existing-app integration point. Real publishing/scheduling/analytics workers, provider credential storage and tenant isolation within that existing app must be integrated and tested separately. Live SMTP, Google, PayPal, HTTPS/DNS, hosting capacity, alerts and legal adoption remain launch gates. The local tests below must not be interpreted as evidence that those services are operational.

## Important deployment changes

1. **Back up and migrate:** preserve production `.env`, database and uploads, then run `php tools/migrate.php`. Never replace real data with the archive's clean SQLite file.
2. **Configure SMTP and cron:** notifications now run through `tools/worker.php`. Follow `docs/OPERATIONS.md`. The default production `MAIL_DRIVER=log` does not deliver or mark mail as sent.
3. **Review proxy/session settings:** HTTPS is enforced on real web deployments; forwarded headers are trusted only for explicit proxy IPs. Shared sessions are still required before multiple web nodes.
4. **Payments stay gated:** `PAYPAL_ENABLED=false` until real sandbox acceptance, provider setup and entitlement-policy review are complete.
5. The complete source package includes actual `.env`, lockfiles, source assets, schemas/migrations, tooling and reports. Runtime data/logs are sanitized; installed dependencies, caches and Git internals are not deployment source files. Do not expose the ZIP publicly if your `.env` later contains real credentials.

## Significant findings and changes

| Finding in reviewed code | Impact | Resolution in this release |
|---|---|---|
| Empty CSRF submitted against an uninitialized session could compare equal | Missing-token checks were insufficient | Require a valid nonempty 64-hex token and an existing server-side token; browser tests reject empty/forged CSRF |
| Checkout creation happened on GET | Cross-site navigation could trigger a state-changing provider call | GET only renders confirmation; authenticated POST requires CSRF, rate limit and session-bound provider idempotency key |
| Google profile did not require `email_verified`; matching email automatically linked accounts | Identity confusion/account-linking risk | Strict verified profile validation, PKCE, expiring single-use state; no automatic email-based linking |
| Local redirect guard allowed backslash/encoded variants | Browser normalization could turn a nominal local path into an external destination | Central decoded-path guard rejects protocol-relative, backslash and control-character destinations |
| Registration/provider linking/reset contained separate related writes | Partial records and concurrent token consumption | Transactional writes; single-use reset update; deadlock-safe DB-only transaction retry |
| Password reset did not revoke other sessions; polling renewed idle time | Old sessions could remain authenticated | `auth_version`, idle and absolute lifetime checks; polling no longer advances idle activity |
| File limiter read/modified/wrote without a lock across the operation | Concurrent bypass and no shared multi-node state | Atomic indexed database limiter with per-IP and selected per-account buckets; HTTP 429/Retry-After |
| Initial MySQL limiter stress test deadlocked on duplicate insert locking | Requests failed under concurrent contention | Exclusive no-op upsert plus bounded rollback-safe transaction retries; concurrent tests rerun successfully |
| Contact/reset email executed inside request | Slow SMTP delayed pages; save/delivery failure gap | Transactional outbox and bounded CLI worker, claims/leases, expiry, retry backoff, dead letters |
| OAuth/payment HTTP checks were inconsistent and provider errors could be logged verbatim | Weak boundary handling and sensitive-data leakage | One trusted-host HTTPS adapter, no redirects, response caps, deadlines, checked statuses/JSON, safe-only retry and shared circuit breaker |
| Billing upsert used SQLite-only syntax; event mapping trusted too much payload context | MySQL breakage and entitlement/ownership risk | Portable transaction logic; locally owned subscription + canonical provider plan/user binding; supported lifecycle events only; duplicate/out-of-order handling |
| Raw event/provider/mail data in diagnostics | PII/reset-token/credential exposure | Allowlisted JSON telemetry with request IDs; minimal billing-event metadata; production body logging disabled; completed mail payload scrubbed |
| Unknown plan gates allowed access | Typos or undeclared checks could fail open | Unknown gates now deny; ownership and plan-boundary tests added |
| Signup terms checkbox was unnamed and not validated; remember-me UI had no implementation | UI implied behavior the server did not enforce | Server-enforced terms acknowledgement; misleading remember-me checkbox removed (not a legal-consent certification) |
| No versioned upgrade workflow; dependency ranges and ad hoc build setup | Reproducibility/upgrade risk | Checksummed additive migrations, npm integrity lock, pinned Python build requirements, venv instructions, CI definition |
| Optional Pillow build dependency audit reported advisories | Unsafe local image-processing dependency versions permitted | Pin updated to Pillow 12.3.0; Python and npm audit outputs retained. No Pillow runtime is used by the PHP app |
| Stylesheet exceeded 2,500 lines and dashboard view queried DB directly | Harder maintenance and mixed concerns | Seven ordered CSS source modules bundled into one CSS request; dashboard data moved to a service |

### Deliberate limitations / better approaches than blindly copying examples

- The book's password diagram is conceptual: real password hashes use random salts and an adaptive algorithm, not a single unsalted deterministic digest. This app prefers Argon2id with bcrypt fallback.
- CORS is not authentication or CSRF protection. This same-origin app emits no permissive CORS headers; protected actions still require authentication/authorization/CSRF.
- A transaction alone is not enough for concurrency. This release uses conditional updates, row locks/SQLite write transactions, unique keys and tested retry behavior.
- Do not automatically retry a payment/OAuth operation or switch identity/payment providers after an ambiguous result. Retry is limited to reads or explicitly idempotent operations. Checkout uses `PayPal-Request-Id`.
- SMTP cannot provide an exactly-once guarantee. A worker crash after remote acceptance can cause a duplicate email on retry; that limitation is documented rather than hidden.
- Retain relational storage: these users/subscriptions/reset/job records benefit from constraints and transactions. NoSQL would add infrastructure without resolving a demonstrated need.
- Do not cache session-bearing HTML or CSRF/reset pages at a public CDN. There is no heavy anonymous database query to justify a new application cache; existing versioned assets are the appropriate cache target.
- An arbitrary 600-line rule is a heuristic, not a reason to split working code at random. CSS was separated at responsibility boundaries; service extraction targeted actual mixed responsibilities.
- No MCP server, Kubernetes deployment, Redis cluster or external monitoring account was added just to fill a checklist. Additional infrastructure needs an operational owner and measured need.

## All 47 book blocks mapped to this project

**Legend:** Implemented/verified = local evidence for the included scope; Partial = some controls exist but production/integration work remains; Tooling = development workflow, not a customer feature; Not applicable = intentionally not added without a relevant feature.

| # | Block / PDF page | Assessment and evidence |
|---:|---|---|
| 01 | Plan first · p10 | **Tooling added.** `docs/BOOK_AUDIT_PLAN.md` records scope, priorities, boundaries and acceptance before implementation. |
| 02 | MVP · p11 | **Scope preserved.** Harden the useful website/account/contact slice; do not pretend the missing social engine was implemented. |
| 03 | Git · p13 | **Tooling/partial.** Local source baseline and `.gitignore` added; `.env`/runtime data ignored. No older Git history existed to audit; no private remote/push/backup configured. Previous ZIP retained locally as rollback reference. |
| 04 | Lockfiles · p14 | **Tooling added.** Exact npm versions + integrity lock, pinned Python requirements, point-in-time npm/pip-audit scans. Rebuild/install evidence retained. Refresh advisories routinely. |
| 05 | venv · p15 | **Tooling added.** Optional image builders installed/tested in `.venv`; excluded from source ZIP. PHP deployment needs no Python. |
| 06 | Env vars · p16 | **Improved.** Actual `.env`/example, central boot validation (`RuntimeConfig`), explicit HTTPS/proxy/payment/logging settings. Real provider values remain blank. |
| 07 | Secrets · p17 | **Improved/partial.** Git ignores, private deployment paths, metadata-only logger, no raw provider/error bodies, safe production mail logging. Pattern scan found no matching live-key/private-key signatures in tracked source/current history; not proof that unknown secrets can never exist. Host/vault/backup access still needs operator controls. |
| 08 | CLAUDE.md · p19 | **Tooling added.** Agent-neutral `AGENTS.md` and a `CLAUDE.md` entry point. |
| 09 | Agent memory · p20 | **Tooling added.** `docs/PROJECT_MEMORY.md` records stable architecture/product facts without secrets. |
| 10 | Skills · p21 | **Tooling added.** `.skills/release-qa/SKILL.md` packages the repeatable release procedure. |
| 11 | Modularity · p23 | **Improved.** Focused auth, reset, contact, queue, HTTP, circuit, config and logging classes; seven ordered CSS modules, each under 600 lines. |
| 12 | Separation · p24 | **Improved.** Templates render data; contact/reset/outbound/billing work is in services. Dashboard query removed from its view. Routes coordinate rather than implement SMTP/reset persistence. |
| 13 | JSON · p26 | **Improved.** Provider JSON checked for status, bounded size/depth, object shape and consumed field types; queue/webhook schemas validated. Structured quote data keeps compatibility with the existing message column. |
| 14 | SQL · p27 | **Verified.** Prepared statements; SQLite and MySQL/MariaDB code paths exercised. MariaDB 11.8.6 was the actual MySQL-compatible test server, not Oracle MySQL 8 certification. |
| 15 | Data modeling · p28 | **Improved.** Existing related tables/FKs retained; `auth_version`, outbox/rate/circuit tables and uniqueness constraints added. No duplicate provider ownership or raw-event PII store introduced. |
| 16 | Migrations · p29 | **Added.** Ordered, checksum-verified SQLite/MySQL migrations; repeat-run and populated-old-schema preservation tested. Backups/maintenance required; MySQL DDL has partial-commit recovery caveats. |
| 17 | Transactions · p30 | **Improved/verified.** Registration, provider creation, reset consumption, contact+outbox and webhook effects are atomic. Rollback injection tested. External calls stay outside retryable DB transactions. |
| 18 | Indexing · p31 | **Improved.** Queue ready/lease/finished, reset-token lookup, rate-expiry, contact date and subscription status indexes. No production slow-query workload was supplied; these are access-pattern indexes, not a claimed production tuning result. |
| 19 | N+1 queries · p32 | **Reviewed.** No current per-row DB query loop found in public pages. Auth lookup cached per request; dashboard fetch is bounded. Real social-engine lists remain outside scope. |
| 20 | NoSQL · p33 | **Not applicable.** Relational DB is the better fit here; no document store added. |
| 21 | TLS · p36 | **Partial.** App HTTPS redirect, explicit proxy trust, Secure cookies and HTTPS-only HSTS policy; SMTP/HTTP peer verification. Real domain certificate, TLS versions, redirects and mixed-content/CDN behavior still require hosted acceptance. Local development intentionally differs. |
| 22 | Input validation · p37 | **Improved/verified.** Scalar/array/body boundaries, allowlists, lengths, account counts, malformed form rejection, file-upload rejection, signup terms and provider schemas. Apache/PHP limits supplied; host must confirm effective settings. |
| 23 | XSS · p38 | **Improved/verified.** HTML escaping, JSON-script hex escaping, nonce CSP, no raw input templating; retained malicious test text stays escaped. Inline CSS remains allowed for existing styles—CSP is defense in depth, not proof of XSS absence. |
| 24 | CSRF · p39 | **Improved/verified.** Nonempty session tokens on browser mutations; payment GET made read-only. Webhooks use verified provider signatures, not CSRF tokens. |
| 25 | CORS · p40 | **Appropriate same-origin default.** No wildcard/credentialed cross-origin API policy introduced. Future separate API origins require an explicit allowlist plus independent auth checks. |
| 26 | SSRF · p41 | **Improved/current surface constrained.** No arbitrary user URL fetcher exists. Contact URLs are stored as text only; HTTP adapter allows exact provider HTTPS hosts, no redirects, verified TLS. Any future link-preview/downloader must add DNS/IP revalidation and resource limits before activation. |
| 27 | Hashing · p42 | **Verified.** Salted adaptive password hashes, rehash-on-login, bounded inputs; reset tokens stored as SHA-256 of high-entropy random tokens. No plaintext password storage. |
| 28 | Authentication · p43 | **Improved/verified locally.** Session rotation/strict IDs, idle/absolute expiry, reset-driven revocation, verified OAuth identity/PKCE. Google responses were mocked; real consent/callback still needs testing. |
| 29 | Authorization · p44 | **Improved/partial.** Protected dashboard/checkout, ownership guards, unknown gates deny, canonical billing plan/user binding. Tenant/resource isolation inside the missing social app is not verified. |
| 30 | Brute force · p45 | **Improved/verified.** IP + account login/reset limits, password work for missing accounts, cooldowns. Limits include attempts, not only failures; tune carefully to balance abuse and shared-IP/accessibility concerns. |
| 31 | Timeouts · p47 | **Improved.** HTTP connect/total bounds, bounded SMTP connection/protocol deadline, response caps and finite workers. Native mail() disabled rather than falsely claiming a timeout for it. |
| 32 | Retry · p48 | **Added/verified.** Safe/idempotent HTTP retry only; queue exponential backoff+jitter and five-attempt cap; rollback-safe DB deadlock retries. No unbounded loops or blind payment retry. |
| 33 | Circuit breaker · p49 | **Added/verified.** Shared DB circuits for HTTP providers, cooldown and one half-open probe. SMTP resilience uses queue backoff rather than a pretend interchangeable payment/email failover. |
| 34 | Error handling · p50 | **Improved.** Safe top-level response, request reference, metadata-only exception logs, checked provider failures and preserved queued work. Alerts and incident response ownership are still operational requirements. |
| 35 | Race conditions · p51 | **Improved/verified.** Concurrent limit, duplicate contact, queue claim/lease recovery and reset tests pass on SQLite and MariaDB. MySQL deadlock uncovered during testing was corrected and retested. |
| 36 | Background jobs · p53 | **Added for notifications.** Transactional outbox + CLI cron worker. Operator must schedule/monitor it. Not a new social publishing scheduler; SMTP remains at least once. |
| 37 | Caching · p54 | **Appropriate existing target.** Content-versioned assets, bundled CSS, host compression/static caching, per-request user lookup reuse; private/no-store HTML. OPcache/CDN/production cache behavior remains host work. |
| 38 | Rate limiting · p55 | **Added/verified.** Atomic shared DB IP/account buckets, trusted-proxy chain handling, 429/Retry-After, webhook budget. Edge-level volumetric DDoS protection is not replaced by app rate limits. |
| 39 | Pagination · p56 | **Applied where relevant.** Ops queue listing uses `id > cursor`, max 100 rows; worker claims one bounded job. No customer post/media list endpoint exists here to paginate yet. |
| 40 | Error tracking · p58 | **Partial.** Central local exception/fatal capture and worker/provider events; no external tracker, alert destination or uptime monitor provisioned. Runbook describes required alerting. |
| 41 | Structured logs · p59 | **Added/verified.** JSON event/request/user metadata with context allowlist, no bodies/tokens/query strings, bounded file size. Host/CDN access logs require their own query-string redaction and retention. |
| 42 | Testing · p61 | **Expanded.** Unit/integration, SQL dialect, concurrent-process, browser auth/security, public UI, no-JS, package and dependency checks. Live providers, load saturation and penetration testing remain outside the verified evidence. |
| 43 | CI/CD · p62 | **Tooling/partial.** Pinned-action GitHub QA workflow added with build/test/dependency gates. It has not run in a remote repo and deliberately does not auto-deploy without a chosen target/secrets/approval. |
| 44 | Domain & DNS · p63 | **Operator work.** Deployment/runbook instructions and configured domain preserved. No DNS/hosting/mail records were changed or remotely verified. |
| 45 | Unified interface · p65 | **Partially applied.** Shared HTTP adapter and queue→Mailer boundary with injectable test transport. No unsafe automatic provider substitution for payments/identity; a real secondary provider needs explicit configuration and semantics. |
| 46 | Playwright · p66 | **Expanded/verified.** Responsive public/refinement suites plus isolated signup/reset/session/CSRF/redirect/CSP checks and screenshots. |
| 47 | MCP · p67 | **Not applicable to app runtime.** Existing agent tools suffice for this task. No unnecessary privileged MCP endpoint/server added. |

## Verified evidence

- **73 engineering assertions on SQLite and 73 on the MySQL-compatible MariaDB path**, with injected provider transports (no real provider calls).
- **Concurrent-process tests on both databases:** 20 rate requests → exactly 5 admitted; 20 identical contact submits → one contact/job; 20 workers → 10 unique claims; abandoned leases recovered; 10 simultaneous resets → one successful token consumption.
- **Browser security/auth flow:** headers/no-store/CSP, private paths, malformed/oversized requests, missing/forged CSRF, signup terms, session rotation, reset, second-session revocation, unsafe redirect and idle expiry.
- **Contact HTTP/worker flow:** valid general/quote storage, quote field serialization, retry errors, escaping, honeypot, CSRF, 429 throttling, worker output with a test-only log driver. This is not live SMTP delivery.
- **Public visual regressions:** homepage/public routes, assets/symbols, navigation, pricing, dashboard metrics, header contraction, Flow layout and motion controls at the documented five widths; refinements additionally at 900px and no-JavaScript fallback.
- **Dependencies:** npm audit and pip-audit report no known advisories for the final pinned dependency sets at audit time. The optional build-time Pillow version was upgraded after the initial advisory scan.
- **Migration/package:** old populated SQLite upgrade preserves account/hash; repeated migrations are a no-op; final source manifest and sanitized migrated database/logs checked. MySQL-compatible migrations and services exercised on MariaDB.

Exact machine-readable logs and benchmark/screenshot results are under `qa/book-audit/` and `qa/`. Counts describe assertions/tests executed, not a line-coverage percentage or proof of zero defects.

## Latest local performance sample

390×844 viewport, cold cache, 8 resource requests, 637,103 encoded body bytes including the document. CSS modules still bundle into **one stylesheet request**; no animation library or network-heavy backend was added to the landing page.

| Local lab profile | LCP | CLS | Load event |
|---|---:|---:|---:|
| Unthrottled | 280 ms | 0 | 258 ms |
| Simulated 4 Mbps, 40 ms latency, 4× CPU | 1,332 ms | 0.01075 | 1,897 ms |

Single-run development-server observations, not field metrics or a users-per-second capacity benchmark. The different timings from earlier visual releases include machine/load variability; no unsupported speedup is claimed. Use real hosting telemetry and a realistic load test before scale decisions.

## Remaining launch gates

1. Integrate and authorize the actual social engine: tenant ownership, OAuth/token vault, scheduling/publishing retries, quotas, media validation, analytics and provider policy compliance.
2. Configure SMTP, verify inbox arrival/deliverability and run/monitor the notification worker. Set SPF/DKIM/DMARC with the actual mail provider.
3. Verify Google and PayPal sandbox/live flows, webhook reconciliation and billing/access policies using real credentials; keep payments disabled until accepted.
4. Deploy behind real TLS with verified private-root isolation, proxy trust, effective PHP/HTTP limits, backups, encrypted storage and token-free access logs.
5. Establish alerting, approved retention/deletion procedures and incident ownership. Default local logs/cron are not a managed operations service.
6. For scale: measure realistic write/queue/API workloads; move to shared MySQL plus shared sessions and centralized logs before multiple web nodes. No users-per-second or production capacity is claimed.
7. Adopt legal/operator policies and review account linking/email verification/abuse needs against the real product.

**Bottom line:** this is a substantially hardened, book-reviewed source release with concrete local evidence and an actionable runbook—not a promise that every book block is universally necessary or that unconfigured production services have passed QA.
