-- Run ONCE in phpMyAdmin (SQL tab, database: healthkey). Adds the "seen at" time.
ALTER TABLE chat_messages ADD COLUMN read_at DATETIME NULL AFTER is_read;
