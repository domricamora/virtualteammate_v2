# Virtual Teammate — Marketing Site Guide

*A non-technical, page-by-page guide to the Virtual Teammate marketing site: what every page
contains, what it's for, the forms and calls-to-action on it, and where to change the words and
numbers. For the code, build pipeline, and SEO internals, see `marketing-site-dev.md`.*

---

## 1. What this is

The marketing site for **Virtual Teammate** — a staffing agency for HIPAA-compliant medical,
dental, and business virtual assistants (VAs, branded "Virtual Teammates" or VTs). It is a
PHP-rendered website with **no build step**: you edit a file, save it, and refresh the browser
to see the change live.

- **Live site:** https://virtualteammate.com
- **Local preview:** http://localhost/vtnew/

Every page is built from shared parts so the header, navigation, and footer stay consistent.
Those shared parts live in `includes/` (covered in §5). Throughout this guide, "edit the page's
`index.php`" means the file inside that page's own folder — e.g. the About page is
`about/index.php`.

---

## 2. The site at a glance

| Page | Path | What it's for |
|------|------|---------------|
| Homepage | `/` | The main lead-gen page: hero, ROI calculator, proof, offer, CTAs |
| Services hub | `/services/` | Directory of all 10 roles *(localhost-only, work in progress)* |
| 10 service pages | `/services/<role>/` | One deep page per role (5 medical, 5 dental) |
| Healthcare landing | `/healthcare-landing/` | Quiz-driven lead funnel for medical practices |
| Dental landing | `/dental-landing/` | Quiz-driven lead funnel for dental practices |
| Business landing | `/business-landing/` | Quiz-driven lead funnel for business owners |
| About | `/about/` | Founder, mission, principles, process |
| Business | `/business/` | Business & non-profit VA staffing |
| Careers | `/careers/` | VA recruiting: roles, culture, hiring process, apply |
| Case studies | `/case-studies/` | Real client KPI results |
| Contact | `/contact/` | Phone, email, booking, Phoenix HQ map |
| Guarantee | `/guarantee/` | The 30-Day Right-Fit Promise, in detail |
| Meet the team | `/virtual-teammates/` | Searchable VA talent directory |
| Terms | `/terms/` | Terms of Service (legal) |
| Privacy | `/privacy-policy/` | Privacy Policy (legal) |

> **Two recurring promises appear all over the site** — learn them once: the **30-Day
> Right-Fit Promise** (no-cost replacement, full money-back in the first 30 days, and backup
> coverage), and the **flat-rate pricing** (from **$750 bi-weekly** full-time for the Pro tier,
> **$1,000** for the Specialist tier; about **60–73% less** than a US in-house hire). If those
> numbers change, they need updating in many places — search the whole site for the figure.

---

## 3. Page-by-page reference

Every page sets its own SEO title/description at the very top of its `index.php`, then writes
its content inside `<main>`. Most pages share the same building blocks: a **hero** (eyebrow tag
+ big headline + lead paragraph + CTA buttons), proof/stat strips, content sections, an **FAQ**,
and a closing **call-to-action**.

### 3.1 Homepage — `/`

**Purpose:** The flagship lead-generation page for medical & dental VAs, built around ROI and
the guarantee. Its sections, top to bottom:

1. **Hero + stats** — Headline *"Short-staffed and overworked? Fully staff your practice in
   weeks, not months, for 73% less."* with an animated rising graph, a four-point value list,
   an animated stats card (73% lower cost · 95% clean-claim rate · 4.9★ Google · 30-Day
   Promise), and a trust row. CTAs: **Book my practice staffing audit** and **Calculate my
   savings**, plus a **Get the HIPAA VA buyer's checklist** link.
2. **Client marquee** — A scrolling strip of client logos, auto-loaded from
   `images/clients/marquee/`.
3. **Proven Client Impact** — Four case-study cards (*"Targets set. Targets beaten."*) with real
   KPI gains (cancer center, multi-specialty clinic, primary care, endo/oral surgery).
4. **ROI calculator** — *"See your annual savings: in real time."* A slider (1–25 teammates)
   drives live animated figures: annual/monthly savings, a savings-percentage gauge, a
   cost-comparison bar, and an expandable breakdown.
5. **Specialties** — *"Trained for your specialty, measured on your numbers."* Two big cards
   (Medical and Dental), each with proof bullets and pill links to that side's five service
   pages, plus a HIPAA trust strip and the "Practice Staffing Audit" explainer.
6. **Guarantee** — The 30-Day Right-Fit Promise as three cards (no-cost replacement · money-back
   · backup coverage).
7. **Process** — *"Hire in weeks, not months."* Three steps: book the audit → meet & interview →
   launch and go live.
8. **Meet the Team profiles** — Up to 8 VA cards pulled **live from the portal database**
   (medical/dental, with photos). Clicking a card opens the **Request a teammate** modal. If the
   database is unavailable, a graceful fallback card shows instead.
9. **Security & Compliance** — Five cards (HIPAA training, BAA-compatible agreement, aligned
   controls, full audit trail, device & access security), each split into "What it is" and
   "What it means for you."
10. **Global Reach** — *"A bigger bench, a better match, on your time zone."* Six benefit
    bullets beside an animated world map.
11. **Press & News** — A scrolling strip of press logos (driven by `js/main.js`).
12. **FAQ** — Eight questions (cost, fit/guarantee, security, speed, software, location, point of
    contact, contracts). Also fed to search engines as structured data.
13. **Offer** — *"Here's exactly what you get."* Six cards summarizing the package and pricing.
14. **CTA stages** — *"Three Ways to Start"*: the buyer's checklist, the practice staffing audit,
    and the jumpstart/strategy call.
15. **Footer** + scroll-to-top button.

**Forms & CTAs:** All buttons open one of the shared modals — **practice-audit** and
**strategy-call** (HubSpot Meetings booking embeds), **buyers-checklist** (HubSpot form), and
**request** (a teammate-request form that posts to `lead.php`). See §4.

**What to edit:** Hero copy, stat numbers, case-study figures, the ROI calculator's pre-rendered
default values (keep in sync with the rate in `js/main.js`), specialty pill links, FAQ, offer
cards, and process steps — all in `index.php`.

### 3.2 Services hub — `/services/`

**Purpose:** A directory of all ten roles, grouped Medical (5) and Dental (5) as bento-grid
cards, with the biller highlighted as the "Specialist role" feature card on each side.

> **Publish status:** This hub is **localhost-only** (work in progress). On any other host it
> redirects to the homepage `#specialties` section, and it carries `noindex,nofollow`. The ten
> individual service pages below are **fully live** regardless.

**What to edit:** The role cards are hardcoded PHP arrays (`$medical`, `$dental`) near the top of
`services/index.php` — each entry has a slug, icon, name, and blurb.

### 3.3 The ten service pages — `/services/<role>/`

These are the heart of the site's organic-search and conversion strategy: one dedicated page per
role a practice might hire, each targeting how that buyer actually searches (e.g. "virtual
medical receptionist," "dental billing virtual assistant"). There are **ten** — five medical and
five dental. They **are published and listed in the sitemap.**

**Every service page follows the same anatomy**, so once you've seen one you can edit any of
them:

1. **Hero** — eyebrow tag, an `<h1>` headline, and a lead paragraph framing the pain and the
   promise; three trust badges (HIPAA · Bilingual · Live in 1–2 Weeks); two CTAs (book an audit /
   get the buyer's checklist).
2. **Stat strip** — Up to 73% lower cost · 1–2 weeks to start · 4.9★ Google · 30-Day Promise.
3. **What they handle** — a checklist of the role's tasks beside a "Right now vs. with a
   teammate" before/after.
4. **Why practices do this** — a six-card benefits grid.
5. **Plugs into your setup** — chips listing the phone systems, EHRs, and practice-management
   software the teammate works inside.
6. **How it works** — three steps reframed for the role.
7. **FAQ** — six role-specific questions (also structured data for search).
8. **CTA** — the shared booking/lead block (`includes/service-cta.php`).

To edit a page's copy, open its `index.php` (e.g. `services/medical-receptionist/index.php`); the
SEO title, description, and FAQ entries are PHP variables at the very top of the file.

#### Medical roles

- **Medical Receptionist** — `/services/medical-receptionist/` — *Phones & Front Desk · "Never
  miss a patient call again."* Answers phones live, books/confirms/reschedules, runs reminders &
  recall, checks insurance, handles intake and portal messages, bilingual. Works with RingCentral,
  Weave, Zoom Phone, Nextiva, 8x8 and EHRs like Epic, Athenahealth, eClinicalWorks.
- **Medical Administrative Support** — `/services/medical-administrative-support/` — *Front-Office
  Help · "Take the paperwork off your team's plate."* Chart prep, intake, records, prior auths &
  referrals, inbox/calendar, keeping the EHR clean.
- **Virtual Medical Assistant** — `/services/medical-assistant/` — *Clinical Support · "Give your
  MAs their day back."* Visit prep, refills, prior authorizations, routing results, care
  coordination, patient follow-up.
- **Medical Biller** — `/services/medical-biller/` — *Medical Billing Help · "Get paid for the
  work you've already done."* Clean claims daily, denial work, chasing unpaid claims, posting &
  reconciling, patient balances, coverage/prior-auth checks.
- **Medical Scribe** — `/services/medical-scribe/` — *Real-Time Charting · "Stop charting after
  the kids are asleep."* Live notes over an encrypted link, orders and referrals, your templates,
  keeping pace all clinic day, tidying the chart for sign-off.

#### Dental roles

- **Dental Receptionist** — `/services/dental-receptionist/` — *Phones & Front Desk · "Keep every
  chair full."* Live calls, booking/confirming, recall & reactivation, dental insurance checks,
  new-patient intake, bilingual; works on your phone system and PMS.
- **Dental Admin** — `/services/dental-admin/` — *Dental Back Office · "Walk in to a day that's
  already prepped."* Insurance verification & benefit breakdowns, chart and treatment-plan prep,
  records, forms & e-signatures, reports. Works in Dentrix, Open Dental and more.
- **Dental Biller** — `/services/dental-biller/` — *Dental Billing Help · "Stop leaving production
  stuck in claims."* Clean claims with narratives and attachments, denial work, chasing unpaid
  claims, posting EOBs, secondary claims & pre-auths, patient statements.
- **Dental Scribe** — `/services/dental-scribe/` — *Real-Time Charting · "Eyes on the patient,
  not the keyboard."* Live clinical notes, perio charting, logging existing/proposed treatment,
  closing the chart chairside.
- **Dental Treatment Coordinator** — `/services/dental-coordinator/` — *Treatment & Recall ·
  "Turn diagnosed treatment into booked visits."* Works the unscheduled-treatment list, presents
  financing, fills hygiene recall, tightens the schedule, follows up until it's booked.

### 3.4 Healthcare landing — `/healthcare-landing/`

**Purpose:** A focused, quiz-driven funnel for medical practice owners. Headline: *"Our
teammates. Your whole back office. 73% less than in-house hires."*

**Sections:** hero with a value checklist and offer; a proof bar + stats; **Proven Client
Impact** (four case cards); *What they take off your plate* (six back-office areas); the
**Offer** block with pricing; the **Practice Efficiency Quiz** (6 questions); the three-commitment
guarantee; an 8-question FAQ; and a final dual CTA.

**The lead engine — the quiz:** Six questions estimate hours and revenue lost to admin, then ask
for **email + phone** to unlock a downloadable report. On submit it posts to **two places**: the
HubSpot form endpoint *and* the site's own `lead.php`. Secondary CTAs open the **practice
staffing audit** booking and the **buyer's checklist** download.

**What to edit:** Hero/offer copy and pricing, the four case cards, and the quiz questions and
result thresholds, all in `healthcare-landing/index.php`.

### 3.5 Dental landing — `/dental-landing/`

**Purpose:** The dental counterpart, same structure, dental-specific copy. Headline: *"Our
teammates. Your whole back office. 73% less than in-house hires."* Proof points center on
no-show reduction, chairs filled, recall, and clean claims; software references are Dentrix,
Open Dental, Curve, Denticon, Carestream. Includes a 6-question **Dental Practice Efficiency
Quiz** and a 10-question FAQ. Same dual lead capture (HubSpot + `lead.php`).

**What to edit:** `dental-landing/index.php` — hero, case cards, quiz, FAQ.

### 3.6 Business landing — `/business-landing/`

**Purpose:** A quiz-first funnel for general business owners. Headline: *"How much is busywork
costing your business?"* The page **opens with the quiz** (8 questions, including one
multi-select), then shows trust stats, a "Hidden Cost of Busywork" section, a "What they take off
your plate" six-area grid, a 4-question FAQ, and a final CTA.

**Key difference:** the primary closing CTA is an **external Calendly/HubSpot Meetings discovery
call** ("Book a Free 15-Min Consultation"), alongside a "Buy Back Your Company's Time" modal. The
quiz still posts to HubSpot + `lead.php`.

**What to edit:** `business-landing/index.php` — hero image and copy, quiz questions, benefit
grid, and the consultation link.

### 3.7 About — `/about/`

**Purpose:** Build trust through founder, mission, and process. Sections: hero with an
"at a glance" panel; a stats bar (2,000+ VTs placed · 600+ practices · 30+ years · 4 countries);
the **founder** section (Chris McShanag); the **mission** ("value-creation culture"); **Core
Principles** (6 cards); **Why Practices Choose VT** (6 cards); the 3-step **process** timeline;
and a 6-question FAQ. CTAs open the practice-audit booking and link to case studies.

**What to edit:** `about/index.php` — headlines, the four stat numbers, the founder narrative,
principles/benefit cards, process steps, and FAQ.

### 3.8 Business — `/business/`

**Purpose:** Show that VT staffs beyond healthcare. Sections: hero (*"Beyond healthcare: Virtual
Teammates for every function"*); **Roles We Staff** (6 cards: Administrative, Sales/SDR,
Marketing, Finance, Customer Service, Non-profit Ops); a **Roles That Drive Growth** deep-dive
(8 articles); **Meet the Bench** (a filterable gallery of real *business* teammates); a
6-question FAQ; and a **Two Ways to Start** block (operational assessment vs. buy-back).

**Forms:** opens the **operational-assessment**, **buy-back**, and **request-a-teammate** modals.

**What to edit:** `business/index.php` — role cards, the eight deep-dive articles, FAQ, and the
two CTA cards.

### 3.9 Careers — `/careers/`

**Purpose:** Recruit VA talent. Sections: hero (*"Create your future. Live your best life."*);
a stats bar; **Roles We Hire For** (8 departments); **Benefits** (6 cards); **Mission & Values**
(7 values); the **8-step hiring process** timeline; **testimonials** (6 teammate quotes); a
6-question FAQ; and a final CTA. "Explore Open Positions" links out to the Teamtailor job board.

**Form:** a built-in **application form** (name, email, location, message, and a role dropdown
with the 8 departments) that posts a lead; confirmation promises a reply in 1–2 business days.

**What to edit:** `careers/index.php` — role and benefit cards, the values, hiring-process steps,
testimonials, FAQ, and the role dropdown options.

### 3.10 Case studies — `/case-studies/`

**Purpose:** Prove results with real numbers. Sections: hero with a sample KPI scorecard;
**Across All Workstreams** (four aggregate stat cards); **Client Spotlights** (four anonymized
case cards — cancer center, multi-specialty clinic, primary care, endo/oral surgery — each with
target-vs-result KPIs); **How We Measure** (Targeted KPI → Results → Value Created → Achievement
%); **Why VT Delivers** (6 cards); a 6-question FAQ; and the shared "ways to start" CTA.

**What to edit:** `case-studies/index.php` — the four case narratives and KPI numbers, the
aggregate stats, and the FAQ. Client identities are intentionally kept confidential.

### 3.11 Contact — `/contact/`

**Purpose:** Make it easy to reach a real person. Sections: hero with a "Client Success Desk"
card; **Three Ways to Reach Us** (phone, email, book-a-call cards); a quick-facts bar (hours, HQ
address, service area, portal login); a **Google Map** of the Phoenix HQ with a "Get Directions"
link; the shared global-coverage block; a 6-question pre-contact FAQ; and the booking CTA.

**Key details (appear in several places + the schema):** phone **(480) 847-2498**, email
**clientsuccess@virtualteammate.com**, address **2425 East Camelback Road, Suite 400, Phoenix, AZ
85016**, hours **Mon–Fri 8am–6pm MST**.

**What to edit:** `contact/index.php` — update the phone, email, address, and hours everywhere
they appear (including the structured-data block near the top).

### 3.12 Guarantee — `/guarantee/`

**Purpose:** Sell the risk reversal. Sections: hero (*"Hire a Virtual Teammate zero-risk.
Period."*); a stats strip (30-day window · 2 business days to replace · $0 fees · 95%+
retention); **The Three Commitments**; what the practice staffing audit covers; **How to Claim
the Promise** (3 steps); a side-by-side **risk comparison** (VT vs. a typical VA agency);
**Why We Can Offer This** (6 cards); a 6-question FAQ with plain-language fine print; and a final
CTA.

**What to edit:** `guarantee/index.php` — the three commitments, the comparison rows, the stats,
and the FAQ/fine print.

### 3.13 Meet the team — `/virtual-teammates/`

**Purpose:** A searchable talent directory that doubles as a lead funnel. Sections: hero with a
photo collage; a **Value Matching** lead form; **the bench** with search + department/skill
filters and a card grid (12 at a time, "Load more"); SEO content blocks (Why VT, VAs for every
team, How matching works, FAQ); and a closing CTA.

**How profiles work:** Cards are pulled **live from the portal database** (HIPAA medical/dental
VAs). **Anonymous visitors** see a teaser (name, role, skills, scores) and a locked notice;
**logged-in portal clients** see the full CV (intro video, résumé, assessment scores) and can
request the teammate directly. The lead form posts to `lead.php`; clicking "Get matched" on a
teaser pre-fills it.

**What to edit:** `virtual-teammates/index.php` — hero copy, the lead-form labels, the SEO
content sections, and the FAQ. The **profile data itself is not edited here** — it comes from the
portal (see the portal guide).

### 3.14 Terms — `/terms/` and Privacy — `/privacy-policy/`

**Purpose:** The two legal pages. **Terms** governs use of the website only (staffing
engagements are covered by a separate signed agreement); **Privacy** explains what the site
collects and how it's used, and states plainly that **no patient/PHI should be submitted through
public forms**. Both show a "Last updated" date and a `support@virtualteammate.com` contact.

**What to edit:** `terms/index.php` and `privacy-policy/index.php` — the "Last updated" date and
the contact email are the safe everyday edits; **route any wording changes through legal/
compliance** before publishing.

---

## 4. Forms, CTAs, and what happens on submit

Most calls-to-action across the site open one of a few **shared modals**, identified by a
`data-cta-intent` value on the button:

- **practice-audit** / **strategy-call** — open a **HubSpot Meetings** booking calendar embed
  (no form fields; the visitor picks a time).
- **buyers-checklist** — opens a **HubSpot form** to email the "HIPAA VA buyer's checklist" PDF.
- **request** — opens a "Request a teammate" form (used by the homepage, business, and talent
  directory) that posts to `lead.php`.

**Standard lead forms and the quizzes** ultimately send the visitor's details to the site's own
endpoint, **`/lead.php`**. When a visitor submits:

1. Their information is **saved** to the internal leads list.
2. The team is **notified by email** automatically.
3. The lead is **synced to HubSpot** so it flows into the sales pipeline.
4. The visitor sees an instant thank-you message.

The three **landing-page quizzes** additionally post straight to a HubSpot form endpoint (so the
quiz answers and scores are captured there too) in parallel with `lead.php`. From your side,
every submission shows up in the staff portal's **Leads** page and raises its "new leads" badge —
see the portal user guide.

All forms include an invisible "honeypot" field that silently absorbs spam bots, so junk
submissions don't reach the team.

---

## 5. Shared parts & everyday edits

Most cross-page changes happen in three places: shared partials in `includes/`, the single
stylesheet `css/style.css`, and the single behavior file `js/main.js`.

### Navigation and footer

- **Navigation** (logo, menu links, the Healthcare mega-menu): `includes/nav.php`
- **Footer** (links, contact info, legal): `includes/footer.php`

Menu anchors like `#specialties` or `#faq` jump to sections on the homepage.

### Client logos (the scrolling client strip)

Loaded automatically from `images/clients/marquee/`. To add a client, drop a logo image
(`.webp`, `.png`, `.jpg`, or `.svg`) into that folder — it appears on the next page load. To
remove one, delete its file. No code change needed.

### Press logos (the news strip)

Listed in a `press` array near the top of `js/main.js`. Each entry has a name, an image path
under `images/press/`, and a link. Edit that array and add the matching image to update.

### ROI calculator numbers

The savings rates are constants in `js/main.js` (the bi-weekly cost of a Virtual Teammate vs. a
comparable US in-house hire). If pricing changes, update those constants — the calculator,
gauge, and bar chart all recalculate. **Also** update the homepage's pre-rendered default
figures so non-JavaScript visitors and search crawlers see matching numbers.

### Meet-the-Team / talent profiles

The homepage "Meet the Team" cards and the `/virtual-teammates/` directory are populated **from
the portal database**, not the page markup. To change who appears or their details, edit the VT
in the portal (see the portal user guide), not the marketing page.

### FAQ, testimonials, and section copy

These live directly in each page's `index.php`. Editing the text in place updates the page; FAQ
entries are also emitted as structured data for search engines (see the developer reference).

### Images

- **Logos & favicons:** `images/` root.
- **Hero & section photos:** `images/photos/` and `images/`.
- **Client logos:** `images/clients/` and `images/clients/marquee/`.
- **Press logos:** `images/press/`.
- **Downloadable PDFs:** `images/pdf/`.

When replacing a photo, keep the same filename to avoid touching code, or update the reference in
the page.

---

## 6. How the site looks on different screens

The design is fluid and adjusts to the viewer's screen:

- **Standard desktop / laptop:** full multi-column layout (up to a 1980px-wide canvas).
- **Tablet / small laptop (≤1280px):** the menu collapses to a hamburger and sections stack.
- **Phone (≤768px):** further simplified, full-width, larger touch targets.
- **Very large / high-resolution displays:** the whole layout scales up proportionally to fill
  big monitors.

Animations (fade-ins, counters, marquees) are automatically disabled for visitors who have asked
their device to reduce motion, for accessibility.

---

## 7. Quick reference — "where do I change…?"

| I want to change… | Edit this |
|-------------------|-----------|
| A menu link or the Healthcare mega-menu | `includes/nav.php` |
| Footer text or links | `includes/footer.php` |
| Homepage hero / FAQ / offer / process | `index.php` |
| A specific service page's copy | `services/<role>/index.php` |
| The services-hub role cards | the `$medical` / `$dental` arrays in `services/index.php` |
| A landing-page quiz or its results | the relevant `*-landing/index.php` |
| Phone, email, or HQ address | `contact/index.php` (and check the footer) |
| Pricing ($750 / $1,000 / 60–73%) | search the whole site — it appears on many pages |
| Client logos in the strip | add/remove files in `images/clients/marquee/` |
| Press logos | the `press` array in `js/main.js` + `images/press/` |
| ROI calculator rates | the rate constants in `js/main.js` (+ homepage defaults) |
| Who appears in Meet the Team / talent directory | the VT records in the portal |
| Colors / spacing / fonts | `css/style.css` |
| Where forms go / lead handling | see the developer reference (`lead.php`) |
| Legal wording (Terms / Privacy) | `terms/` / `privacy-policy/` — via legal review |

For anything involving code structure, the build pipeline, SEO data, or the lead-handling
internals, see **`marketing-site-dev.md`**.
