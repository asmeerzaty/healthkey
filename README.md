# HEALTHKEY: QR + NFC health bracelet prototype (PHP + MySQL)

Academic prototype. **Not a medical device.**
Each bracelet carries a QR code (and optionally an NFC tag). The QR holds only a random token URL (`/scan.php?token=...`) and **no medical data**. Anyone who scans it, with no login, sees the owner's emergency page. The owner can switch it off at any time.

Live demo: https://healthkey.infinityfreeapp.com/

---

## Features

| Area | What it does |
|---|---|
| Accounts | Register, login, account settings, delete account, delete my health data |
| Health profile | Blood type, allergies, conditions, medication, emergency contact, doctor |
| Bracelet shop | Product page, hidden QR until purchase, demo checkout, QR revealed after purchase |
| My QR | Download/print the QR, **pause / resume**, **create a new QR** (the old one stops working immediately), and a history of recent scans |
| Claim a tag | Link a pre-made physical tag to your account (`claim-qr.php`) |
| Scan page | Public emergency page by token. Paused or inactive tags show as "inactive". Every scan is logged |
| NFC tag text | `nfc.php` builds a short emergency text (and optionally the live scan link) to copy into a free NFC app. It tracks the profile and warns when the tag text is out of date |
| Appointments | Add, edit, mark done, delete |
| AI assistant | Gemini-powered health-information assistant. Falls back to a basic rule-based bot if no key or API is unavailable |
| Contact Admin | Customer chat with admins |
| Stories | Illustrative (not real testimonials), at `/stories.php` and `/story.php?s=slug` |
| Admin panel | Users, patients, orders, QR codes (generate stock tags, deactivate), scan logs, appointments, customer messages, stories |
| Owner panel | Everything admins can do (except customer chat) plus bracelet catalogue and stock, create/edit/delete admins, private staff chat |

---

## Roles

| Role | Demo login | Can do |
|---|---|---|
| Customer | `user@healthkey.test` / `User123!` | Profile, buy, QR/NFC, appointments, AI chat, contact admin |
| Admin | `admin@healthkey.test` / `Admin123!` | Handle customers: users, orders, QR, scans, appointments, customer chat. Chat with the owner |
| Owner (superadmin) | see `database/healthkey.sql` | All of the above except customer chat, plus bracelet CRUD and stock, and admin management |

The owner never talks to customers; admins do. Only the owner can create, edit or delete admin accounts.

> **Change every demo password before sharing the site publicly.**

---

## Setup A: Local (XAMPP / WAMP / Laragon)

1. Extract the `healthkey` folder into `htdocs` (or `www`).
2. Start Apache and MySQL.
3. phpMyAdmin: import `database/healthkey.sql` (creates DB `healthkey` with demo data).
4. Check `includes/config.php`: DB user/password, and `BASE_URL` = `'/healthkey'` (the folder name).
5. Open http://localhost/healthkey/

QR images, Bootstrap and icons load from CDNs, so an internet connection is needed.

## Setup B: InfinityFree (free hosting)

1. **Upload the files into `htdocs/` directly**, so `htdocs/index.php` and `htdocs/includes/` exist (not `htdocs/healthkey/`).
2. In the InfinityFree panel create a MySQL database and note host, name, user and password.
3. phpMyAdmin: import `database/healthkey.sql` into that database. Remove any `CREATE DATABASE` / `USE healthkey` lines first.
4. Put the server settings in `includes/config.local.php` (preferred) or `includes/config.php`:

```php
<?php
define('DB_HOST', 'sqlXXX.infinityfree.com');
define('DB_NAME', 'if0_XXXXXXXX_healthkey');
define('DB_USER', 'if0_XXXXXXXX');
define('DB_PASS', 'your-password');
define('BASE_URL', '');                 // site is at the domain root, NOT /healthkey
define('GEMINI_API_KEY', 'your-key');
```

5. Delete `check.php` from the server when you are finished debugging.

### InfinityFree notes (problems we hit)

| Symptom | Cause and fix |
|---|---|
| Register / Continue give **404** | `BASE_URL` was `/healthkey`. Set it to `''` on the server |
| A page or script gives **403 Forbidden** | InfinityFree's filters block some file names (ones containing "chat" or "bot"). Files were renamed: `chatbot.php` to `assistant.php`, `assets/js/chat.js` to `assets/js/msg.js`, `chat-api.php` to `msg-api.php`. Use names like these for any new file |
| Message disappears when sent | The chat script or API file was blocked (403). Fixed by the renames above |
| Config error about an undefined constant | `AI_FALLBACK_MODEL` and `AI_DEBUG` must exist in `config.php` (see below) |
| QR image blank | The qrcode.js CDN is blocked or slow. Hard-refresh and disable ad blockers |
| Uploaded images missing | Check the file really exists in `assets/img/...` and has permission 644 (folders 755). File names are case-sensitive |
| AI says it is unavailable | Free hosting can block outgoing calls to Google. Add a valid key, or the basic bot is used |

---

## Configuration reference (`includes/config.php`)

Defaults live in `config.php`. Anything defined first in `config.local.php` wins. Keep `config.local.php` out of git.

| Setting | Default | Purpose |
|---|---|---|
| `DB_HOST` / `DB_NAME` / `DB_USER` / `DB_PASS` | (set yours) | Database connection |
| `BASE_URL` | `/healthkey` locally, `''` on InfinityFree | Prefix for all links |
| `PRODUCT_NAME` / `PRODUCT_PRICE` | HEALTHKEY Bracelet / 29.90 | Shop defaults |
| `GEMINI_API_KEY` | empty | Enables the AI assistant |
| `AI_MODEL` | `gemini-2.5-flash` | Main model |
| `AI_FALLBACK_MODEL` | `gemini-2.5-flash-lite` | Used if the main model fails |
| `AI_HOURLY_LIMIT` | `30` | Messages per user per hour |
| `AI_DEBUG` | `false` | `true` shows the real AI error in chat while setting up |

---

## AI assistant setup (free, Google Gemini)

The PHP server calls Gemini (`includes/ai.php`, used by `msg-api.php`). Without a key it falls back to a basic rule-based bot.

1. Create a **free** key at https://aistudio.google.com/apikey.
2. Put it in `includes/config.local.php` as `GEMINI_API_KEY`. **Never commit or share it.**
3. Locally, make sure PHP's `curl` extension is enabled, then restart Apache.
4. If Google retires a model, change `AI_MODEL` / `AI_FALLBACK_MODEL`.

Free-tier notes: limits are low, and Google may use free-tier inputs to improve its products. **Do not send real patient data.** Availability depends on region and on the host allowing outgoing requests.

---

## NFC tag

1. Complete your health profile.
2. Open **NFC tag text** (`nfc.php`), pick what to include, and copy the text.
3. Write it to your tag with a free NFC app. To open the live page instead, write the scan link as a **URL record**.
4. Press "I've written it". HEALTHKEY then warns you if your profile or QR link changes and the tag needs rewriting.

A written NFC tag cannot be switched off remotely. A paused or replaced QR link stops working, but static NFC text stays as written.

---

## How to test

- **Register:** `/register.php`, login, fill the Health Profile.
- **Purchase / reveal:** Bracelet page (QR hidden), Buy Now, demo checkout, My QR is revealed.
- **Claim a physical tag:** log in as a new user, go to `/claim-qr.php`, enter `bbbbbbbb...` (64 times "b"). A second user cannot claim it again.
- **Scan (no login):** `/scan.php?token=0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef`, or scan a QR with a phone.
- **Pause / replace:** My QR, pause or create a new QR, then scan the old one and see "inactive".
- **Admin:** log in as admin, check the dashboard, users, orders, QR codes, scan logs, messages.

---

## Security notes

PDO prepared statements, `password_hash`, CSRF tokens on all POST forms, escaped output, 256-bit random tokens, server-side ownership checks (QR and orders always looked up by session user), role checks on every admin page, and a `.htaccess` in upload folders that blocks script execution. Secrets belong in `config.local.php`, not in `config.php`.

---

## Structure

```
healthkey/
├── index.php, login.php, register.php, dashboard.php ...   pages
├── assistant.php, support.php, msg-api.php                 AI chat, admin chat, chat API
├── my-qr.php, claim-qr.php, scan.php, nfc.php              QR / NFC
├── includes/   config, config.local, database, auth, functions, ai, content, header, footer
├── admin/      admin and owner pages
├── assets/     css, js (msg.js ...), img
└── database/   healthkey.sql + migrations
```

## CRUD overview

| Entity | Create | Read | Update | Delete |
|---|---|---|---|---|
| Users | register / admin "Add user" | admin list, view, search | account settings / admin Edit | "Delete account" / admin delete |
| Health profile | health-profile.php | profile, scan page, admin | edit and save | "Delete my health data" |
| Appointments | appointments.php | user list, admin list | edit, mark done | delete |
| Orders | checkout | user, admin | admin set status | admin delete |
| QR codes | purchase / claim / admin generate | my-qr, admin list | pause, resume, replace / admin disable, enable | admin delete |
| Scan logs | automatic on scan | admin list | none | admin delete / clear all |
| Chats | send message | AI + admin chats | none | clear AI chat / admin delete thread |
