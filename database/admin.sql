-- MĀRGHĀ Wellness — admin panel tables
-- Run once on an existing database: cPanel › phpMyAdmin › (select database) › Import.
-- Safe to run again (IF NOT EXISTS). Already included at the end of schema.sql for new installs.

SET NAMES utf8mb4;

-- Admin accounts. The first account is created at /admin/setup.php.
CREATE TABLE IF NOT EXISTS admin_users (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username       VARCHAR(60)  NOT NULL,
    name           VARCHAR(120) NOT NULL,
    email          VARCHAR(190) NULL,
    role           ENUM('admin','viewer') NOT NULL DEFAULT 'admin',
    password_hash  VARCHAR(255) NOT NULL,
    last_login_at  DATETIME NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admin_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed login attempts, used to slow down password guessing.
CREATE TABLE IF NOT EXISTS admin_login_attempts (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address    VARCHAR(45) NOT NULL,
    username      VARCHAR(60) NULL,
    attempted_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_login_attempts_ip (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Editable page text (Admin › Page text). Missing keys use the defaults in includes/settings.php.
CREATE TABLE IF NOT EXISTS settings (
    setting_key    VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value  MEDIUMTEXT NULL,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
