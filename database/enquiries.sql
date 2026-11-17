-- MĀRGHĀ Wellness — "Begin your journey" enquiry form
-- Run ONCE in phpMyAdmin › (select database) › SQL. Adds 3 columns; no data is changed or deleted.
ALTER TABLE enquiries
    ADD COLUMN city           VARCHAR(80)  NULL AFTER country,
    ADD COLUMN journey_stage  VARCHAR(200) NULL AFTER city,
    ADD COLUMN dates_flexible TINYINT(1) NOT NULL DEFAULT 0 AFTER travel_month;
