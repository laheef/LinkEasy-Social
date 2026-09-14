# Private runtime storage

Keep this folder outside the web document root and writable by the PHP account.

The complete ZIP includes a clean `linkeasy.sqlite` database initialized from `database/schema.sqlite.sql`, plus an empty `logs/mail.log`. It does not ship development accounts, password-reset messages or contact submissions.

For a **new** install, use this clean database or configure MySQL in `.env`.
For an **existing** install, preserve the existing database and logs; do not overwrite user data with the empty distribution copy.

`MAIL_DRIVER=log` writes messages here instead of sending email. Those messages can include password-reset links, so never serve or share a live mail log publicly. Configure SMTP and test delivery before launch.


## Engineering release
The packaged DB contains additive migration history and no customer/job records. Run `php tools/migrate.php` on existing databases after backup. New `app.jsonl` structured logs contain metadata only; production mail bodies are not logged. Mail jobs are private DB records consumed by `tools/worker.php`. See `docs/OPERATIONS.md` for SMTP/cron, leases/retry/expiry, payload cleanup, explicit retention and backup/encryption requirements. Never expose this directory or overwrite live contents with packaged clean files.
