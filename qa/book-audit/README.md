# Engineering QA evidence — 14 September 2026

- `engineering-sqlite.txt` / `engineering-mysql.txt`: 73 assertions each; MariaDB 11.8.6 exercised the MySQL-compatible path. Provider HTTP mocked.
- `concurrency-*.txt`: real concurrent PHP processes, atomic rate budget, contact/job dedupe, unique worker claims, abandoned lease recovery and single-use password resets.
- `public-ui.txt`, `command-ui.txt`, `refinement-ui.txt`: responsive regression results, 360–1440px; no-JS and reduced-motion coverage.
- `contact-http.txt`, `security-browser.txt`: final extracted-package acceptance with synthetic accounts/messages; test-only mail-log delivery, not live SMTP.
- `fault-injection.txt`: deliberately unavailable DB, safe error response with no internal path/SQL/trace disclosure.
- `npm-audit.json`, `python-audit.json`: final dependency advisory snapshots (no known advisories reported); not a permanent guarantee.
- `performance.json`: single-run cold-cache local lab samples; not production capacity.
- `php-lint.txt`, `contact-validation.txt`: syntax and validation checks.
- `production-gates.txt`: expected ACTION/DISABLED/MANUAL entries, not falsely marked complete.

Screenshots `../refined-*` show the current visual release; older screenshot sets are retained as history. Read `docs/BOOK_AUDIT.md` for the complete 47-block assessment and `docs/OPERATIONS.md` before deploying.
