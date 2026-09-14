ALTER TABLE les_users ADD COLUMN auth_version INT NOT NULL DEFAULT 1;
CREATE TABLE IF NOT EXISTS les_mail_jobs (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
 dedupe_key VARCHAR(128) NOT NULL UNIQUE,
 payload MEDIUMTEXT NOT NULL,
 status VARCHAR(20) NOT NULL DEFAULT 'queued',
 attempts INT NOT NULL DEFAULT 0,
 available_at BIGINT NOT NULL,
 locked_until BIGINT NULL,
 claim_token VARCHAR(64) NULL,
 created_at BIGINT NOT NULL,
 finished_at BIGINT NULL,
 expires_at BIGINT NULL,
 last_error VARCHAR(100) NULL,
 INDEX idx_mail_ready (status, available_at, id),
 INDEX idx_mail_lease (status, locked_until),
 INDEX idx_mail_finished (status, finished_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS les_rate_limits (bucket VARCHAR(64) PRIMARY KEY, hits INT NOT NULL, reset_at BIGINT NOT NULL, INDEX idx_rate_expiry (reset_at)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS les_circuits (provider VARCHAR(100) PRIMARY KEY, failures INT NOT NULL DEFAULT 0, open_until BIGINT NOT NULL DEFAULT 0, probe_until BIGINT NOT NULL DEFAULT 0) ENGINE=InnoDB;
CREATE INDEX idx_resets_token ON les_password_resets(token_hash, used, expires_at);
CREATE INDEX idx_contacts_created ON les_contact_messages(created_at, id);
CREATE INDEX idx_subscriptions_active ON les_subscriptions(user_id, status);
