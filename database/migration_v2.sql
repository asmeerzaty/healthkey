-- HEALTHKEY v2 — run ONCE in phpMyAdmin (SQL tab, database: healthkey).
USE healthkey;
-- 1) Bracelet photos uploaded by the owner
ALTER TABLE bracelets ADD COLUMN image VARCHAR(255) NULL AFTER description;
-- 2) NFC tag tracking (what was last written to the user's tag)
ALTER TABLE patient_profiles
  ADD COLUMN nfc_opts VARCHAR(120) NULL,
  ADD COLUMN nfc_hash CHAR(32) NULL,
  ADD COLUMN nfc_written_at DATETIME NULL;
-- 3) Users can pause their own QR
ALTER TABLE qr_codes MODIFY status ENUM('unclaimed','claimed','active','disabled','paused') DEFAULT 'unclaimed';
-- 4) Consent + login throttling
ALTER TABLE users ADD COLUMN consent_at DATETIME NULL;
CREATE TABLE IF NOT EXISTS login_attempts(id INT AUTO_INCREMENT PRIMARY KEY,ip VARCHAR(45) NOT NULL,email VARCHAR(150) NOT NULL,attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(ip,attempted_at),INDEX(email,attempted_at));
