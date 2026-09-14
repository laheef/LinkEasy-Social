# LinkEasy Social engineering rules

Read `docs/PROJECT_MEMORY.md`, `docs/BOOK_AUDIT_PLAN.md`, `docs/BOOK_AUDIT.md` and the latest `UPDATE_NOTES.md` before changing the app. Use `.skills/release-qa/SKILL.md` before shipping.

## Architecture / product
- PHP 8.2+, server-rendered views, vanilla JS, self-hosted assets. No runtime Node/Python dependency. Keep services, validation and data access out of views.
- Preserve LinkEasy Social's exact 3D logo, responsive Command hero, animated Flow section, motion controls, benefits-led copy and role-only About team.
- BYO API is hosted, not self-hosted. Unlimited accounts only for BYO API/One-Time Setup. Setup is custom-quoted. Provider rules/fees apply. Never invent stats, staff or operational integrations.
- `/dashboard` is an integration scaffold. Do not call the publishing backend complete without implementing/testing the actual existing app integration.
- Small responsibility-focused files; CSS source is split into ordered modules bundled to a single file. Rebuild with `npm ci --ignore-scripts && npm run build`.

## Safety
- Reject malformed/oversized input at boundaries; validate options server-side and escape all HTML. Use bound SQL parameters and scoped ownership checks. Fail closed on unknown plan gates.
- Every browser state-changing route is POST + CSRF. Provider webhooks need verified signatures and canonical ownership/plan binding. GET never starts paid checkout.
- Never fetch public-user URLs server-side. Outbound HTTP uses the trusted-provider adapter with HTTPS, deadlines, bounded responses and no redirects. Do not relax this for link previews.
- Never log tokens, credentials, email/reset bodies, raw provider payloads or full URL query strings. Use allowlisted structured metadata. Keep `.env`, database and logs private and out of Git.
- Do not automatically link OAuth by email. Verify email/state/PKCE. Password reset must consume tokens atomically and invalidate old sessions.

## Reliability / data
- Database transaction callbacks perform DB work only: they may retry on deadlocks. No external calls inside transactions.
- Slow notification work goes into the outbox in the same transaction as the business write. Workers are bounded CLI processes with leases, retry limits and expiry. SMTP is at-least-once, not exactly-once.
- Add versioned migrations; never edit applied migrations or overwrite production data with the clean packaged DB. MySQL DDL changes need backup/maintenance/recovery planning.
- Limit reads and operational listings. Prefer existing relational storage; add caches/queues/databases only for measured needs. Never cache sessions/CSRF/reset pages publicly.

## Verification / delivery
- Run security/integration and concurrency tests on dedicated temporary data; no live payments/mail or remote changes without explicit authorization.
- Run browser checks, inspect screenshots, preserve 360px and reduced-motion behavior. Test changes on SQLite and MySQL/MariaDB if SQL changes.
- Complete source ZIP includes actual safe `.env`, lockfiles, schemas/migrations, source assets, docs and tools; sanitize runtime data. Exclude installed dependencies, cache and Git internals, not application files.
- Report verified, configured-but-untested, deferred and not-applicable items honestly. A local test is not a security certification or a production scale guarantee.
