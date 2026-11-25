-- MĀRGHĀ Wellness — Content editor user level
-- Run ONCE in phpMyAdmin › (select database) › SQL. Existing accounts keep their level. No data is deleted.
-- (If your admin_users table has no `role` column yet, run database/roles.sql first.)
ALTER TABLE admin_users
    MODIFY role ENUM('admin','editor','viewer') NOT NULL DEFAULT 'admin';
