# Engineering-audit update: migration and worker required

**Start with `docs/OPERATIONS.md`.** This release preserves your UI but changes notification delivery and adds database migrations. Back up first, preserve the live `.env`/database/uploads, run `php tools/migrate.php`, configure SMTP and add the bounded mail worker to cron. The included SQLite DB is clean and must never replace live data. Real web deployments enforce HTTPS; set trusted proxy IPs explicitly if needed. Payments remain disabled until accepted sandbox tests.

The hosting/layout guidance below still applies; any older instruction suggesting schema import alone or synchronous contact email is superseded by the runbook above.

---

> **Complete-package update:** `.env` is now included. For a fresh install, edit it; for an existing deployment, preserve your current secrets and runtime data. Do not replace an existing database with the clean packaged SQLite file. Start with `README.md` and `PRODUCTION_CHECKLIST.md`. The `/dashboard` route is still a mounting point for your existing social-management application, not a complete publishing backend.

# Deploying LinkEasy Social to Hostinger shared hosting

No build step and no `composer install` are required — the site ships with
its own lightweight autoloader and all generated assets are included. You
upload files, import one database schema (or use SQLite, which creates
itself), and create one `.env` file with your secrets.

---

## 1. Requirements (set in hPanel → **Advanced → PHP Configuration**)

- **PHP 8.2 or newer** (8.3/8.4 recommended).
- PHP extensions: `pdo_mysql` (or `pdo_sqlite` for SQLite), `curl`,
  `openssl`, `mbstring`, `json` — check availability and enable them in your hosting account.
- **Apache/LiteSpeed with rewrite support** (the included `.htaccess`
  handles clean URLs, security headers and the HTTPS redirect).
- A free SSL certificate (hPanel → **Security → SSL**), issued before you
  switch the site live.
- GD/Imagick are **not** needed on the server (image tooling runs locally).

---

## 2. Upload the files

Everything **except the contents of `public/` must live outside the web
root** so that `config/`, `src/`, `.env` and `storage/` can never be
downloaded over HTTP. Pick the layout that matches your domain.

### Layout A — recommended (addon/subdomain with a custom document root)

1. If your hosting plan/domain settings allow a custom document root,
   point it to the public folder, for example:
   `/home/uXXXXXX/linkeasy-social/public`
2. Upload the project folder (excluding your local `.env`, logs and development database) (via hPanel File Manager ZIP upload, or
   SFTP) to `/home/uXXXXXX/linkeasy-social/`:

   ```
   linkeasy-social/
   ├── public/          ← this folder is the document root
   ├── config/
   ├── routes/
   ├── src/
   ├── views/
   ├── database/
   ├── storage/logs/
   ├── .htaccess (in public/), .env, ...
   ```

   No code changes are needed with this layout.

### Layout B — main domain (document root is `public_html`, can't be moved)

1. Upload the application files to a folder **next to** `public_html`, e.g.
   `/home/uXXXXXX/les-app/` (all folders except `public/`, plus `.env`).
2. Upload the **contents** of `public/` into `public_html/`
   (including the hidden `.htaccess`).
3. Edit one line in `public_html/index.php` so it points at the app folder:

   ```php
   // replace:
   require dirname(__DIR__) . '/src/bootstrap.php';
   // with:
   require '/home/uXXXXXX/les-app/src/bootstrap.php';
   ```

   Everything else (`config/`, `views/`, `storage/`) is then resolved
   automatically from that location.

You do **not** need to upload `tools/`, `storage/build/`, test scripts, or
the SQLite dev database.

---

## 3. Database

### Option 1 — MySQL (recommended on Hostinger)

1. hPanel → **Databases → MySQL Databases**: create a database, a user, and
   attach the user with **all privileges**. Note the Hostinger-prefixed
   names, e.g. `u123_social` / `u123_appuser`.
2. Open **phpMyAdmin**, choose the new database, **Import**
   `database/schema.mysql.sql`.
3. Put the credentials in `.env` (host is `localhost`):

   ```env
   DATABASE_DSN="mysql:host=localhost;dbname=u123_social;charset=utf8mb4"
   DATABASE_USER="u123_appuser"
   DATABASE_PASS="the-database-password"
   ```

### Option 2 — SQLite (zero setup)

Ensure the `pdo_sqlite` extension is enabled, leave `DATABASE_DSN` pointing
at `storage/linkeasy.sqlite` (or blank), and make `storage/` writable (see
§7). Tables are created automatically on first request.

---

## 4. The `.env` file and every secret you need

Create `.env` in the application root (one level above `public/`) by copying
`.env.example`. **Never** put it inside `public_html`. Values are read once
at boot; edit them with hPanel File Manager.

| Variable | Required? | What to put |
|---|---|---|
| `APP_NAME` | yes | `LinkEasy Social` |
| `APP_URL` | yes | Your real URL, no trailing slash, e.g. `https://linkeasysocial.com`. Also update any explicit OAuth redirect, email and provider settings when moving domains. |
| `APP_ENV` | yes | `production` |
| `APP_DEBUG` | yes | `false` (hides errors from visitors) |
| `APP_TIMEZONE` | yes | e.g. `UTC` or `Asia/Karachi` |
| `DATABASE_DSN` / `DATABASE_USER` / `DATABASE_PASS` | yes | From §3 |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` | for Google login | From Google Cloud Console (§5) |
| `GOOGLE_REDIRECT_URI` | for Google login | `https://YOURDOMAIN/auth/google/callback` |
| `PAYPAL_MODE` | for paid signups | `live` for production (or `sandbox` while testing) |
| `PAYPAL_CLIENT_ID` / `PAYPAL_CLIENT_SECRET` | for paid signups | PayPal Developer Dashboard → Apps & Credentials → **Live** app |
| `PAYPAL_WEBHOOK_ID` | for paid signups | ID of the live webhook (§6) |
| `PAYPAL_PLAN_MONTHLY_ID` / `PAYPAL_PLAN_YEARLY_ID` | for paid signups | Live subscription **Plan** IDs (§6) |
| `MAIL_DRIVER` | yes | `smtp` in production (`log` just writes to a file) |
| `SMTP_HOST` | with smtp | Usually `smtp.hostinger.com` |
| `SMTP_PORT` / `SMTP_SECURE` | with smtp | `465`+`ssl` (implicit) or `587`+`tls` (STARTTLS) |
| `SMTP_USER` / `SMTP_PASS` | with smtp | Full mailbox email and its password |
| `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME` | yes | Must be a mailbox on your domain, e.g. `hello@linkeasysocial.com` |
| `SUPPORT_EMAIL` | yes | Public contact address shown in the footer/contact flow |
| `PRICE_MANAGED_MONTHLY` / `PRICE_MANAGED_YEARLY` | optional | Override the $19 / $190 defaults in one place |
| `GITHUB_URL` | optional | Legacy optional value; no longer used by hosted-plan pricing CTAs |

If Google login or PayPal is left unconfigured, those buttons/flows degrade
safely (Google routes to the email login; Managed billing routes to the
contact form). Secrets are server-side only — none are printed to the page.

Ready-made production `.env`:

```env
APP_NAME="LinkEasy Social"
APP_URL="https://linkeasysocial.com"
APP_ENV="production"
APP_DEBUG="false"
APP_TIMEZONE="UTC"

DATABASE_DSN="mysql:host=localhost;dbname=u123_social;charset=utf8mb4"
DATABASE_USER="u123_appuser"
DATABASE_PASS="CHANGE_ME_DB"

GOOGLE_CLIENT_ID="CHANGE_ME.apps.googleusercontent.com"
GOOGLE_CLIENT_SECRET="CHANGE_ME"
GOOGLE_REDIRECT_URI="https://linkeasysocial.com/auth/google/callback"

PAYPAL_MODE="live"
PAYPAL_CLIENT_ID="CHANGE_ME"
PAYPAL_CLIENT_SECRET="CHANGE_ME"
PAYPAL_WEBHOOK_ID="CHANGE_ME"
PAYPAL_PLAN_MONTHLY_ID="P-CHANGE-ME"
PAYPAL_PLAN_YEARLY_ID="P-CHANGE-ME"

MAIL_DRIVER="smtp"
MAIL_FROM_ADDRESS="hello@linkeasysocial.com"
MAIL_FROM_NAME="LinkEasy Social"
SMTP_HOST="smtp.hostinger.com"
SMTP_PORT="465"
SMTP_SECURE="ssl"
SMTP_USER="hello@linkeasysocial.com"
SMTP_PASS="CHANGE_ME_MAILBOX"

SUPPORT_EMAIL="support@linkeasysocial.com"
GITHUB_URL=""
```

---

## 5. Google OAuth (login/signup with Google)

1. https://console.cloud.google.com → create a project.
2. **APIs & Services → OAuth consent screen**: External, app name
   "LinkEasy Social", support/developer email, publish to **Production**
   once tested.
3. **Credentials → Create Credentials → OAuth client ID → Web application**.
4. Authorized JavaScript origins: `https://linkeasysocial.com`
5. Authorized redirect URI (must match `GOOGLE_REDIRECT_URI` exactly):
   `https://linkeasysocial.com/auth/google/callback`
6. Copy the Client ID and secret into `.env`.

---

## 6. PayPal subscriptions (Managed plan)

1. https://developer.paypal.com → **Apps & Credentials**, switch to
   **Live**, create an App → copy Client ID/secret.
2. In the live dashboard create a **Product** (subscription service) and two
   **Plans**: monthly (e.g. $19/month) and annual ($190/year). Copy the
   `P-…` plan IDs into `.env`.
3. Create a **Webhook** for the app with URL
   `https://linkeasysocial.com/billing/paypal/webhook`, subscribed to
   subscription events (`BILLING.SUBSCRIPTION.*`, `PAYMENT.SALE.COMPLETED`).
   Copy the webhook ID into `.env`. The handler verifies PayPal's signature
   and is idempotent — duplicate deliveries won't double-provision accounts.
4. Keep `PAYPAL_MODE="sandbox"` while testing with sandbox buyer accounts,
   then switch to `live`.

---

## 7. Email on Hostinger (contact form + password reset)

1. hPanel → **Emails → Email accounts**, create `hello@linkeasysocial.com`
   (or `support@…`) inside your domain.
2. Set `MAIL_DRIVER=smtp` with the SMTP settings above
   (`smtp.hostinger.com`, port **465** + SSL is simplest).
3. While `MAIL_DRIVER=log`, every message is appended to
   `storage/logs/mail.log` instead — useful to verify the form before email
   is configured.

## 8. File permissions

In File Manager (or SFTP): directories `755`, files `644`, and make the
writable folders writable by PHP (runs as your account user on Hostinger):

```
storage/          755 (775 if log/SQLite writes fail)
storage/logs/     755/775
storage/linkeasy.sqlite  664 only if you choose SQLite
```

`config/` and `.env` stay `644` and must not be group/world-writable.

## 9. SSL / HTTPS

Issue the free SSL in hPanel, then force HTTPS. The shipped
`public/.htaccess` already contains the 301 redirect (comment out those two
lines temporarily if you deploy before SSL is active). Secure session
cookies are enabled automatically once the site runs over HTTPS.

---

## 10. Go-live checklist

1. Visit `https://YOURDOMAIN/` — landing loads, logo and images appear.
2. `/robots.txt`, `/sitemap.xml`, `/site.webmanifest`, `/about`, `/contact`, `/help` and `/legal` return 200.
3. Submit the **contact form** → 302 redirect with a success message; email
   arrives (or appears in `storage/logs/mail.log` on the `log` driver).
4. **Sign up** with email/password → lands on `/dashboard`; log out and back
   in; wrong password shows the inline error.
5. **Continue with Google** → consent screen → returns logged in.
6. Pricing → Managed → button jumps to PayPal (sandbox first, then live).
7. Complete a real sandbox subscription flow and inspect its webhook deliveries: verified events update subscriptions and duplicate deliveries do not provision twice. A simulator alone does not prove end-to-end billing correctness.
8. `/login?next=/dashboard` redirect behavior and idle logout work.
9. Open a deliberately bad URL → branded 404; with `APP_DEBUG=false` no
   stack traces leak anywhere.
10. Re-test at 360px width and on a phone (hamburger menu, stacked pricing).

## 11. Changing domain later

Edit `APP_URL` in `.env` for canonical URLs, OG metadata, sitemap and absolute links.
Also change `GOOGLE_REDIRECT_URI` if explicitly configured, plus domain-based
email addresses and any provider settings. Rebuild branded OG artwork if it embeds the old domain. Then update the
Google console authorized origins/redirect and the PayPal webhook URL.

## Troubleshooting

- **All routes except `/` 404** — `.htaccess` didn't upload (it's hidden in
  File Manager; enable "Show hidden files"), or mod_rewrite is off.
- **500 error** — inspect private PHP error logs; verify the DSN and storage permissions. Avoid exposing debug output on a public production site.
- **Contact form submits but no email** — driver is `log`, SMTP creds/port
  are wrong, or the From address isn't a mailbox on the sending domain.
- **Google login fails with redirect_uri_mismatch** — the console URI must
  equal `GOOGLE_REDIRECT_URI` exactly (https, no trailing slash).
- **Can't log in / session drops** — SSL not active while Secure cookies are
  expected, or a proxy strips HTTPS; confirm the padlock and the `.htaccess`
  redirect are in place.
- **PayPal always sends you to Contact** — mode/plan IDs/credentials are
  blank; the code intentionally falls back until everything is configured.

## Public experience refresh

Read `UPDATE_NOTES.md` before updating an existing deployment. The six
`SOCIAL_*_URL` settings configure footer profile links; they are public values,
not secrets. BYO API replaces the self-hosting offer in public copy. Legal
drafts in `config/public-pages.php` require operator review. Do not re-import
schemas or overwrite the existing `.env`/runtime storage for this UI update.
