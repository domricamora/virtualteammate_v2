# Virtual Teammate — Marketing Site Developer Reference

*Architecture, conventions, and internals of the `vtnew` marketing site. For content-level
edits, see `marketing-site-guide.md`. For the staff portal, see the portal docs.*

---

## 1. Stack & serving

- **Language:** PHP 8.2 (server-rendered, no framework).
- **Server:** Apache, with the repository as the document root.
- **No build step.** Edit a file, save, refresh. PHP renders the shared includes server-side.
- **External runtime dependencies (CDN/remote):**
  - Font Awesome 6.5.2 (icons).
  - HubSpot (chat widget, meetings embed, forms/CRM API).
  - Manrope is self-hosted (woff2 in the site), not loaded from Google Fonts.

### Useful commands

Lint a single PHP file:

```
php -l includes/head.php
```

Full render to stdout (end-to-end smoke test):

```
php index.php > out.html
```

---

## 2. Project layout

```
.
├── index.php                  # homepage
├── 404.php / 403.php / 500.php # error pages (route via includes/error-page.php)
├── lead.php                   # form lead-capture endpoint (POST)
├── track.php                  # pageview beacon receiver
├── vt-link.php                # public VT profile viewer (token-gated)
├── talent-photo.php           # public talent photo server
├── talent-media.php           # public talent media (photo/resume/video) by token
├── installer.php              # forwards to portal/install.php
├── llms.txt                   # plain-text company summary for LLM crawlers
├── robots.txt / sitemap.xml   # crawl directives
├── .htaccess                  # Apache: index, security, gzip/brotli, cache, redirects
├── deploy.sh                  # FTP deploy (sources .ftp.local)
├── includes/                  # shared PHP partials (direct access denied)
├── css/style.css              # all styles
├── js/main.js                 # all behavior
├── images/                    # logos, photos, client/press logos, pdf
├── services/ business/ about/ careers/ case-studies/ contact/ guarantee/
├── healthcare-landing/ dental-landing/ business-landing/
├── terms/ privacy-policy/ virtual-teammates/
├── portal/                    # staff portal (separate app — see portal docs)
└── data/                      # runtime SQLite + media (web-blocked, not deployed)
```

---

## 3. Page composition pattern

A page sets SEO variables, includes the head and nav, writes its content inside `<main>`,
then includes the footer:

```php
<?php
$page_title       = 'About Virtual Teammate';
$page_description = '…';
$canonical        = 'https://virtualteammate.com/about/';
$is_homepage      = false;
$home_base        = '../';            // relative prefix from this page to site root
$breadcrumbs      = [
  ['name' => 'Home',  'url' => '/'],
  ['name' => 'About', 'url' => '/about/'],
];
include 'includes/head.php';          // (or '../includes/head.php' on inner pages)
include 'includes/nav.php';
?>
<main>
  <!-- page sections -->
</main>
<?php include 'includes/footer.php'; ?>
```

### `includes/head.php` parameters

| Variable | Type | Purpose |
|----------|------|---------|
| `$page_title` | string | `<title>` and default OG title |
| `$page_description` | string | meta description and default OG description |
| `$canonical` | string | absolute canonical URL |
| `$og_title`, `$og_description` | string | social overrides (default to the page vars) |
| `$is_homepage` | bool | when true, emits Organization + Service + FAQ + WebSite JSON-LD |
| `$breadcrumbs` | array | `[['name'=>…, 'url'=>…], …]` → BreadcrumbList JSON-LD on inner pages |
| `$faqs` | array | `[['q'=>…, 'a'=>…], …]` → FAQPage JSON-LD (ignored on the homepage) |
| `$robots` | string | override the default robots directive (e.g. `'noindex,nofollow'`) |
| `$body_class` | string | extra classes on `<body>` |
| `$home_base` | string | relative path prefix to site root (`./` on homepage, `../../` on `/services/<slug>/`) |

`head.php` also emits GA4 (production only), no-cache headers for HTML, and versioned CSS/JS
URLs via `vt_asset_ver()` (see §6).

---

## 4. `includes/` reference

| File | Role |
|------|------|
| `head.php` | `<head>`: meta, JSON-LD schema, fonts, versioned CSS/JS, GA4. Parameters above. |
| `nav.php` | Sticky topbar + nav, Healthcare mega-menu (medical + dental), mobile hamburger. |
| `footer.php` | Footer grid, scroll-to-top button (`#scrollTop`), CTA modals include, `main.js` load. Honors `$hide_footer`, `$home_base`. |
| `cta-modals.php` | Hub that includes the modal set on non-homepage pages (guards against double-render). |
| `book-modal.php` | `#cta-book` — HubSpot Meetings scheduler modal. |
| `jumpstart-modal.php` | `#cta-strategy-call` — strategy-call modal (reuses the meetings loader). |
| `checklist-modal.php` | `#cta-buyers-checklist` — email-capture modal posting to `lead.php`. |
| `request-modal.php` | `#cta-request` — generic request modal; wires modal scroll-lock/ESC/autofocus. |
| `lead-form.php` | Reusable lead band. Two modes: `'book'` (modal link) or `'form'` (full form). Customizable via `$lf_*` vars (title, sub, thanks, cta, placeholders, roles, source, form). Posts to `/lead.php`. |
| `force-ssl.php` | HTTPS-enforcement guard (included at the top of `head.php`). Toggled by `data/force_ssl.on`. Skips CLI requests; respects `X-Forwarded-Proto` / Cloudflare. |
| `cache.php` | Asset cache control: `vt_cache_state()`, `vt_asset_ver()`. See §6. |
| `hubspot-config.php` | Returns `hub_id`, `pipeline_id`, `forms` (intent→GUID), `form_fields` (intent→allowed fields). Used by `lead.php` and modals. |
| `hubspot-chat.php` | HubSpot live-chat embed (loaded by footer). |
| `hubspot-loader.php` | HubSpot Meetings embed loader (used by scheduler modals). |
| `error-page.php` | Shared error template (reads `$err_code`, `$err_eyebrow`, `$err_head`, `$err_msg`). |
| `service-cta.php`, `cta-stages.php`, `vt-cards.php` | Service-page CTA sections, funnel-stage definitions, VA profile card component. |

`includes/.htaccess` denies direct web access to the partials.

---

## 5. CSS architecture — `css/style.css`

Single stylesheet (~10k lines). Desktop rule first, then down-breakpoint overrides.

### Design tokens (CSS custom properties at the top)

Reuse these instead of hard-coding hex/rgba:

```
--gold #dfa949   --gold-dk #b8882e   --gold-lt #f5e4b8
--violet #8582c3 --violet-dk #3919ba --violet-deep #2a1290
--dark #1a1535   --body #3d3860      --white #ffffff
--glass-bg  rgba(255,255,255,.10)    --glass-bg2 rgba(255,255,255,.16)
--glass-border rgba(255,255,255,.28) --glass-border-strong rgba(255,255,255,.45)
--glass-blur blur(18px)              --glass-blur-sm blur(10px)
```

Manrope is self-hosted (variable 300–800, woff2). The page uses a dark violet→dark gradient
background; `body` is `max-width:1980px; width:100%; margin:0 auto` with horizontal overflow clipped.

### Class-prefix naming (scope per section)

`hero-*`, `spec-*` (specialties), `biz-*` (business), `roi-*`/`calc-*` (ROI + calculator),
`pstep-*`/`proc-*` (process), `test-*` (testimonials), `hw-*` (how we work), `prof-*` (profiles),
`faq-*`, `cf-*` (CTA form), `ft-*` (footer), `mq-*`/`marquee-*` (client marquee), `news-*` (press
marquee), `world-*`/`global-*` (world map), `hv-*` (hero visual collage), `svc-*` (service pages),
`nav-*`, `scroll-top`. Buttons: `.btn-primary`, `.btn-glass`.

### Responsive breakpoints

Two **down** breakpoints:

- `@media (max-width:1280px)` — tablet / small laptop (nav → hamburger, grids stack).
- `@media (max-width:768px)` — mobile.

**Ultra-wide / high-res:** at the very end of the file, a ladder of `@media (min-width:…)`
tiers steps `body { zoom: … }` from 1.11× up to a 2.6× cap (for displays up to ~6016×3384).
Because the layout is in px, `zoom` is the single lever that scales layout, type, and spacing
together while preserving tuned ratios. Content stays centered via `body{margin:0 auto}`;
the html gradient fills any gutter past the cap.

### Animation utilities

`.reveal` (fade-in + slide-up on scroll; stagger via `.d1`–`.d6`), keyframes `floatOrb`,
`orbPulse`, `shimmer`, `pulseGlow`, `marqueeLeft`, `spinSlow`. All wrapped by
`@media (prefers-reduced-motion: reduce)` which disables motion.

---

## 6. Asset versioning & caching — `includes/cache.php`

- `vt_cache_state()` reads `data/cache-state.php` (toggled from the portal's cache control).
- `vt_asset_ver()` returns `?v={filemtime}` when caching is **on** (stable, cache-friendly URLs
  that bust only when the file changes), or a unique per-load token when caching is **off**
  (forces fresh fetches during active development).

`head.php` appends `vt_asset_ver()` to the CSS and JS URLs so a deploy invalidates the
browser cache automatically.

---

## 7. JavaScript behavior — `js/main.js`

Single file (~1.4k lines), no framework. Behaviors:

1. **Nav / mega-menu** — `.nav-toggle` opens `#primaryNav`; `.nav-drop-trigger` accordion on
   mobile via `matchMedia('(max-width:1280px)')` (desktop dropdown is pure CSS hover). Closes on
   link follow, outside click, or breakpoint change.
2. **Scroll reveal** — `IntersectionObserver` on `.reveal` (threshold 0.12, rootMargin -8%) adds
   `.in`. Falls back to immediate reveal if unsupported; respects reduced-motion.
3. **Count-up** — `[data-count]` eases 0→target over ~1400ms (easeOutCubic); supports
   `data-decimals` and `data-suffix`. Hero stats replay periodically while visible.
4. **Client marquee** — track populated from `window.VT_MARQUEE` (filled server-side by a PHP glob
   of `images/clients/marquee/*`); the track is duplicated for seamless infinite scroll, paused on hover.
5. **Press marquee** — built from the hardcoded `press` array (name, `src` under `images/press/`,
   `href` to the release). Toned down by default, full color on hover.
6. **ROI calculator** — flat bi-weekly rates (VT full-time / part-time vs. US in-house full-time /
   part-time) as constants. Slider sets the count; buttons toggle the schedule. Recomputes annual /
   monthly / 3-year savings, savings %, per-VA savings, then tweens the numbers (token-cancelled to
   avoid overlap) and updates a donut gauge (stroke-dasharray) and a comparison bar chart.
7. **Scroll-to-top** — `#scrollTop` shows past 600px scroll, fades to 50% when idle, smooth-scrolls
   to top (respects reduced-motion).

CTA modals need no JS wiring — they open via anchor `href` + CSS `:target`; `data-cta-intent`
attributes are retained for analytics only.

---

## 8. Lead capture — `lead.php`

Single POST endpoint behind every form on the site (~480 lines).

**Flow:**

1. **Method guard** — 405 unless POST.
2. **Honeypot** — if `vt_hp` (or legacy `company_site`) is filled, return `{"ok":true}` without
   storing (bots think they succeeded).
3. **Field collection** — generic sweep of `$_POST` (skips control fields `vt_hp`, `company_site`,
   `_csrf`; truncates values to ~2000 chars).
4. **Field mapping** — name (`first_name`/`firstname` + `last_name`/`lastname`, or `name`/`full_name`),
   `email` (required, validated), phone, company (`company`/`practice`/`clinic`/`organization`/…),
   message (`message`/`notes`/`comments`), `source` (default `website`), `form`, `intent` (funnel
   stage: buyers-checklist, practice-audit, strategy-call, contact, careers, vt-request, roi),
   `vt_interest`.
5. **Persist** — insert into the `leads` table in `data/portal.sqlite` (table auto-created if
   missing): name, email, phone, company, message, source, form, vt_id, vt_interest, details (full
   dump), ip, created_at.
6. **Respond** — buffered JSON `{"ok":true}` / `{"ok":false,"error":…}`, returned immediately.
7. **After response** (via `fastcgi_finish_request()` or buffered background work):
   - **Team email** — branded HTML + text to the `lead_notify_email` app setting.
   - **HubSpot contact upsert** — PATCH/POST to the CRM contacts API by email, with custom
     properties (`lead_intent`, `lead_source_form`).
   - **HubSpot Forms submit** — POST to the public Forms API, mapping intent → form GUID (records a
     real form submission for HubSpot workflows).

**Mail transport:** reads `portal/smtp.local.php` (shared with the portal mailer) for a minimal
STARTTLS + AUTH LOGIN client; falls back to native `mail()`. **Validation:** email only; the
honeypot + server-side validation stand in for CSRF on these public forms.

This `leads` table is the **same table** read by the portal's **Leads** page — see the portal docs.

---

## 9. Other endpoints & files

- **`track.php`** — pageview beacon. Logs path, IP, geo (via ip-api.com, cached per IP in
  `geo_cache`), user agent, referrer into the `traffic` table; returns a non-blocking 204.
- **`vt-link.php` / `talent-photo.php` / `talent-media.php`** — public, token-gated views of VT
  talent profiles, photos, resumes, and videos (validated against `vt_special_links`); `talent-photo`
  serves only `vt_hired`/`vt_onpool` photos with a placeholder fallback.
- **`llms.txt`** — plain-text summary of the company (facts, services, pages, FAQs) for LLM crawlers.
- **`.htaccess`** — `DirectoryIndex index.php`; custom 403/404/500; denies sensitive files
  (`.ftp.local`, `.env*`, `deploy.sh`, `smtp.local.php`, `*.md/.sql/.sqlite*/.log/.local/.bak/.ini/.sh`);
  301s `/index.php` and `/index.html` → `/`; `Options -Indexes`/`-MultiViews`; gzip + brotli;
  cache (HTML no-cache, CSS/JS/img/fonts 1yr immutable, PDF 1mo); security headers (nosniff,
  SAMEORIGIN, Referrer-Policy, Permissions-Policy).
- **`robots.txt`** — allows general + named AI crawlers; disallows `/includes/`, `/portal/`,
  `/data/`; points to the sitemap.
- **`sitemap.xml`** — homepage (1.0), services (0.9), marketing pages (0.8), legal (0.3).

---

## 10. Deployment

`deploy.sh` uploads over **plain FTP** (port 21 — FTPS fails on a cert principal mismatch) using
credentials from the gitignored `.ftp.local` (`FTP_USER`, `FTP_PASS`, `FTP_HOST`).

- **Ships:** the top-level production PHP files (index, error pages, `lead.php`, `track.php`,
  talent endpoints, installer), `.htaccess`, `robots.txt`, `llms.txt`, `sitemap.xml`,
  `favicon.ico`, and the production directories (`css`, `js`, `images`, `fonts`, `includes`,
  `services`, `business`, landing pages, `about`, `careers`, `case-studies`, `contact`,
  `guarantee`, `virtual-teammates`, `terms`, `privacy-policy`, `portal`, `hubapi`).
- **Selectively ships from `data/`:** only `data/.htaccess` and `data/.gitkeep` — never the
  SQLite DB, sync state, credentials, or media.
- **Never ships:** `.git/`, `.ftp.local`, `*.md` (so `docs/` stays internal), `.gitignore`,
  `deploy.sh` itself, `portal/smtp.local.php`.
- **Parallelism:** `FTP_PARALLEL` (default 8). `curl --ftp-create-dirs` handles directory creation.

**Triggers:** runs automatically on `git push` via `.git/hooks/pre-push` (non-blocking — the push
proceeds even if FTP fails). Manual deploy without pushing: `./deploy.sh`. Push without deploying:
`git push --no-verify`.
