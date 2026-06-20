# Virtual Teammate — HubSpot Client Funnel

End-to-end funnel from **lead capture → nurturing → contract start**, mapped to
this site's actual CTAs and to HubSpot (Hub ID **46221241**, region na1).

Legend: ☐ = to do in HubSpot · ✅ = already wired in the codebase.

---

## Site CTAs → funnel stage (the three entry points)

The homepage "Three Ways to Start" block defines the funnel intents. Every form
posts to `/lead.php` (which now upserts a HubSpot contact) and carries a
`data-cta-intent` / `intent` value:

| Intent (`data-cta-intent`)        | Funnel stage | Site CTA                                   |
|-----------------------------------|--------------|--------------------------------------------|
| `buyers-checklist`                | TOFU (explore)   | "HIPAA VA buyer's checklist"           |
| `practice-audit`                  | MOFU (diagnose)  | "Book my practice staffing audit"      |
| `strategy-call` / `jumpstart`     | BOFU (scope)     | "Jumpstart / strategy call"            |
| `book`                            | BOFU             | Contact-page "Pick a time"             |
| `buyback` / business              | MOFU             | Business & non-profit VAs              |
| careers / VT request / ROI calc   | side funnels     | Careers apply, VT shortlist, ROI reach-out |

---

## Phase 0 — Foundation (one-time)

- ✅ Tracking code installed sitewide (`js.hs-scripts.com/46221241.js`, production-gated).
- ✅ Every form → HubSpot **contact upsert** by email (server-side in `lead.php`).
- ☐ Confirm the private-app token scopes: `crm.objects.contacts.write`, `forms`, `crm.objects.deals.write`.
- ☐ Create custom **contact properties**: `lead_intent`, `lead_source_form`, `ehr_pms`, `specialty` (medical/dental), `practice_size`, `affiliate_code` (LeadDyno).
- ☐ Confirm **lifecycle stages**: Subscriber → Lead → MQL → SQL → Opportunity → Customer.
- ☐ Build the **deal pipeline** "VT Client Onboarding" with stages (Phase 3 below).

## Phase 1 — Lead capture

- ☐ Create a HubSpot **form** per CTA intent (see table): Buyer's Checklist,
  Practice Staffing Audit, Strategy/Jumpstart Call, Contact, Careers, VT Shortlist
  Request, ROI Reach-out. (Or one master "Website Lead" form with an `intent` field.)
- ✅ Field mapping handled in `lead.php` (email, first/last, phone, company → HubSpot
  contact). To also fire HubSpot **form submissions** (for form analytics/workflows),
  give me each form's GUID and I'll wire the matching CTA to the Forms API
  (`api.hsforms.com/.../submit/46221241/{guid}`) — the landing pages already do this.
- ☐ On capture set `lifecyclestage = lead`, stamp `lead_intent` + `lead_source_form`.
- ☐ Capture UTM + LeadDyno `affiliate_code` into contact properties.
- ☐ Auto-notify the CSM (already emailed via `lead.php`; mirror as a HubSpot task).

## Phase 2 — Lead nurturing

- ☐ **Segment** active lists by `lead_intent` and by specialty (medical vs dental).
- ☐ **Workflows / sequences:**
  - `buyers-checklist` → deliver the PDF, then a 3–5 email educational nurture
    (HIPAA compliance, pricing transparency, case studies) → CTA: *Book the audit*.
  - `practice-audit` → instant confirmation + prep checklist + calendar reminders;
    no-show → re-engagement branch.
  - `strategy-call` / `jumpstart` → notify owner immediately, fast-track to sales.
- ☐ **Lead scoring**: + for engagement (email opens, page views, calculator use,
  pricing/case-study visits) and fit (specialty, practice size, EHR match).
- ☐ **MQL → SQL** handoff rule (e.g., audit booked OR score ≥ threshold) → assign
  owner (CSM / founder Chris for new healthcare engagements) and create a **deal**.

## Phase 3 — Sales → contract start (deal pipeline)

Pipeline **"VT Client Onboarding"** — create a deal when an audit/strategy call is booked:

1. ☐ **New / Audit booked** — deal created, owner assigned.
2. ☐ **Discovery complete** — audit done; workflows mapped, KPIs + role spec captured.
3. ☐ **Proposal sent** — scoped engagement (role, tier/pricing, timeline).
4. ☐ **Agreement / e-sign** — contract signed; 30-Day Right-Fit Promise noted.
5. ☐ **Closed-won → Customer** — set `lifecyclestage = customer`; trigger onboarding workflow.
6. ☐ **Onboarding** — kickoff, EHR/PMS access, SOP handoff, CSM intro.
7. ☐ **Live** — teammate placed (target 1–2 weeks); start monthly KPI scorecards.

## Phase 4 — Retention / expansion (post-contract)

- ☐ Monthly KPI-scorecard email workflow; QBR tasks for the CSM.
- ☐ Health-score property; at-risk re-engagement branch.
- ☐ Expansion plays (additional teammates → VT Shortlist Request form).
- ☐ Review/referral request workflow → Google review link + LeadDyno affiliate invite.

---

## What's done in code vs. what's a HubSpot-account task

- **Code (done):** tracking embed, contact upsert on every form, source/affiliate scripts.
- **Code (ready when you are):** wire each CTA to a specific HubSpot **form GUID**
  for native form submissions — send me the GUIDs (or say "use one master form").
- **HubSpot account (yours / I can draft):** properties, lifecycle, pipeline,
  workflows, lead scoring — these are configured in the HubSpot UI. I can't create
  them blindly via API without risking your account, but I can provide exact
  field/stage/workflow specs to paste in, or build them via API if you explicitly
  authorize it.
