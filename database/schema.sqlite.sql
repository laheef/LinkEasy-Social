-- LinkEasy Social scaffold schema (SQLite)
-- Tables are prefixed les_ so they can coexist with an existing application.
-- If you integrate with an existing users table, point src/Auth.php at it
-- and skip creating les_users / les_oauth_accounts.

CREATE TABLE IF NOT EXISTS les_users (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    name             TEXT NOT NULL,
    email            TEXT NOT NULL UNIQUE,
    password_hash    TEXT,
    plan             TEXT NOT NULL DEFAULT 'free',
    email_verified   INTEGER NOT NULL DEFAULT 0,
    remember_token   TEXT,
    created_at       TEXT NOT NULL,
    updated_at       TEXT
);

CREATE TABLE IF NOT EXISTS les_subscriptions (
    id                        INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id                   INTEGER NOT NULL REFERENCES les_users(id) ON DELETE CASCADE,
    paypal_subscription_id    TEXT UNIQUE,
    plan                      TEXT NOT NULL DEFAULT 'free',
    status                    TEXT NOT NULL DEFAULT 'active',
    billing_cycle             TEXT,
    current_period_end        TEXT,
    cancelled_at              TEXT,
    created_at                TEXT NOT NULL,
    updated_at                TEXT
);

CREATE TABLE IF NOT EXISTS les_subscription_events (
    id                        INTEGER PRIMARY KEY AUTOINCREMENT,
    paypal_event_id           TEXT NOT NULL UNIQUE,
    event_type                TEXT NOT NULL,
    paypal_subscription_id    TEXT,
    payload                   TEXT NOT NULL,
    received_at               TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS les_oauth_accounts (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id           INTEGER NOT NULL REFERENCES les_users(id) ON DELETE CASCADE,
    provider          TEXT NOT NULL,
    provider_user_id  TEXT NOT NULL,
    created_at        TEXT NOT NULL,
    UNIQUE(provider, provider_user_id)
);

CREATE TABLE IF NOT EXISTS les_password_resets (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    email         TEXT NOT NULL,
    token_hash    TEXT NOT NULL,
    expires_at    TEXT NOT NULL,
    used          INTEGER NOT NULL DEFAULT 0,
    created_at    TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS les_contact_messages (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        TEXT NOT NULL,
    email       TEXT NOT NULL,
    subject     TEXT NOT NULL,
    message     TEXT NOT NULL,
    created_at  TEXT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_subscriptions_user ON les_subscriptions(user_id);
CREATE INDEX IF NOT EXISTS idx_resets_email ON les_password_resets(email);
