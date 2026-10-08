-- Run ONCE in phpMyAdmin (database: healthkey) if you already imported the old healthkey.sql.
USE healthkey;
ALTER TABLE chat_messages
  ADD COLUMN channel ENUM('ai','admin') NOT NULL DEFAULT 'ai' AFTER sender,
  ADD COLUMN is_read TINYINT NOT NULL DEFAULT 1 AFTER message,
  ADD INDEX idx_user_channel (user_id,channel), ADD INDEX idx_channel_read (channel,is_read);
-- Old admin replies belong to the admin channel (older user->admin messages cannot be told apart and stay in the AI chat).
UPDATE chat_messages SET channel='admin' WHERE sender='admin';
