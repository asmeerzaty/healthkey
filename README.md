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

## AI chatbot setup (free, Google Gemini)
The assistant calls Google's Gemini API from the PHP server (`includes/ai.php`, used by `chat-api.php`). Without a key it falls back to a basic rule-based bot.
1. Go to https://aistudio.google.com/apikey and create a **free** API key (Google account, no card).
2. Paste it into `includes/config.php` -> `GEMINI_API_KEY`. Never share or commit it.
3. Make sure PHP's `curl` extension is enabled (XAMPP: `extension=curl` in php.ini, restart Apache).
4. Optional: change `AI_MODEL` if Google renames/retires the model.
Free-tier notes: rate limits are low (a few requests per minute) and Google may use free-tier inputs to improve its products, so do not put real patient data through it. Availability can depend on your region.
## Stories
Stories live in `includes/content.php` (illustrative, not real testimonials) and render at `/stories.php` and `/story.php?s=slug`.

## Secrets & GitHub
Never put your API key or DB password in `includes/config.php` (it is committed). Copy `includes/config.local.example.php` to `includes/config.local.php` and put secrets there; `.gitignore` keeps it out of git. If a key was ever pushed, revoke it in AI Studio and create a new one — deleting the file later does not remove it from git history. The demo accounts in `healthkey.sql` are for testing only: change/remove them before any real deployment.

## CRUD overview
| Entity | Create | Read | Update | Delete |
|---|---|---|---|---|
| Users | register / admin "Add user" | admin list, view, search | account settings / admin Edit | user "Delete account" / admin delete |
| Health profile | health-profile.php | profile, scan page, admin | edit & save | "Delete my health data" |
| Appointments | appointments.php | user list, admin list | edit, mark done | delete |
| Orders | checkout | user, admin | admin set status | admin delete |
| QR codes | purchase / claim / admin generate | my-qr, admin list | admin disable/enable | admin delete |
| Scan logs | automatic on scan | admin list | - | admin delete / clear all |
| Chats | send message | AI + admin chats | - | clear AI chat / admin delete thread |
Run `database/migration_appointments.sql` once if you imported an older database.

## Roles
| Role | Login (demo) | Can do |
|---|---|---|
| Customer (user) | user@healthkey.test / User123! | profile, buy, QR, appointments, AI chat, contact admin |
| Admin | admin@healthkey.test / Admin123! | handle customers: users, orders, QR, scans, appointments, customer chat; chat with owner |
| **Owner (superadmin)** | owner@healthkey.test / Owner123! | everything admins can (except customer chat) PLUS bracelet catalog CRUD + stock, create/edit/delete admins, private chat with admins |
The owner never talks to customers; admins do. Only the owner can create/edit/delete admin accounts. Run `database/migration_superadmin.sql` once if you imported an older database.
