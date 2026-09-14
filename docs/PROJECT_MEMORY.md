# Stable project memory

- Brand: LinkEasy Social, light UI, red #FC2428, exact uploaded 3D ribbon mark. Centered benefits-led hero, six floating social icons, rich vertical Command dashboard, connected Flow section. Sample dashboard data must stay labeled.
- Stack: PHP views + service classes, SQLite default / MySQL-MariaDB supported, PDO, CSS modules bundled with esbuild, vanilla JS. No public file upload or arbitrary URL-fetch endpoint exists.
- Contact: lightweight general enquiry; quote topics expand a shared validated brief. Business data + mail outbox write atomically. Quote details remain in the contact message column. SMTP runs through CLI worker, not the request.
- Plans: Free / Managed have limits. Hosted BYO API / custom-quoted One-Time Setup allow unlimited accounts and no platform posting quotas; provider fees/quotas still apply. No recurring platform fee for those hosted options while the app operates, not a perpetual-service promise.
- About describes purpose, vision and work functions, not invented staff biographies.
- Production settings remain incomplete: real SMTP, Google, PayPal and hosting evidence not supplied. PAYPAL_ENABLED is false. MAIL_DRIVER=log never reports production delivery; jobs retry/dead-letter until configured.
- Social publishing, credential vault, provider token lifecycle and analytics workers are not included here. The account dashboard is an integration boundary to the existing app, not a finished social engine.
- Book source: user-supplied 72-page *Vibe Engineering Blocks*, Hasan, edition 1.0 (2026); all 47 blocks mapped in `docs/BOOK_AUDIT.md`.
- Update this file when stable decisions change; put transient tasks/results in dated audit/release notes. No credentials or customer data.

- GitHub publication target authorized by owner: `laheef/LinkEasy-Social`, direct default-branch `main` push without force. The root README is GitHub-oriented; actual `.env` remains excluded. The screenshot gallery lives in `docs/screenshots/`. See `docs/GITHUB_PUBLISH.md`; check remote/authentication state rather than assuming the push completed.
