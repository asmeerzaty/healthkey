# HEALTHKEY — QR health bracelet prototype (PHP + MySQL)
Academic prototype. **Not a medical device.** The QR holds only a random token URL (`/scan.php?token=...`); no medical data.

## Setup (XAMPP / WAMP / Laragon)
1. Extract the `healthkey` folder into `htdocs` (or `www`).
2. Start Apache + MySQL.
3. phpMyAdmin → Import `database/healthkey.sql` (creates DB `healthkey` + demo data).
4. Edit `includes/config.php` if your DB user/password or folder name differ (`BASE_URL`).
5. Open http://localhost/healthkey/
No Composer needed: QR images are drawn in the browser with qrcode.js (CDN), so an internet connection is required for the QR image, Bootstrap and icons.

## Demo logins
- Admin: admin@healthkey.test / Admin123!
- User: user@healthkey.test / User123! (profile + active QR already set up)

## How to test
- **Register:** /register.php → login → fill Health Profile.
- **Purchase / reveal:** Bracelet page shows a hidden QR → Buy Now → demo checkout → My QR is revealed (download/print).
- **Claim physical tag:** log in as a new user → /claim-qr.php → enter `bbbbbbbb…` (64 × "b") → tag linked to you, active once your profile is complete. A second user cannot claim it again.
- **Scan (no login):** open `/scan.php?token=0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef` (or scan the QR with a phone on the same Wi-Fi using your PC's IP).
- **Admin:** /login.php as admin → dashboard, users (search/deactivate), patients, orders (set paid), QR codes (generate stock tags, deactivate → scan shows "inactive"), scan logs, messages (reply to "Contact Admin" chats).

## Security notes
PDO prepared statements, `password_hash`, CSRF tokens on all POST forms, escaped output, 256-bit random tokens, server-side ownership (QR/orders always looked up by session user), role checks on every admin page.
## Structure
`includes/` (config, db, auth, helpers, layout) · `admin/` · `assets/` · `database/healthkey.sql` · pages in root.
