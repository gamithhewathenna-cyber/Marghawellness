# MĀRGHĀ Wellness — Website (Phase 1: Homepage)

PHP 8.0+ · MySQL 5.7+/MariaDB 10.3+ · Apache (cPanel) · no frameworks, no build step.

## Deploy on cPanel

1. **Upload** — cPanel › File Manager › `public_html` › Upload `margha-website.zip` › Extract.
   Move the contents of the extracted `margha/` folder into `public_html/` (or keep it in a
   sub-folder; links auto-detect the base path — then also set `RewriteBase` in `.htaccess`).
   Make sure hidden files are shown so `.htaccess` is copied.
2. **Database** — cPanel › MySQL® Databases: create a database and a user, add the user to the
   database with *All Privileges*.
3. **Import** — cPanel › phpMyAdmin › select the database › Import › `database/schema.sql`.
4. **Configure** — edit `config/config.php`: `DB_NAME`, `DB_USER`, `DB_PASS`, contact email and
   social links.
5. **PHP version** — cPanel › MultiPHP Manager: PHP 8.0 or newer (8.2+ recommended).
6. **HTTPS** — once AutoSSL is active, uncomment the *Force HTTPS* lines in `.htaccess`.

The homepage renders even before step 3 — it falls back to the built-in content in
`includes/content.php` — but the newsletter form needs the database.

## Structure

```
index.php               Homepage (assembles the section partials in order)
page.php                Front controller for all other routes (/journeys, /retreats …)
.htaccess               Clean URLs, security headers, caching, folder protection
api/subscribe.php       Newsletter endpoint (CSRF, honeypot, rate limit, PDO)
admin/                  Admin panel (login, page text, listings, enquiries, subscribers, users)
includes/settings.php   Editable page text: schema + defaults, setting() helper
config/config.php       DB credentials, contact + social settings
config/navigation.php   Every route + header/footer menus (single source of truth)
includes/               Bootstrap, PDO wrapper, helpers, content repository
partials/header.php     <head>, header + main nav
partials/footer.php     Newsletter, footer links, scripts
partials/home/*.php     One file per homepage section
pages/                  (create) /pages/{slug}.php to build a new page
assets/css/fonts.css    Self-hosted Cormorant Garamond, Jost, Quicksand, Lato (woff2)
assets/css/style.css    All styles — tokens at the top, responsive rules at the bottom
assets/js/main.js       Header, mobile menu, hero slider, filters, carousels, form (no deps)
database/schema.sql     Tables: subscribers, enquiries, hero_slides, retreats, journeys, posts
```

## Adding the next pages

Every menu link already resolves: `/journeys`, `/retreats`, `/journal`, `/news`, `/our-story`,
`/enquire`, etc. show a branded "coming soon" page. To build one, create
`pages/{slug}.php`, e.g. `pages/journeys.php`:

```php
<?php
$body_class = 'page-journeys';
partial('header', compact('page_title', 'body_class'));
// … your sections (reuse classes from style.css) …
partial('footer');
```

New routes are added in `config/navigation.php` (`PAGES`).

## Content

Hero slides, retreats, journeys (with path filter: rest / soul / healing) and journal posts
are read from MySQL and edited in the admin panel (below). Images are referenced by filename
in `assets/images/`; uploads from the admin go to `assets/images/uploads/`. Adding a second
hero slide activates the slider arrows automatically.

## Admin panel — `/admin/`

1. Import `database/admin.sql` in phpMyAdmin (already part of `schema.sql` for new installs).
2. Open `https://your-domain/admin/` — the first visit shows **setup**: create the first admin
   account. Setup locks itself as soon as one account exists, so do this right after upload.
3. Sign in at `https://your-domain/admin/login.php`.

| Screen                     | What it edits                                                     |
|----------------------------|-------------------------------------------------------------------|
| Pages › Home               | Every homepage section in page order: text, headings, images, and the cards shown in that section (hero slides, retreats, journeys, posts) |
| Pages › Header & footer    | Footer newsletter text, contact email, social links, default SEO  |
| Listings                   | Full lists of retreats, journeys and journal posts                |
| Enquiries / Subscribers    | Read, change status, search, download CSV                         |
| Maintenance mode           | Switch the public site to a "back soon" page                      |
| Admin users                | Add/remove admins, change your password                           |

How it fits together:
- `admin/_inc/pages.php` — the Pages menu: each website page and its sections, in order. A
  section points to a settings group (text/images) and/or a listing (cards). **To add a new
  page to the admin** (e.g. Journeys), add an entry here.
- `includes/settings.php` — the editable text fields and their defaults. A value saved in the
  admin overrides the default; "Reset to original" removes the override. Output a field with
  `setting('key')` — `e()` for plain text, `rich()` for `<em>/<strong>/<br>`, `paras()` for
  multi-paragraph text.
- `admin/_inc/entities.php` — listing types (tables) and their form fields.

Security: bcrypt passwords, CSRF on every form, 5 failed logins per IP → 15‑minute lock, 2‑hour
idle sign-out, uploads checked by content type and renamed, scripts blocked in the uploads folder.

## Images

The images in `assets/images/` were cut from the approved design file, so they are at the
design's screen resolution (1439px wide). Replace them with the original high-resolution photos
(same filenames, ~2400px wide for hero/CTA, JPEG quality ~80) before launch.
