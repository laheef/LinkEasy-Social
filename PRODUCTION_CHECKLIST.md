# Production readiness — actions still required

The full project package is deployable for website/authentication evaluation. It is **not a verified, complete social publishing backend**.

## Must be completed before public product launch

- [ ] Read the 47-block assessment in `docs/BOOK_AUDIT.md` and the deployment/worker runbook in `docs/OPERATIONS.md`.
- [ ] Back up, apply `php tools/migrate.php` in maintenance mode and verify existing account/data preservation.
- [ ] Schedule the notification worker in cron; verify real inbox arrival, queue lag/dead-letter handling and heartbeat alerts. Log-only development tests do not send real mail.
- [ ] Confirm explicit proxy trust, effective body/PHP limits, no-store session pages and query-string redaction in host/proxy access logs.
- [ ] Before multiple web nodes: shared MySQL, shared sessions, centralized logs and measured load/queue sizing; local sessions alone do not scale across nodes.
- [ ] Keep `PAYPAL_ENABLED=false` until sandbox checkout/webhook/entitlement acceptance passes with real provider configuration.


- [ ] Integrate the existing social-management application at `/dashboard`; verify account ownership and permissions across its routes.
- [ ] Verify social-provider OAuth/API integrations, token encryption and rotation, scheduling workers/cron, publishing retries, API quotas and analytics ingestion in that app.
- [ ] Configure SMTP, verify delivery of both general and setup-quote enquiries (including all structured fields), and complete a password-reset email flow. Assign an operator to monitor private contact records and notification failures. The supplied `MAIL_DRIVER=log` does not send mail.
- [ ] Configure Google login if offered; test real consent and exact callback URI.
- [ ] Create matching PayPal plans, credentials and webhook registration; test sandbox purchase/cancellation, replayed webhooks and failure paths before enabling live billing.
- [ ] Confirm current plan allowances and provider eligibility against actual backend gates; BYO API onboarding is currently arranged through support.
- [ ] Point the domain at `public/`, issue TLS, verify HTTPS/cookies and make sure private files are inaccessible.
- [ ] Use a new MySQL schema import for a new installation only, or verify the clean SQLite database is writable. Never replace an existing production database with this empty file.
- [ ] Configure backup/restore, error logging, log retention and monitoring. Check host filesystem permissions and provider secrets access.
- [ ] Review the draft legal pages, operator identity, retention schedule, refund policy, copyright process and applicable jurisdiction.
- [ ] Verify the complete flow on Hostinger and your real domain; local responsiveness tests do not prove live-service readiness.

Run `php tools/check_production.php` for a non-network configuration audit. It prints no credentials. Missing settings and policy drafts are expected to be reported with the included default `.env`.

## Included `.env`

- APP_URL is `https://laheef.dev`; change it for another domain.
- APP_ENV is `production`, APP_DEBUG is `false`.
- Database defaults to the portable project SQLite path.
- Mail defaults to logging until valid mailbox credentials are supplied.
- Google/PayPal credentials and official social-profile URLs are empty, not guessed.
- GOOGLE_REDIRECT_URI derives from APP_URL when left empty.

These defaults avoid leaking debug output or attempting real payments/email with fabricated credentials. They do not remove the need for configuration and live testing.
