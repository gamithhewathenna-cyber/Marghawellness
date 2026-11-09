-- MĀRGHĀ Wellness — user levels (Admin / Viewer)
-- Run ONCE in phpMyAdmin › (select database) › SQL.
-- Existing accounts become "admin", so nobody loses access. No data is deleted.

ALTER TABLE admin_users
    ADD COLUMN role ENUM('admin','viewer') NOT NULL DEFAULT 'admin' AFTER email;
