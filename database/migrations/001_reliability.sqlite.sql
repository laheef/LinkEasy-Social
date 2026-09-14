ALTER TABLE les_users ADD COLUMN auth_version INTEGER NOT NULL DEFAULT 1;
CREATE TABLE IF NOT EXISTS les_mail_jobs (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 dedupe_key TEXT NOT NULL UNIQUE,
 payload TEXT NOT NULL,
 status TEXT NOT NULL DEFAULT 'queued',
 attempts INTEGER NOT NULL DEFAULT 0,
 available_at INTEGER NOT NULL,
 locked_until INTEGER,
 claim_token TEXT,
 created_at INTEGER NOT NULL,
 finished_at INTEGER,
 expires_at INTEGER,
 last_error TEXT
);
CREATE INDEX idx_mail_ready ON les_mail_jobs(status, available_at, id);
CREATE INDEX idx_mail_lease ON les_mail_jobs(status, locked_until);
CREATE INDEX idx_mail_finished ON les_mail_jobs(status, finished_at);
CREATE TABLE IF NOT EXISTS les_rate_limits (bucket TEXT PRIMARY KEY, hits INTEGER NOT NULL, reset_at INTEGER NOT NULL);
CREATE INDEX idx_rate_expiry ON les_rate_limits(reset_at);
CREATE TABLE IF NOT EXISTS les_circuits (provider TEXT PRIMARY KEY, failures INTEGER NOT NULL DEFAULT 0, open_until INTEGER NOT NULL DEFAULT 0, probe_until INTEGER NOT NULL DEFAULT 0);
CREATE INDEX IF NOT EXISTS idx_resets_token ON les_password_resets(token_hash, used, expires_at);
CREATE INDEX IF NOT EXISTS idx_contacts_created ON les_contact_messages(created_at, id);
CREATE INDEX IF NOT EXISTS idx_subscriptions_active ON les_subscriptions(user_id, status);
