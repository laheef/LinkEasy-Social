# Release QA procedure

Use before packaging changes to LinkEasy Social.
1. Read AGENTS.md and scope the change. Record plan and acceptance criteria. Back up the previous release. Never run destructive tests on production.
2. Install locked build/test tooling. Use a venv for optional Python image builders. Audit dependencies without printing configuration values.
3. Run PHP lint, contact/engineering tests and concurrent DB tests. For schema/SQL changes also test a dedicated MySQL/MariaDB instance and upgrades of a populated old schema. Verify transaction rollback, replay handling and server-side permissions.
4. Build assets; run all three public/refinement suites and the isolated security-browser suite. Check CSP console errors, 360px layout, keyboard controls, pause/reduced motion and screenshots. Check performance under a documented lab profile.
5. Package a clean candidate; extract it separately. Verify manifest, empty customer tables, empty logs and applied migration checksums. Test contact → outbox → worker with a test-only log driver. Test signup/reset/session invalidation. Stop and remove test services/data.
6. Write the evidence report. Distinguish mocks from live integrations, lab timing from production capacity, and source completeness from product-launch readiness.
7. Package all application/support files including actual safe `.env`; exclude installed dependency/cache/Git directories and sanitize runtime contents. Never put the ZIP inside public/. Present the complete archive and highlight any new migration/cron requirements.
8. Push/deploy only after explicit authorization and approved target credentials. A green local test alone must not trigger a production payment or deployment.
