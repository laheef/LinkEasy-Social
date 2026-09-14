# Book-guided engineering audit plan

Source read in full: Hasan, *Vibe Engineering Blocks*, edition 1.0 (2026), all 72 PDF pages / 47 blocks. Supplied filename: `prompt engineering book.pdf`. Page references in the final audit refer to PDF page numbers. The book is guidance, not a security certification.

## Scope and boundaries
Preserve the existing PHP website, branding, responsive Command/Flow UI, pricing, public pages and expanded contact form. Harden the included account, contact and billing scaffold without inventing a social publishing engine. No live billing/email, remote deployment, GitHub push or production data mutation is authorized or needed for local QA.

## Prioritized work
1. Capture the current release as rollback reference and inventory code, input boundaries, storage and external calls.
2. Harden request validation, CSRF, sessions, safe redirects, security headers and proxy trust; bound request sizes. Make signup/reset/provider account writes transactional. Avoid unverified automatic account linking. Invalidate existing sessions after password reset.
3. Introduce additive versioned migrations and indexed database-backed rate limits. Use atomic writes and explicit SQLite/MySQL dialect handling.
4. Move contact/reset mail to a transactional outbox. CLI-only bounded worker, claims/leases, retry backoff and dead letters. Document at-least-once delivery, duplicate-email risk and cron operation.
5. Centralize outbound HTTP policy: exact trusted hosts, HTTPS, no redirects, bounded bodies/deadlines, status/schema validation, safe-only retries and circuit breaking. Tighten verified billing-event application and duplicate handling; do not silently enable live checkout.
6. Add structured allowlisted logs, request IDs, safe errors, bounded operational queries and explicit retention tools. Keep mail payloads private and out of production telemetry.
7. Add project rules, reproducible dependency setup, CI gates and deployment/runbook guidance. Do not add unnecessary frameworks, NoSQL or an MCP runtime.
8. Run unit/integration, concurrency, browser, asset and packaging checks; map all 47 blocks to evidence and limitations. Package the complete source, actual safe `.env`, additive migrations, tools, reports and assets with sanitized runtime data.

## Acceptance
Critical local safety regressions must pass. No provider mock is called a real provider test. No local benchmark is called production capacity. Production TLS/DNS, real SMTP/OAuth/PayPal, operator legal/retention choices and existing social-backend integration remain explicitly identified launch gates.
