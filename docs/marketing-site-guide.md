# Virtual Teammate — Marketing Site Guide

*A non-technical guide to the pages, content, and everyday edits on the Virtual Teammate marketing site.*

---

## 1. What this is

The marketing site for **Virtual Teammate** — a staffing agency for medical, dental, and
business virtual assistants. It is a PHP-rendered website with no separate "build" step:
files are edited, saved, and the change is live on refresh.

- **Live site:** https://virtualteammate.com

To preview a change, save the file and refresh the browser. There is nothing to
compile or rebuild.

---

## 2. The pages

### Homepage

The homepage (`index.php`) is a long single-scroll page. Its sections appear in this order,
top to bottom:

1. **Topbar** — slim bar above the nav with a "Get started" call to action.
2. **Navigation** — logo, menu, Healthcare mega-menu (medical + dental links).
3. **Hero + stats** — headline, animated graph, key statistics.
4. **ROI calculator** — interactive savings calculator (slider + full-time/part-time toggle).
5. **Client marquee** — scrolling strip of client logos.
6. **Global network** — world map showing reach.
7. **Press marquee** — scrolling strip of news/press logos.
8. **Specialties** — split layout for Medical and Dental VAs.
9. **ROI stats** — supporting savings statistics.
10. **Process** — how engagement works, step by step.
11. **Testimonials** — client quotes.
12. **How we work** — service model overview.
13. **VA profiles** — sample virtual assistant cards.
14. **FAQ** — accordion of common questions.
15. **Business strip** — pointer to business/non-profit staffing.
16. **CTA form** — final call to action / lead form.
17. **Footer** — navigation, contact info, legal links.
18. **Scroll-to-top button** — appears after scrolling down.

### Other pages

| Page | Path | Purpose |
|------|------|---------|
| Medical service pages (5) | `/services/medical-*/` | Administrative support, receptionist, biller, scribe, assistant |
| Dental service pages (5) | `/services/dental-*/` | Admin, receptionist, biller, scribe, coordinator |
| Services hub | `/services/` | Listing of all roles |
| About | `/about/` | Company, founder, leadership, mission, process |
| Business | `/business/` | Business & non-profit VA staffing |
| Careers | `/careers/` | VA job listings & how to apply |
| Case studies | `/case-studies/` | Documented client results |
| Contact | `/contact/` | HQ address, phone, email, booking |
| Guarantee | `/guarantee/` | The 30-Day Right-Fit Promise |
| Healthcare landing | `/healthcare-landing/` | Healthcare lead funnel |
| Dental landing | `/dental-landing/` | Dental lead funnel |
| Business landing | `/business-landing/` | Business lead funnel |
| Meet the team | `/virtual-teammates/` | VA profile gallery |
| Terms | `/terms/` | Terms of Service |
| Privacy | `/privacy-policy/` | Privacy Policy |

> **Note:** The individual service pages and the services hub are currently enabled on the
> staging environment only; on production they redirect visitors to the homepage
> specialties section. This is intentional while those pages are finished.

---

## 3. Editing common content

Most everyday changes are made in three places: shared page parts in `includes/`, the
single stylesheet `css/style.css`, and the single behavior file `js/main.js`.

### Navigation and footer

- **Navigation** (logo, menu links, the Healthcare dropdown): `includes/nav.php`
- **Footer** (links, contact info, legal): `includes/footer.php`

Menu anchors like `#specialties` or `#faq` jump to sections on the homepage.

### Client logos (the scrolling client strip)

Client logos are loaded automatically from the folder
`images/clients/marquee/`. To add a client, drop a logo image
(`.webp`, `.png`, `.jpg`, or `.svg`) into that folder — it will appear in the strip on the
next page load. To remove one, delete its file. No code change is needed.

### Press logos (the news strip)

Press logos are listed in a `press` array near the top of `js/main.js`. Each entry has a
name, an image path under `images/press/`, and a link to the press release. To add or
remove an outlet, edit that array and place the matching image in `images/press/`.

### ROI calculator numbers

The savings figures in the ROI calculator are set as fixed rates in `js/main.js` (the
bi-weekly cost of a Virtual Teammate vs. a comparable US in-house hire, full-time and
part-time). If pricing changes, update those constants and the calculator, gauge, and bar
chart all recalculate automatically.

### FAQ, testimonials, and section copy

These live directly in the page markup (`index.php` for the homepage, or the relevant
page's `index.php`). Editing the text in place updates the page. FAQ entries are also
fed to search engines as structured data — see the developer reference for how that works.

### Images

- **Logos & favicons:** `images/` root (`logo.webp`, `logo-sm.webp`, favicons).
- **Hero & section photos:** `images/photos/` and `images/`.
- **Specialty card photos:** `images/photos/` (medical / dental section images).
- **Client logos:** `images/clients/` and `images/clients/marquee/`.
- **Press logos:** `images/press/`.
- **Downloadable PDFs:** `images/pdf/` (e.g. KPI result sheets).

When replacing a photo, keep the same filename to avoid touching code, or update the
reference in the page.

---

## 4. What happens when a visitor submits a form

Every lead form on the site — the hero form, the CTA form, the booking and checklist
modals, and the landing-page forms — sends the visitor's details to a single endpoint
(`/lead.php`). When a visitor submits:

1. Their information is **saved** to the internal leads list.
2. The team is **notified by email** automatically.
3. The lead is **synced to HubSpot** so it flows into the sales pipeline.
4. The visitor sees an instant thank-you message.

From the visitor's perspective there is nothing extra to do — the response is immediate.
From your side, every submission shows up in the staff portal's **Leads** page (and raises
the "new leads" badge there). See the portal user guide for that view.

The forms also include an invisible "honeypot" field that silently absorbs spam bots, so
junk submissions do not reach the team.

---

## 5. How the site looks on different screens

The design is fluid and adjusts to the viewer's screen:

- **Standard desktop / laptop:** full multi-column layout (up to a 1980px-wide canvas).
- **Tablet / small laptop (≤1280px wide):** the menu collapses to a hamburger button and
  multi-column sections stack.
- **Phone (≤768px wide):** further simplified, full-width, larger touch targets.
- **Very large / high-resolution displays:** the whole layout scales up proportionally so
  it fills big monitors without looking sparse.

Animations (fade-ins, counters, marquees) are automatically disabled for visitors who have
asked their device to reduce motion, for accessibility.

---

## 6. Quick reference — "where do I change…?"

| I want to change… | Edit this |
|-------------------|-----------|
| A menu link | `includes/nav.php` |
| Footer text or links | `includes/footer.php` |
| Homepage hero / FAQ / testimonials | `index.php` |
| A service page's copy | `/services/<role>/index.php` |
| Client logos in the strip | add/remove files in `images/clients/marquee/` |
| Press logos | the `press` array in `js/main.js` + `images/press/` |
| ROI calculator rates | the rate constants in `js/main.js` |
| Colors / spacing / fonts | `css/style.css` |
| Where forms go / lead handling | see the developer reference (`lead.php`) |

For anything involving code structure, the build pipeline, SEO data, or the lead-handling
internals, see **`marketing-site-dev.md`**.
