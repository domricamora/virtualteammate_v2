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

## 10. PHP function reference

Every server-side function defined in the marketing site (the portal has its own). Most pages
are procedural templates; the functions below are the reusable logic. Several endpoints
(`vt-link.php`, `talent-media.php`, the page templates) are intentionally procedural and define
no named functions.

### `includes/cache.php`

| Function | Purpose |
|----------|---------|
| `vt_cache_state(): array` | Reads `data/cache-state.php` and returns the asset-cache on/off state (toggled from the portal's cache control). |
| `vt_asset_ver(string $absPath): string` | Returns the `?v=` cache-busting suffix for an asset URL: `filemtime` when caching is **on** (stable, busts on change), a unique per-load token when **off** (forces fresh fetches in development). |

### `index.php`

| Function | Purpose |
|----------|---------|
| `vtnew_homepage_profiles(int $limit = 6): array` | Queries `data/portal.sqlite` for active medical/dental VT profiles that have a photo on disk, tags each Medical/Dental, and returns up to `$limit` rows for the homepage "Meet the Team" grid. Returns `[]` if the portal DB isn't present, so the page still renders. |

### `lead.php` (the lead-capture endpoint)

| Function | Purpose |
|----------|---------|
| `lead_respond(array $payload, int $code = 200): void` | Discards any output buffer, sets the status, emits the final JSON, and exits. Every response routes through here so stray output can't corrupt the JSON body. |
| `lead_fail(string $msg, int $code = 400): void` | Shorthand for an error response — `{"ok":false,"error":$msg}`. |
| `lead_notify_team(PDO $pdo, array $lead): void` | Best-effort branded team-notification email sent **after** the response. Recipient from `app_settings.lead_notify_email` (default `nricamora@virtualteammate.com`). Skipped on localhost only when no SMTP relay is configured. |
| `lead_smtp_config(): ?array` | Loads and statically caches outbound SMTP credentials from `portal/smtp.local.php`; returns the config array or `null` (→ fall back to native `mail()`). Never throws. |
| `lead_smtp_send(array $cfg, string $from, string $to, string $message): bool` | Minimal STARTTLS + AUTH LOGIN SMTP client; returns `true` only when the server accepts the message (250 at end-of-DATA). |
| `lead_send_mail(string $to, string $subject, string $html, string $text): bool` | Builds a MIME multipart (HTML + text) message and sends it via the SMTP relay when configured, else native `mail()`. |
| `lead_email_html(array $lead): string` | Renders the branded HTML body for the team-notification email. |
| `lead_push_hubspot(PDO $pdo, array $lead): void` | Upserts the lead as a HubSpot CRM contact by email (PATCH then POST), setting custom properties (`lead_intent`, `lead_source_form`). |
| `lead_submit_hubspot_form(array $lead): void` | POSTs a real submission to the HubSpot Forms API, mapping the funnel `intent` → form GUID (so HubSpot workflows fire). |
| `lead_hubspot_call(string $method, string $url, string $token, string $body): array` | Thin cURL wrapper for HubSpot API calls; returns `[status, body]`. |

### `track.php` (pageview beacon)

| Function | Purpose |
|----------|---------|
| `track_done(): void` | Sends `204 No Content` and exits. |
| `track_client_ip(): string` | Best-effort client IP, honoring `CF-Connecting-IP`, `X-Forwarded-For` (first hop), `X-Real-IP`, then `REMOTE_ADDR`. |
| `track_geo(PDO $pdo, string $ip): array` | Resolves `[country, region, city]` from the `geo_cache` table, falling back to ip-api.com (then cached per IP). Private/loopback ranges are tagged `Local`. |

### `talent-photo.php` (public VT photo server)

| Function | Purpose |
|----------|---------|
| `tp_serve_placeholder(): void` | Serves `images/photos/placeholder-avatar.svg` (1-day cache) and exits — the graceful avatar fallback whenever a real photo can't be returned. |

---

## 11. Client-side (JavaScript) function reference

Beyond the global behaviors in `js/main.js` (§7), several pages and includes carry their own
inline `<script>` modules. These are the named functions, grouped by module.

### Homepage CTA modals & lead forms — `index.php` (inline)

| Function | Purpose |
|----------|---------|
| `resetForms()` / `lock()` / `unlock()` / `sync()` | The modal state machine: lock/restore page scroll and sync open/closed state to the URL hash. |
| `fillRequest(a)` | Populates the "Request a teammate" modal from a clicked profile card's `data-vt-*` attributes. |
| `postLead(form)` | AJAX-submits a lead form to `lead.php` and swaps in the thank-you state. |
| `resetBtn()` | Restores a submit button after a request completes or fails. |
| `handleCsmCallback(e)` / `handleChecklist(e)` / `handlePracticeAudit(e)` / `handleStrategyCall(e)` / `handleRequest(e)` | Per-form submit handlers for each CTA intent. |
| `attach(id, handler)` | Wires a handler to a form by id. `init()` boots all the above on load. |

### Reusable request modal — `includes/request-modal.php`

| Function | Purpose |
|----------|---------|
| `fillRequest(a)`, `postLead(form)`, `resetBtn()`, `resetForms()`, `lock()`, `unlock()`, `sync()` | The non-homepage version of the request-modal + lead logic above. |
| `bindForm()` / `initBehavior()` | Bind the form's submit handler and wire scroll-lock / ESC / autofocus behavior. |

### Modal embed loaders

| Function | Module | Purpose |
|----------|--------|---------|
| `createForm()` / `loadEmbed()` / `maybeHash()` | `includes/checklist-modal.php` | Lazily build and inject the HubSpot checklist form embed when the modal opens (or on a deep-link hash). |
| `loadHS()` | `includes/hubspot-loader.php` | Inject the HubSpot Meetings embed script on demand (used by the booking modals). |
| `loadLeadDyno()` | `includes/leaddyno.php` | Load the LeadDyno referral-tracking script. |
| `vtTrack()` | `includes/footer.php` | Fire the post-load pageview beacon to `track.php`. |
| `gtag()` | `includes/head.php` | Standard GA4 command stub (production only). |

### Business page CTAs — `business/index.php`

| Function | Purpose |
|----------|---------|
| `resetForms()` / `lock()` / `unlock()` / `sync()` | Modal scroll-lock + hash state for the operational-assessment, buy-back, and request modals. |

### VA card filtering — `includes/vt-cards.php` (business "Meet the Bench")

| Function | Purpose |
|----------|---------|
| `norm(s)` | Normalize a string for case-insensitive search. |
| `matches(card)` | Test one card against the current search + department + skill filters. |
| `apply()` | Re-filter and re-render the visible card set. |
| `populateSkills()` | Repopulate the skill dropdown for the selected department. |

### Talent directory — `virtual-teammates/index.php`

| Function | Purpose |
|----------|---------|
| `esc(s)` | HTML-escape a string before injecting it into the modal. |
| `matches(card)` / `apply()` / `populateSkills()` | Search + department/skill filtering of the bench grid. |
| `driveId(u)` | Extract a Google Drive file id from a share URL. |
| `videoEmbed(url, poster)` | Build the right intro-video embed (Drive / YouTube / Vimeo / hosted `<video>`). |
| `ctaBlock(id, full, fn)` | Render the modal's CTA depending on auth state (member request vs. lead-form scroll). |
| `submitRequest(btn)` | POST a logged-in client's "request this teammate" action. |
| `openModal(card)` / `closeModal()` | Open the profile modal (full CV for members, teaser for anonymous) / close it. |
| `resetBtn()` | Restore the request button after submit. |

### Landing-page quiz engine — `healthcare-landing/`, `dental-landing/`, `business-landing/`

Each landing page carries the same quiz module (question counts differ — 6/6/8):

| Function | Purpose |
|----------|---------|
| `loadQuestion()` | Render the current quiz question and advance the progress bar. |
| `showResults()` | Compute and display the efficiency tier, estimated hours saved, and revenue impact. |
| `generatePDF(…)` | Build the downloadable results report (args vary per page: `tier, hoursSaved, revenuePotential` on business; `hoursSaved, revenuePotential` on healthcare/dental). |
| `restartQuiz()` | Reset the quiz to the first question. |
| `submitToHubSpot(email, phone, leadsource)` | POST the captured contact + quiz outcome to the HubSpot Forms API. |
| `submitToLeadDB(email, phone, …)` | POST the same to the site's own `lead.php` (dual capture). |

---

## 12. Deployment

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
