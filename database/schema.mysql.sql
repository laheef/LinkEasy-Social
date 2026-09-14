-- LinkEasy Social scaffold schema (MySQL 8+ / MariaDB)
-- utf8mb4 throughout. Prefix les_ avoids collisions with an existing app.

CREATE TABLE IF NOT EXISTS les_users (
    id             BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name           VARCHAR(120) NOT NULL,
    email          VARCHAR(190) NOT NULL UNIQUE,
    password_hash  VARCHAR(255) NULL,
    plan           VARCHAR(40) NOT NULL DEFAULT 'free',
    email_verified TINYINT(1) NOT NULL DEFAULT 0,
    remember_token VARCHAR(100) NULL,
    created_at     DATETIME NOT NULL,
    updated_at     DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS les_subscriptions (
    id                        BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id                   BIGINT UNSIGNED NOT NULL,
    paypal_subscription_id    VARCHAR(120) NULL UNIQUE,
    plan                      VARCHAR(40) NOT NULL DEFAULT 'free',
    status                    VARCHAR(40) NOT NULL DEFAULT 'active',
    billing_cycle             VARCHAR(20) NULL,
    current_period_end        DATETIME NULL,
    cancelled_at              DATETIME NULL,
    created_at                DATETIME NOT NULL,
    updated_at                DATETIME NULL,
    INDEX idx_subscriptions_user (user_id),
    CONSTRAINT fk_sub_user FOREIGN KEY (user_id) REFERENCES les_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS les_subscription_events (
    id                        BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    paypal_event_id           VARCHAR(120) NOT NULL UNIQUE,
    event_type                VARCHAR(120) NOT NULL,
    paypal_subscription_id    VARCHAR(120) NULL,
    payload                   MEDIUMTEXT NOT NULL,
    received_at               DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS les_oauth_accounts (
    id               BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id          BIGINT UNSIGNED NOT NULL,
    provider         VARCHAR(40) NOT NULL,
    provider_user_id VARCHAR(120) NOT NULL,
    created_at       DATETIME NOT NULL,
    UNIQUE KEY uq_provider_account (provider, provider_user_id),
    CONSTRAINT fk_oauth_user FOREIGN KEY (user_id) REFERENCES les_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS les_password_resets (
    id         BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email      VARCHAR(190) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used       TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    INDEX idx_resets_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS les_contact_messages (
    id         BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(120) NOT NULL,
    email      VARCHAR(190) NOT NULL,
    subject    VARCHAR(200) NOT NULL,
    message    TEXT NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
