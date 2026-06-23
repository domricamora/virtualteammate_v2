# Virtual Teammate Portal — User Guide

*A guide to the staff & client portal for everyone who logs in: super admins, clients,
client success managers (CSMs), and virtual teammates. For the code behind it, see
`portal-dev.md`.*

---

## 1. What the portal is

The Virtual Teammate Portal is the private workspace where the team and clients manage
day-to-day work: tasks, meetings, daily reports, messaging, productivity, and (for staff)
the full client and talent pipeline.

- **Address:** the portal lives at **`/portal/`** on the site.
- **Sign in:** go to `/portal/`, enter your email and password. You stay signed in until you
  log out or your session expires.
- **Your profile:** open **Profile** to update your name, phone, job title, and photo.
- **Your password:** change it any time from the **Password** page (you'll confirm your
  current password first).

> **Not yet documented here:** the portal can also be installed as an app on your phone or
> desktop and can send push notifications. Those features are covered separately and are
> intentionally left out of this guide for now.

---

## 2. Roles at a glance

What you can see and do depends on your role. There are five:

| Role | Who it's for | In short |
|------|--------------|----------|
| **Super admin** | Virtual Teammate staff (founders, operations) | Full access — runs the whole system. |
| **Client** | Client company employees | Manage their assigned virtual teammates and work. |
| **CSM** | Client Success Managers | Manage their book of clients and those clients' VTs. |
| **VT (hired)** | Placed virtual teammates | Do daily work for their client and report on it. |
| **VT (on-pool)** | VTs not yet placed | A trimmed view focused on readiness and messaging. |

Your navigation menu only shows the pages your role is allowed to use.

---

## 3. Super admin

Super admins (Virtual Teammate staff) have the complete picture and all controls.

**Dashboard.** A system overview: user counts, active clients, upcoming meetings, recent
end-of-day reports, last 7 days of site traffic, and system status (cache and HTTPS).

**People & relationships.**
- **Users** — create, edit, activate/deactivate any account.
- **Clients** — manage client companies and their contract status.
- **CSMs** — view each CSM and the clients/VTs they manage.
- **VT Profiles** — manage virtual-teammate records (status, department, skills, resume,
  video, photo).
- **Assignments** — connect CSMs to clients and VTs to clients.
- **Relationships** — see the whole web of who manages and works with whom.

**Sales & leads.**
- **Leads** — every form submission from the marketing site. New leads raise a badge on the
  menu item until you view them; you can delete leads here.
- **Client Funnel** — the pipeline from lead to active client.

**Operations (shared with other roles).**
- **Meetings**, **EOD reports**, **Tasks**, **Messages**, **Productivity** — the super admin
  can see across all of these.

**System & outreach.**
- **HubSpot Sync** — import and refresh VTs, clients, and CSMs from HubSpot (see §7).
- **Email** — compose and send an email to a recipient from within the portal, and set which
  address receives new-lead notifications.
- **Traffic** — marketing-site pageview analytics (path, location, referrer).
- **Audit** — an immutable log of every important action (logins, edits, settings changes).
- **System toggles** — turn asset caching and forced-HTTPS on or off.

---

## 4. Client

Clients manage the virtual teammates assigned to their company.

**Dashboard.** Team overview: assigned VTs, active tasks, hours logged this month, and
upcoming meetings.

**What clients can do.**
- **My VTs** — view the virtual teammates working for them.
- **VT Assignments** — see which VTs are assigned and their status.
- **Tasks** — create and assign tasks to their VTs, set priority and due dates, and mark them
  complete (attachments supported).
- **Meetings** — schedule and review meetings with their CSM or VTs.
- **Productivity Reports** — review their team's end-of-day reports and KPI summaries.
- **Messages** — chat one-to-one with their CSM and VTs.
- **Request a VT** — ask for an additional virtual teammate; the request goes to a CSM for review.
- **Invoices** — view billing history.
- **Resources** — curated documents and links.
- **Notifications** — alerts for tasks, meetings, and messages.

---

## 5. CSM (Client Success Manager)

CSMs manage a book of clients and the VTs working for those clients.

**Dashboard.** Book-of-business view: their clients, those clients' VTs, and key metrics.

**What CSMs can do.**
- **My Clients & VTs** — the clients they manage and each client's virtual teammates.
- **VT Assignments** — manage which VTs are assigned to which clients.
- **Meetings** — schedule and track meetings with clients and VTs.
- **Productivity Reports** — monitor team productivity and KPIs across their book.
- **Messages** — chat with clients and VTs.
- **Request a VT (review)** — approve or decline clients' requests for additional VTs.
- **Special Links** — generate shareable links to talent profiles/resources.
- **Resources** — shared documents and links.
- **Notifications** — alerts across their accounts.

---

## 6. Virtual Teammate (hired and on-pool)

Virtual teammates use the portal to do their daily work and report on it. Hired VTs (placed
with a client) see the full set; on-pool VTs (awaiting placement) see a trimmed set.

**Dashboard.** Daily view: today's assignments, upcoming meetings, unread messages, and (for
hired VTs) payslip status.

**Hired VT can:**
- **My Assignments** — daily tasks from their client/CSM; mark them complete.
- **My Meetings** — scheduled calls with their client or CSM.
- **Productivity Reports / EOD** — submit an end-of-day report each day: best work done,
  where they need help, focus for next, and KPI progress.
- **Messages** — chat with their CSM and teammates.
- **My CSM & Teammates** — see who manages them and who else works at the same client.
- **Payslips** — view payroll history.
- **Resources** and **VTM Apps** — training materials and the directory of approved tools.
- **Refer a Friend** — share a referral link and track referral bonuses.
- **Notifications** — alerts for new tasks, meetings, and messages.

**On-pool VT** sees a focused subset: **Productivity Reports**, **Messages**, **Resources**,
**VTM Apps**, **Refer a Friend**, and **Notifications**. Assignments, meetings, payslips, and
the CSM/teammate relationship view appear once they are placed with a client.

---

## 7. HubSpot Sync (super admin)

The portal keeps its people in step with HubSpot. From the **HubSpot Sync** page a super
admin can:

- **Test the connection** to confirm the API token works.
- **Run a sync** that imports/updates virtual teammates, clients, and CSMs from HubSpot. The
  sync runs in small batches and shows live progress, so it never times out — you can watch it
  advance and **pause / resume / reset** it.
- **Sync one record** by searching for a specific VT or client and pulling just that person.
- **Choose what's imported** — including whether to download photos, resumes, and videos.

A VT's HubSpot status decides their portal role (for example, "Hired" becomes a hired VT and
"Matched/Unmatched" becomes on-pool). Newly imported people are created with a temporary
default password that should be changed on first login.

---

## 8. Features everyone shares

- **Notifications.** A bell with an unread count. Each alert links to the relevant page. You
  can mark items read, delete them, and choose whether to also receive **email copies**
  (toggle per account).
- **Messaging.** One-to-one chat with the people relevant to your role. New messages arrive
  without reloading the page and raise a notification.
- **Meetings.** Scheduled calls with date/time, topic, attendees, and a join link. Times are
  shown in your local timezone.
- **End-of-day (EOD) reports.** VTs submit a short daily summary; clients, CSMs, and admins
  review them on the productivity pages.
- **Tasks.** Assignable to-do items with priority, due date, status, and file attachments.
- **Productivity.** Dashboards that roll up EOD reports and KPIs for a person or a team.
- **Resources / Knowledge Center.** Curated links, documents, and training materials.

---

## 9. Signing out

Use **Log out** from the menu. For shared computers, always log out — the portal keeps you
signed in otherwise. If you forget your password, a super admin can reset it from the Users page.
