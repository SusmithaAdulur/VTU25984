-- Migration: Add end_time support and rename event_time to start_time
-- Run this migration to upgrade your database

USE event_sync_db;

-- Step 1: Add end_time column (temporary)
ALTER TABLE events ADD COLUMN end_time TIME DEFAULT NULL;

-- Step 2: Rename event_time to start_time
ALTER TABLE events CHANGE COLUMN event_time start_time TIME NOT NULL;

-- Step 3: Set end_time to 1 hour after start_time for existing records
UPDATE events SET end_time = ADDTIME(start_time, '01:00:00') WHERE end_time IS NULL;

-- Step 4: Make end_time NOT NULL
ALTER TABLE events MODIFY COLUMN end_time TIME NOT NULL;

-- Final schema check (events table should now have):
-- id, event_name, event_date, start_time, end_time, status, created_at
--
-- Example table creation for reference:
-- CREATE TABLE events (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     event_name VARCHAR(255),
--     event_date DATE,
--     start_time TIME,
--     end_time TIME,
--     status VARCHAR(50),
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );
