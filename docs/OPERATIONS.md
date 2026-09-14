# Deployment, worker and data runbook

## Scope
This package is the PHP public website and account/contact/billing integration scaffold. It adds a **notification outbox**, not a social publishing worker, provider-token vault or analytics ingestion service. Connect and test the existing social app separately.

## Updating an existing installation
1. Back up the database, existing `.env`, uploads and deployed code; verify you can restore the backup.
2. Put the site in maintenance mode and stop/drain old workers during migration. Keep configuration and persistent data outside the release directory if using atomic symlink releases.
3. Upload source and prebuilt assets. Preserve production `.env` and database; **never upload the archive's empty SQLite file over real data**. Merge new example settings into the existing environment.
4. Run `php tools/migrate.php` with the production database configuration. It creates baseline tables only if absent, then applies checksummed migrations in order. Existing user data is preserved. Do not edit an applied migration.
5. MySQL/MariaDB DDL can commit implicitly. If a migration fails partway, keep maintenance mode on, restore the verified backup or inspect/complete the failed DDL with a database operator. Do not repeatedly force a migration or delete its history row. SQLite migrations are transactional. No automatic destructive down-migration is provided.
6. Run `php tools/check_production.php`; complete the actions below, run smoke tests on a staging clone, then release traffic. Roll back code only if compatible with the expanded schema; preserve data and address security regressions rather than blindly restoring the vulnerable pre-audit release.

Fresh installs: the packaged SQLite DB already contains both migrations and no customer data. For fresh MySQL, configure DATABASE_DSN/USER/PASS and run `php tools/migrate.php`; no manual schema edit is required.

## PHP / host settings
- PHP 8.2+ with PDO SQLite or MySQL, mbstring, cURL and OpenSSL. Argon2id is preferred when the PHP build supports it; otherwise bcrypt. New passwords are bounded to 8–72 bytes.
- Point the document root at `public/` only. `.env`, storage, source, tests, docs and ZIP archives must remain inaccessible. The CLI worker/migration/ops scripts must never be web endpoints.
- `public/.htaccess` disables indexing, limits requests to 256 KiB, denies dotfiles, caches static assets and compresses text. `.user.ini` contains additional supported PHP-FPM limits; verify effective host settings because some require hPanel/server-level configuration. The app adds stricter 32 KiB form / 256 KiB webhook limits and rejects file uploads.
- Enable OPcache and HTTPS at the host. APP_FORCE_HTTPS=true redirects to the configured canonical origin; never derive this from an arbitrary Host header. HSTS is sent only when HTTPS is detected. Production denies framing; PHP's development server allows Arena preview embedding and does not enforce TLS.
- Native TLS needs no proxy setting. Behind a reverse proxy, set TRUSTED_PROXY_IPS to **exact, known upstream IPs** and have the proxy overwrite/append forwarded headers correctly. The app ignores forwarded TLS/IP headers from other peers and walks the trusted IP chain from the right. Do not trust every address or copy arbitrary client headers into the trust list.
- Cookies: HttpOnly, SameSite=Lax, Secure on production HTTPS; strict session IDs, rotation on login/logout, 60-minute request-idle timeout and 24-hour absolute authentication lifetime. Background session polling does not renew idle time. Password reset revokes older sessions through auth_version. There is no long-lived “remember me” feature.

## Email queue — NEW required deployment step
1. Set MAIL_DRIVER=smtp and valid SMTP_HOST/PORT/SECURE/USER/PASS, sender and support email. Hostinger commonly uses a mailbox username, port 465/ssl or 587/tls. SMTP verifies the peer certificate and hostname. Native PHP mail() is deliberately unsupported because it has no controlled transport timeout.
2. Keep MAIL_LOG_CONTENT=false. The supplied production MAIL_DRIVER=log **does not deliver mail or mark jobs sent**. It records metadata, and the job retries/dead-letters. Test email-body logs require APP_ENV=test/local plus MAIL_LOG_CONTENT=true; never enable those settings with real customer data.
3. In hPanel cron, run every minute (use the host's actual PHP executable/path):
   ```cron
   * * * * * /usr/bin/php /private/path/linkeasy-social/tools/worker.php --max-jobs=20 --max-seconds=45
   ```
   The runtime budget is checked **between jobs**; an in-flight network operation may extend it. SMTP has a bounded connection/protocol deadline. Overlapping cron runs are safe because job claims are transactional, token-owned leases.
4. Test: submit a general enquiry, a setup quote and a password reset. Confirm rows saved, jobs processed, real email received, links work once and old sessions are invalidated. Log-driver tests are not SMTP delivery tests.
5. Inspect jobs without exposing their payloads:
   ```sh
   php tools/ops.php status 0 25
   # Continue with next_after_id. Hard limit: 100 rows.
   ```
   Alert on old queued jobs, dead jobs, provider errors and missing `mail_worker_finished` log events. Connect a private log collector/alerting service; this package does not provision your monitoring account.
6. After fixing SMTP, requeue a specific stored contact notification if needed:
   ```sh
   php tools/ops.php resend-contact CONTACT_ID --confirm
   ```
   Dead/expired password reset jobs require a new reset request, not reuse of an expired link.

### Delivery semantics
- Contact record + notification job, and reset token + notification job, commit in one transaction.
- Form submission IDs deduplicate contact retries while the outbox tombstone remains. This is not indefinite deduplication after retention cleanup.
- Claims expire after 180 seconds. A crashed worker can be reclaimed. Retries use exponential delay plus jitter; five failed attempts end in a dead letter. Reset jobs stop at token expiry.
- SMTP is **at least once**: if the server accepts a message and a worker crashes before recording success, a duplicate email is possible. There is no false “exactly once” claim. Billing creation uses a provider idempotency key instead of relying on SMTP semantics.
- Sent/dead mail payloads are scrubbed; status metadata remains for diagnosis. Contact messages remain the original request record. Processing payloads can contain contact details/reset links in the private DB—use encrypted storage/backups and restricted DB access; no app-level vault is claimed.

## Retention / backup
`php tools/ops.php prune 30` is a dry run; add `--apply` only after approving your retention policy. It removes old expired rate-limit state, expired reset rows and completed/dead outbox metadata. Minimum age for reset/mail cleanup is seven days. It **never** deletes users, contact enquiries, subscriptions or payment events. Those require an operator-approved privacy/financial retention policy and access/deletion workflow.

The structured app log and opt-in test mail log are each bounded at approximately 5 MiB; old content is truncated when the bound is reached. Forward logs externally if history/alerts are needed. Logs are not backups. Protect database/backups at rest, restrict access, test restoration, and ensure backups follow deletion policies.

**Access logs:** do not record full query strings: OAuth codes and password-reset tokens can occur in URLs. For Apache use a reviewed format based on `%m %U %H`, not `%r` or `%q`; for Nginx use `$uri`, not `$request_uri`. Apply this at the proxy, CDN and host too. The app's own structured logs never log query strings, raw provider responses, request bodies or credentials.

## Payments and provider operations
- PAYPAL_ENABLED=false remains the default. Before enabling, test real sandbox create/approval, cancellation, suspension, expiry, replays, wrong plan/user binding and webhook delivery/reconciliation. Then configure live IDs and repeat approved live smoke tests.
- GET checkout shows confirmation only. The POST requires login, CSRF and a session-bound idempotency key. Only verified, supported subscription lifecycle events with a canonical provider snapshot and a **locally created, matching** subscription may update access. Sale/unknown events never grant permissions; minimal event metadata is stored.
- Out-of-order older snapshots are ignored; equal-time terminal states cannot be overwritten by activation. Failed processing returns a retryable response. Provider event ordering, payment reconciliation, paid-through cancellation/grace-period policy and actual end-to-end entitlement behavior still need production acceptance. This is not a complete financial ledger.
- Google uses expiring single-use state, PKCE, verified userinfo and an existing provider link; it does not auto-link a password account by matching email. Use the existing sign-in method for an already-registered address. A separate reauthenticated account-linking feature is not implemented.
- Outbound HTTP uses an exact trusted-provider host list, HTTPS, no redirects, 3-second connect / 10-second total per-attempt bounds and a 1 MiB response cap. Only safe/idempotent requests retry once with jitter. Shared circuits open after five transient failures for 30 seconds; one recovery probe is admitted. Configured provider failover is intentionally not invented for identity or payments.

## Scaling decision
Start with one web node + local SQLite + bounded cron worker for evaluation/low write contention. SQLite writes serialize; use local disk, not network-shared SQLite. No throughput capacity is certified.

For multiple web/worker nodes, use shared MySQL/MariaDB (database limiter/outbox/circuits already share state), a securely managed shared PHP session store, consistent application configuration, centralized logs, a queue-lag alert and measured PHP-FPM/DB sizing. Local PHP sessions are **not** multi-node-ready by themselves. Add Redis or a dedicated broker only after measuring load and choosing operational ownership. Keep the social engine's publishing jobs in its own tested integration boundary.

Do not publicly cache HTML containing sessions, CSRF tokens or reset data. The landing page has no expensive public DB query; assets are versioned by content and cached/compressed by the host. Confirm CDN cache behavior and tune indexes from actual slow-query evidence before claiming production scale.
