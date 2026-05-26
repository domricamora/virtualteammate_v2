# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A PHP marketing site for **Virtual Teammate** — a staffing agency for medical, dental, and business virtual assistants. Currently one page ([index.php](index.php)) but structured for multi-page expansion via shared `includes/` partials, a single stylesheet, and a single JS file.

## Serving / previewing

Lives inside a WAMP webroot (`c:\wamp64\www\vtnew`), served by Apache at:

```
http://localhost/vtnew/
```

To preview a change, save the file and refresh. No build step. PHP renders the includes server-side.

PHP lint a single file:

```
"c:/wamp64/bin/php/php8.2.26/php.exe" -l includes/head.php
```

Full render to stdout (for end-to-end smoke tests):

```
"c:/wamp64/bin/php/php8.2.26/php.exe" index.php > /tmp/out.html
```

External runtime dependencies (CDN):
- Google Fonts: Manrope (all weights)
- Font Awesome 6.5.2
- Unsplash images for hero collage, testimonials, and VA profile photos (specialty cards now use local `images/photos/`)

## Project layout

```
.
├── index.php                # homepage — sets SEO vars, includes head/nav/footer
├── includes/                # shared PHP partials (denied direct access via .htaccess)
│   ├── head.php             # meta, schema (Org/Service/FAQ/WebSite/Breadcrumb), CSS link
│   ├── nav.php              # topbar + main nav (anchors resolve to homepage)
│   └── footer.php           # footer, scroll-to-top button, main.js load
├── css/style.css            # all styles, including responsive @media blocks
├── js/main.js               # all behavior (reveal, ROI calc, marquees, scroll-top)
├── images/
│   ├── logo.webp, logo-sm.webp
│   ├── clients/             # client logo PNGs (loaded by client marquee JS)
│   ├── press/               # press logo PNGs (loaded by news marquee JS)
│   └── photos/              # specialty card photos (medical-assistant, dental-assistant)
├── .htaccess                # DirectoryIndex, gzip, cache headers, security headers, redirects
├── robots.txt               # allows all; disallows /includes/; points to sitemap
├── sitemap.xml              # currently only the homepage
├── deploy.sh                # FTP deploy script (sources .ftp.local for creds)
└── .ftp.local               # gitignored FTP credentials (never commit)
```

## Architecture you need to know before editing

### Page composition pattern

A page sets SEO variables, includes head + nav, writes its content inside `<main>`, then includes footer:

```php
<?php
$page_title       = '...';
$page_description = '...';
$canonical        = 'https://virtualteammate.com/about/';
$is_homepage      = false;
$breadcrumbs      = [
  ['name' => 'Home',  'url' => '/'],
  ['name' => 'About', 'url' => '/about/'],
];
include 'includes/head.php';
include 'includes/nav.php';
?>
<main>
  <!-- page sections -->
</main>
<?php include 'includes/footer.php'; ?>
```

Available `head.php` parameters:
- `$page_title`, `$page_description` — primary SEO
- `$canonical`, `$og_title`, `$og_description` — social/canonical overrides
- `$is_homepage` — emits Org + Service + FAQ + WebSite JSON-LD only on homepage
- `$breadcrumbs` — emits BreadcrumbList JSON-LD on inner pages
- `$robots` — override default index directive (e.g. `'noindex,nofollow'`)
- `$body_class` — extra classes on `<body>`

### Responsive layout (fluid up to 1980px)

`body` uses `max-width:1980px; width:100%`. Two breakpoints in [css/style.css](css/style.css):
- `@media (max-width:1280px)` — tablet/small-laptop overrides
- `@media (max-width:768px)`  — mobile overrides

Always write the desktop rule first, then add the breakpoint override.

### Design tokens

Colors and glass-morphism are CSS custom properties at the top of [css/style.css](css/style.css) (`--gold`, `--violet-dk`, `--glass-bg`, `--glass-blur`, etc.). Reuse them when introducing new elements instead of hard-coding rgba/hex.

### Naming conventions

Class prefixes scope rules to a section: `hero-*`, `spec-*` (specialties), `biz-*` (business), `roi-*`, `pstep-*`/`proc-*` (process), `test-*` (testimonials), `hw-*` (how we work), `prof-*` (profiles), `faq-*`, `cf-*` (CTA form), `ft-*` (footer), `calc-*` (ROI calculator), `mq-*` (client marquee), `news-*` (press marquee), `world-*`/`global-*` (world map), `hv-*` (hero visual collage), `scroll-top`.

### Page order on the homepage (top → bottom)

`topbar → nav → hero (+ stats) → ROI calculator → client marquee → global network (world map) → news/press marquee → specialties (split-layout, medical + dental) → ROI stats → process → testimonials → how we work → VA profiles → FAQ → business strip → CTA form → footer → scroll-top button`

### Marquee data lives in JS

Client and press logos are populated dynamically by [js/main.js](js/main.js) — see the `clients` and `press` arrays. Image paths are `images/clients/<slug>.png` and `images/press/<slug>.png`. To add or remove logos, edit those arrays.

## Deployment

`deploy.sh` uploads via plain FTP using credentials in `.ftp.local`. Runs automatically on `git push` via [.git/hooks/pre-push](.git/hooks/pre-push) (non-blocking — push proceeds even if FTP fails).

Uploads: `index.php`, `.htaccess`, `robots.txt`, `sitemap.xml`, `favicon.ico` (if present) + `css/`, `js/`, `images/`, `includes/`.

Skips: `.git/`, `.ftp.local`, `*.md`, `.gitignore`, `deploy.sh` itself.

To deploy manually without pushing: `./deploy.sh`. To push without deploying (rarely needed): `git push --no-verify`.

## Sibling projects in `c:\wamp64\www\`

`vt`, `vtsite`, `vt_saas`, `vtadmin`, `staging`, `staging_virtualteammate` are separate apps under the same webroot. They are **not** part of this project — don't read or edit them when working on `vtnew` unless the user asks.
