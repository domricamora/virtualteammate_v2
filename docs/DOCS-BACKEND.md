# Virtual Teammate — Backend Side Documentation

> The complete technical reference: every plugin, the custom code (plugins, mu-plugins, WPCode snippets), the data model, roles, the HubSpot sync engine, the member portal, and integrations.
> Companion doc: [DOCS-MARKETING.md](DOCS-MARKETING.md) (public site, funnels, content).
>
> **Site:** the **legacy (old) virtualteammate.com** WordPress website — `https://virtualteammate.com` — the prior WordPress build being superseded by the new `vtnew` marketing site. DB `0k7.183.mytemp.website_1772483989`, table prefix `wpvividstg01_`.

---

## 0. Stack at a glance

| Layer | Technology |
|---|---|
| CMS | WordPress (schema `db_version` 61833, ~WP 6.7-era) |
| Theme | `hello-elementor` **3.4.7** (stock, no child theme) |
| Page builder | **Elementor 4.1.1 + Elementor Pro 4.0.1** |
| Membership / roles / login | **Ultimate Member 2.11.4** |
| Data fields | **ACF 6.8.2** + **CPT-UI 1.19.2** |
| CRM / marketing | **HubSpot** via **LeadIn 11.3.45** + custom `vt-hubspot-user-sync` 1.1.0 |
| Forms | **WPForms 1.10.1** |
| Video | **Presto Player 4.2.0** |
| Security / backup / cache | **Wordfence 8.2.2**, **WPVivid 0.9.128**, **LiteSpeed Cache 7.8.1** |
| Custom code | 2 custom plugins, 15 mu-plugins, 26 active WPCode snippets |

**Users:** 169 total — `um_vt-hired` 76, `um_vt-onpool` 43, `um_clients` 37, `administrator` 4, `um_csm` 3, `editor` 2, `subscriber` 2, `um_ambassador` 1.

⚠️ **Architectural note:** almost all custom behavior lives in **mu-plugins** and **WPCode snippets** (auto-inserted PHP), **not** in a child theme. Some snippet code is loaded via `eval()` from the database by the mu-plugins. The theme is stock and disposable.

---

## 1. Plugins

### Active (13)

| Plugin (slug) | Ver | What it does here |
|---|---|---|
| **Advanced Custom Fields** (`advanced-custom-fields`) | 6.8.2 | The structured data layer behind VA profiles (rates, badges, photos, HubSpot IDs). 4 field groups (§5). |
| **Custom Post Type UI** (`custom-post-type-ui`) | 1.19.2 | Registers the public profile CPT `vt-list-by-category`. (Other CPTs are registered in code.) |
| **Elementor** (`elementor`) | 4.1.1 | Visual page builder for all marketing pages. |
| **Elementor Pro** (`elementor-pro`) | 4.0.1 | Theme Builder (headers/footers/dynamic profile templates), Pro widgets, popups. |
| **If Menu** (`if-menu`) | 0.19.2 | Conditional menu visibility by login state / role. |
| **LeadIn (HubSpot)** (`leadin`) | 11.3.45 | Official HubSpot: tracking script, form capture, CRM sync (OAuth). |
| **LiteSpeed Cache** (`litespeed-cache`) | 7.8.1 | Page caching + CSS/JS/image optimization. |
| **Presto Player** (`presto-player`) | 4.2.0 | Video player (VA intros, courses), incl. BunnyCDN/HLS. |
| **Ultimate Member** (`ultimate-member`) | 2.11.4 | Front-end login/registration/profiles + the role system (the portal). |
| **Virtual Teammate Plugin** (`vt-hubspot-user-sync`) | 1.1.0 | **The custom core.** HubSpot↔WP sync, client/VT/CSM provisioning, dashboard chat, notifications. §3. |
| **Wordfence** (`wordfence`) | 8.2.2 | Firewall, login protection, malware scan. |
| **WPForms** (`wpforms`) | 1.10.1 | Lead-capture forms. |
| **WPVivid Backup** (`wpvivid-backuprestore`) | 0.9.128 | Scheduled backups + site cloning/migration. |

### Installed but inactive (6)

| Plugin (slug) | Ver | Note |
|---|---|---|
| **Better Search Replace** (`better-search-replace`) | 1.4.10 | DB URL find/replace — run on demand to normalize any hardcoded dev URLs to `https://virtualteammate.com`. |
| **WPCode Lite** (`insert-headers-and-footers`) | 2.3.6 | ⚠️ The WPCode **snippets still exist and run** even though this manager plugin is inactive — the mu-plugins load snippet code directly. Don't assume "inactive" = snippets off. |
| **SQLite Object Cache** (`sqlite-object-cache`) | 1.6.3 | Optional persistent object cache; off here. |
| **VT HTML Site Importer** (`vt-html-site-importer`) | 1.0.0 | Custom one-off migration tool (§4). |
| **Wise Chat with AI** (`wise-chat`) | 3.4 | Inactive as a normal plugin, **but its engine runs as a mu-plugin** (`0-wise-chat-engine.php`). |
| **Asset CleanUp** (`wp-asset-clean-up`) | 1.4.0.4 | Per-page asset trimming; off (LiteSpeed handles this). |

---

## 2. Custom mu-plugins (always-active)

Location: `wp-content/mu-plugins/`. Must-use plugins load on every request and **cannot be deactivated from admin**. They load alphabetically (`0-` first, `zzz-` last). Custom code uses the **`codex_`** function prefix.

### 2.1 `0-wise-chat-engine.php` *(vendor)*
High-performance standalone AJAX endpoint for Wise Chat; inert unless `?wc-gold-engine` is set. ⚠️ Contains a **hardcoded absolute Windows filesystem path** to the Wise Chat `Loader.php` that breaks on a Linux host — must be repointed per environment.

### 2.2 `automation-by-installatron.php` *(vendor)*
Disables WP auto-updates + the related Site Health test (updates managed by the host's Installatron).

### 2.3 `client-dashboard-access.php` — **portal login/redirect router**
The central access controller. Detects login/dashboard requests, disables caching on them, gates which UM login form each role may use, and redirects logged-in users to their role dashboard.
- **Role → dashboard map:** clients → `/user-2/`; VTs (`um_vt-hired`/`um_vt-onpool`) → `/vt-profile/`; CSMs (`um_csm`) → `/csm-profile/`.
- **Login form-ID → allowed-role map** (hardcoded): `39`→subscriber/clients; `1131`→ambassador/vt-hired/vt-onpool; `1993`→vt-hired/vt-onpool; `1927`,`1636`→applicant; `2680`→wpseo_manager/csm.
- Swaps the logged-in header menu to "My Account / Log Out", rewrites legacy applicant-login menu items, renames post 1137 → "VT Hired Login".
- Disables UM login nonce verification; injects an autoplay-blocking script on login pages.

### 2.4 `client-resources-shortcode.php`
Render function for **`[client_resources]`** (a Downloads grid of client PDFs/worksheets). Tag registered by `dashboard-snippet-fallbacks.php`. Hardcoded Wix CDN PDF URLs + local cover images + CTA `/client-playbook-3/`.

### 2.5 `dashboard-roi-chat-fixes.php`
Two jobs: (1) on every front-end load, ensures `va_conversation` chat threads exist between a client and their linked VAs/CSMs/reviewer; (2) overrides the ROI calculator's `roix_get_contracts` AJAX (priority 1) to resolve the client's company from HubSpot user-meta and pull contracts from the HubSpot CRM. Requires `HUBSPOT_PRIVATE_APP_TOKEN`; hardcoded HubSpot custom object `2-31153232`.

### 2.6 `dashboard-snippet-fallbacks.php` — **the portal orchestrator (~12,500 lines)**
The heart of the client/VT/CSM portal. On `plugins_loaded` it:
- `require_once`s the four sibling shortcode files (`roi-savings-calculator`, `resource-hub`, `vtm-hr-hub`, `client-resources`) — **this is the only place those shortcodes get registered.**
- Re-executes inactive WPCode snippets by ID via **`eval()`** if their shortcode is missing (hardcoded IDs: `6106, 6402, 5956, 3278, 6026, 1551, 5957, 3028, 3269, 6020, 6021, 3099, 3500, 2196`).
- Registers ~25 portal shortcodes (each `remove_shortcode` first so PHP wins): `[vt_taskmanagement]`, `[vt_assignments]`, `[select_va_button]`, `[special_link_generator]`, `[vt_my_csm_vt]`, `[manager_va_dashboard]`, `[roi_savings_calculator]`, `[selected_va_list]`, `[vt_talent_pool]`, `[vt_request_pool]`, `[csm_workday_tracker]`, `[hubspot_invoice_viewer]`, `[workday_tracker_profiles]`, `[vtm_resource_hub_consolidated]`/`[vtm_help_center]`, `[vtm_hr_hub]`, `[client_resources]`, `[my_payslip]`, `[va_workday_tracker]`, `[va_booking_center]`, and more.
- Installs a custom table **`{prefix}codex_eod_reports`** (end-of-day reports), schedules a **daily EOD reminder cron** (`codex_eod_daily_reminder_event`), registers a booking CPT, and wires AJAX for VA selection, CSM approvals, EOD save, and CSM special links.
- **CSM special links:** token-based temporary talent-pool access (option `codex_csm_special_links`); invalid/expired tokens `wp_die` 410.
- Defines 210+ `codex_*` functions across EOD reports, talent pool, VA selection, booking, task management, payslips.

### 2.7 `elementor-safe-mode.php` *(vendor)*
Standard Elementor Safe Mode (editor-only isolation troubleshooting).

### 2.8 `resource-hub-shortcode.php`
Render function for the **Help Center** hub (`[vtm_resource_hub_consolidated]` / `[vtm_help_center]`) — searchable card grid linking to Knowledge Center, HR, Finance, IT, Marketing, Community. Community tile hardcoded to `https://virtualteammate.com/group-teammate-community-about/`.

### 2.9 `roi-savings-calculator-shortcode.php`
Backs **`[roi_savings_calculator]`** (the "Value Creation Calculator"). Resolves the client's company via HubSpot contact, then Google-Sheet fallbacks, then pulls VT contracts/deals from HubSpot. **Default original `roix_get_contracts` AJAX handler (priority 10).**
- ⚠️ **Hardcoded Google Sheet** `1sRpFUHuf5QR3avOnzRErDXGREwzdcJSwu7SfGU9_Nqk` (tabs `632565396` Clients, `1424706697` VT Accounts).
- ⚠️ A **commented-out live-looking HubSpot token** (`//$token = "pat-na1-77aafd22-…"`) is left in source — should be scrubbed.

### 2.10 `um-role-sync.php` — **role guarantor**
On every `init`, ensures the four UM custom roles exist as both WP roles and UM roles:
- `um_vt-hired` ("VT Hired"), `um_vt-onpool` ("VT Onpool"), `um_clients` ("Clients"), `um_csm` ("CSM").
- `um_csm` is granted the **`wpseo_manager`** capability and is treated as a "manager." Also retroactively promotes existing `wpseo_manager` users to `um_csm` (batched 200).

### 2.11 `vtm-dashboard-redirect.php`
Shortcode **`[vtm_dashboard_redirect]`** — put on a login page to bounce logged-in users to their role dashboard (server-side redirect + JS fallback). Defaults: client `/user-2/`, vt `/vt-profile/`, csm `/csm-profile/`.

### 2.12 `vtm-demo-account.php` — **demo data provisioner**
Class `VTM_Demo_Account` seeds a self-contained demo client (`demo@virtualteammate.com`) with fictional VTs, conversations, EOD reports, and mock ROI data, hidden from public listings. Admin-only AJAX (seed/reset/topup, cap `manage_options`). Overrides `roix_get_contracts` at **priority 0** (mock contracts for the demo client).
- ⚠️ **Plaintext demo credentials committed in source** — all accounts use password `demo12345` (`demo@`, `csm.demo@`, `onpool.demo@`, `maya.demo@`, `carlos.demo@`, `aisha.demo@virtualteammate.com`). Flagged for production.

### 2.13 `vtonpool-hide-tabs.php`
Shortcode **`[vtonpool_hidetabs]`** — for on-pool-only VTs, hides dashboard tabs (Payslips, Meetings, Workday Tracker, My Assigned Tasks, My CSM, Virtual Teammate) via CSS/JS + MutationObserver.

### 2.14 `vtm-hr-hub-shortcode.php`
Render function for **`[vtm_hr_hub]`** (HR Hub UI). Tag registered by the orchestrator.

### 2.15 `zzz-local-performance.php` — **development-only**
Entire file is a no-op unless the request comes from a local development host — it never runs on the live site. In a dev environment it seeds the PHP-version check transient, dequeues head clutter + unused UM/Presto assets, **blocks slow remote calls** (Elementor mixpanel/license, WordPress serve-happy), and rewrites Google Docs PDF viewers to direct URLs. No production effect.

### Cross-file gotchas
- **Three stacked `roix_get_contracts` AJAX handlers** by priority: demo (0) → HubSpot-meta override (1) → original Sheet-based (10). First to call `wp_send_json_*` wins.
- Shortcode registration is **centralized** in `dashboard-snippet-fallbacks.php`; if it's renamed/disabled, the client-resources/help-center/HR-hub/ROI shortcodes vanish.
- Multiple files `eval()` WPCode post content from the DB — a maintenance + security consideration.

---

## 3. The custom core: `vt-hubspot-user-sync` (v1.1.0)

Main file ~11,300 lines; class `VT_HubSpot_User_Sync` (static) + `VT_Dashboard_Chat` + `VT_Notification_Center`. Despite the name it does **four** jobs:

### 3.1 Talent sync (HubSpot contacts → public VA profiles)
`run_sync()` (also runs **hourly via cron** `vt_hubspot_user_sync_cron`):
1. Lock (`vt_hubspot_user_sync_lock`, 45 min) → resolve token → bail if missing.
2. `search_virtual_teammate_contacts()` — HubSpot contacts where `hs_lead_status = "Virtual Teammate"`, paginated.
3. `hydrate_contacts()` — batch-read extra properties.
4. Per contact → `sync_contact_to_user()`: map role from `vt_status` (`hired`/`contracted` → `um_vt-hired`; `unmatched/eligible`/`matched` → `um_vt-onpool`; `no longer eligible` → delete), upsert WP user, write meta, import profile photo.
5. `upsert_public_profile_posts()` → creates/updates **`vt-list`** (card) + **`vt-list-by-category`** (full profile) posts, imports media into a `vtmedia` uploads area.
6. Cleanup stale/duplicate posts + orphan media → purge caches.

### 3.2 Client sync (HubSpot companies → portal clients) — batched state machine
Stages (run incrementally via AJAX `ajax_client_sync_step`, state in options):
`init` → `hired_contacts` → `csm_owners` → `companies` → `inactive` → `cleanup_vtmedia` → `finalize`.
- Reads companies with lead status **"Client - Active"**, walks company→contact, company→CSM, and a custom **contract object `2-31153232`** (association type **28**).
- `sync_company_to_client_user()` provisions the client WP user (role `um_clients`) and links their VTs + CSMs.
- `deactivate_inactive_client_user()` handles "Client - Inactive" — detaches conversations, clears notifications, drops the contract cache row.
- Contract data cached in custom table **`vtcontracts`**.

There are parallel **batched talent sync** and **Purge VT Data** state machines (same stage pattern).

### 3.3 Dashboard tooling
- **`VT_Dashboard_Chat`** — front-end polling chat (client / assistant / manager) on `va_conversation` + `va_message`. Shortcodes `[va_chat_window]`, `[va_assistant_chat]`, `[va_manager_chat]`. ⚠️ Bootstraps by `eval()`-ing **WPCode snippets 2104 & 2105** if helper fns aren't defined.
- **`VT_Notification_Center`** — in-app + email notifications stored in user-meta `va_notifications`; shortcode `[va_notification_center]`. Email via native **`wp_mail()`**.
- Client meeting scheduling (`va_meeting`), "request additional VT" cards, assignment-roster sync (`vtm_assignment`).

### 3.4 Admin UI
**Users → VT HubSpot Sync** (cap `manage_options`). Tabs: Talents, Clients, Reset, **Purge VT Data** (destructive, batched), Sync Report, Demo Data.

### 3.5 Integrations & credentials
- **HubSpot CRM v3 REST** (`https://api.hubapi.com`, Bearer). Token resolution order: DB option `vt_hubspot_user_sync_settings['token']` → constant `HUBSPOT_PRIVATE_APP_TOKEN` → constant `HUBSPOT_PRIVATE_TOKEN`.
- **Google Drive** profile-photo fallback (public folder `1MBKL1oD0S01Ro-uIYzYAyE3un1qn7CJD`, no creds).
- **Email:** native `wp_mail()` only (no API).
- Hardcoded portal-specific IDs: contract object `2-31153232`, association type `28`, table `vtcontracts`, chat snippet IDs `2104`/`2105`.

---

## 4. `vt-html-site-importer` (v1.0.0, inactive)

One-off migration tool. **Tools → HTML Site Importer** (cap `manage_options`). Imports a static/PHP site (folder of `.html` or a `sitemap.xml`) into WP Pages: extracts `<main>`/`<article>` content, copies assets into `uploads/vt-imported-site/<slug>/` with URL rewriting, idempotent re-runs (matched by meta `_vt_html_import_source`), supports dry-run, status, slug prefix, host rewrite, max 200 pages. No REST/AJAX/cron; runs synchronously via `admin-post.php`. No hardcoded env values.

---

## 5. Data model

### Custom Post Types
**CPT-UI registers only one:** `vt-list-by-category` ("VT View Profiles") — the public VA profile directory. **All others are registered in code** (WPCode snippets / plugin):

| Post type | Count | Purpose |
|---|---|---|
| `pp_video_block` | 524 | Presto Player video blocks |
| `va_conversation` | 120 | Chat threads |
| `vt-list` | 117 | VA listing cards (internal companion) |
| `vt-list-by-category` | 117 | Public VA profiles |
| `va_message` | 42 | Chat messages |
| `vtm_assignment` | 16 | Client↔VA assignments |
| `um_form` | 13 | Ultimate Member forms |
| `va_meeting` | 3 | Scheduled meetings |
| `um_directory`, `va-profile`, `vt_showcase` | 1 each | Directory / legacy profile / showcase |
| `virtual_assistant` | (code) | Registered in WPCode snippet 2020 |

### ACF field groups (4)
- **VT Profile** (key `vt-profile`) — public profile fields: `department`, `country`, `skills`, `primary_roles`, 6 badge images (HIPAA, EF, DISC, predictive, AT, IQ) + captions.
- **View Profiles** (key `view-profiles`) — intake/editable: `profile_picture`, `name`, `department`, `summary`, `experience`, 5 badge slots, `upload_video`, `upload_resume`, `email`, `link_users` (links to WP user).
- **Account Sync** (key `account-sync`) — `enter_email`, `linked_user`, `va_manager_id`.
- **Business Selection for Visibility** (key `business-selection-for-visibility-purpose`) — `medical_niche` (radio); drives medical/non-medical content gating (snippet 5846).

### Custom DB tables
- `{prefix}codex_eod_reports` — end-of-day reports (created by the orchestrator mu-plugin).
- `{prefix}vtcontracts` — cached HubSpot contract data (created by the sync plugin on activation).

---

## 6. Roles & access

### Roles (WP `user_roles` option)
Standard: administrator, editor, author, contributor, subscriber.
Custom: `ambassador`, `applicant`, `prospect_viewer`, `vtm-admin` (VTM Admin), `vt-hired` (VT Hired), `vt-onpool` (VT Onpool), `clients` (Clients), `csm` (CSM).

> UM prefixes its role keys `um_` when applied to users (e.g. `um_vt-hired`). `um_csm` carries the `wpseo_manager` capability and is treated as a manager by `va_is_manager()`.

### Ultimate Member forms (login/register/profile)
Multiple login forms exist because each is gated to a role (WPCode snippet 3228 + the mu-plugin form→role map):

| ID | Form | Mode | Registers role |
|---|---|---|---|
| 38/39/40 | Default Register/Login/Profile | — | — |
| 1125/1131 | VT Profile / VT Login | profile/login | — |
| 1128 | VT Registration | register | `um_ambassador` |
| 1632/1636 | Applicant Register / Log In | register/login | — |
| 2680/2792/2794 | Manager Login / Profile / Register | — | `wpseo_manager` |
| 6285 | Admin login | login | — |

### Portal dashboards by role
- **Client** → `/user-2/` (login `/client-login/`)
- **VT (hired/onpool)** → `/vt-profile/` (login `/vt-login/`)
- **CSM** → `/csm-profile/` (login `/csm-log-in/`)
- **Manager/Admin** → manager dashboard / admin pages

---

## 7. WPCode snippets (custom code in the DB)

Stored as `post_type='wpcode'`, auto-inserted PHP. **26 published (active), 38 drafts (inactive — many duplicates/experiments).** ⚠️ The WPCode manager plugin is "inactive" but snippets still run (mu-plugins load them, some via `eval()`). Key active snippets:

| ID | Title | Role |
|---|---|---|
| 5846 | Business Niche Visibility | `body_class` filter for medical/non-medical content gating |
| 2086 | For Video Upload | `[acf_presto_video]` — BunnyCDN video |
| 6192 | Help Center (Courses) | `[vtm_help_center]` |
| 6183 | HR Content Help Center | `[vtm_hr_hub]` |
| 2109 | LightBox style video | `[vt_acf_video]` |
| 2020 | Loop Grid Filter | registers `virtual_assistant` CPT |
| 6402 | New ROI Calculator | `[roi_savings_calculator]` (the live one) |
| 2849 | Payslip and Report | `[admin_upload_payslip]` |
| 6020 | Refer a Friend | `[refer_a_friend]` + AJAX email |
| 6026 | Referral UI Client | `[vtm_referrals_tab]` |
| 5851 | Request To Join Modal | CTA modal + AJAX email |
| 5957 | Resources Tab | `[vtm_resource_hub_consolidated]` |
| 2071 | Resume Upload | embeds ACF resume PDF |
| 5666 | retrictclient | restricts `vt-page`, `careers` |
| 3228 | Role-based Login Restriction | maps UM login form → allowed role |
| 2104 | Select The VA | VA-assignment core (CPTs + AJAX) |
| 3797 | sharecommunityshortcode | `[share_group_popup]` ⚠️ contains a hardcoded dev URL — repoint to `virtualteammate.com` |
| 5692 | va dashboard tracker | `[va_workday_tracker]` |
| 6579 | VT Prefilled Profile | UM default avatar/cover from ACF |
| 6284 | VT Resume & Video Restriction | gates Presto video on VA pages |
| 3200 | Workday Request Shortcode | client workday-report requests + AJAX |
| 3278 | workdaytracker shortcode | `[workday_tracker_profiles]` (CSM-approval) |

---

## 8. Scheduled tasks (cron)

| Event | Schedule | Source | Action |
|---|---|---|---|
| `vt_hubspot_user_sync_cron` | hourly | sync plugin | Talent sync from HubSpot |
| `codex_eod_daily_reminder_event` | daily | orchestrator mu-plugin | EOD report reminder |

Client/talent/purge syncs are **AJAX-batched** (admin-triggered), not cron.

---

## 9. Integration & credential map

| Integration | Where config / credentials live |
|---|---|
| **HubSpot CRM** (custom sync) | DB option `vt_hubspot_user_sync_settings['token']` **or** wp-config constant `HUBSPOT_PRIVATE_APP_TOKEN` / `HUBSPOT_PRIVATE_TOKEN`. Portal-specific: custom object `2-31153232`, assoc type `28`. |
| **HubSpot** (LeadIn/tracking) | OAuth — options `leadin_portalId`, `leadin_refresh_token` (secret). |
| **Google Sheets** (ROI calc) | Hardcoded sheet `1sRpFUHuf5QR3avOnzRErDXGREwzdcJSwu7SfGU9_Nqk`. |
| **Google Drive** (photos) | Public folder `1MBKL1oD0S01Ro-uIYzYAyE3un1qn7CJD` (no creds). |
| **Email** | Native `wp_mail()` (server mail / SMTP). See §11. |
| **BunnyCDN** (video) | Referenced in ACF `upload_video` per profile. |

---

## 10. ⚠️ Known issues & handoff checklist

1. **URL cleanup:** use **Better Search Replace** to normalize any remaining hardcoded dev URLs → `https://virtualteammate.com` across the DB. (One bad mangled-URL variant has already been cleaned from posts + Elementor data.)
2. **Fix mangled values:** WPForms form 6524's broken recipient address; snippet 3797's hardcoded share link.
3. **Hardcoded Windows path:** `0-wise-chat-engine.php` references an absolute `C:\…` filesystem path that will break on a Linux host.
4. **Secrets in source:** scrub the commented HubSpot token in `roi-savings-calculator-shortcode.php`; rotate if it was ever real. Demo creds (`demo12345`) are plaintext in `vtm-demo-account.php`.
5. **No child theme:** the custom UM welcome email override (`hello-elementor/ultimate-member/email/welcome_email.php`, contains plaintext group-chat password `ClientVTonly`) will be **wiped on theme update**. Move to a child theme or plugin.
6. **`eval()` of DB snippets:** several mu-plugins execute WPCode post content via `eval()` — audit before trusting on production.

---

## 11. Email / SMTP

Portal notifications and UM emails send via WordPress **`wp_mail()`** from `support@`/`nricamora@virtualteammate.com`. The domain mailbox is **Google Workspace**, which requires an **App Password** for SMTP (regular password won't authenticate). See [the email setup note](#) — the active app password and SMTP host/port belong in the SMTP plugin or wp-config, never committed to a tracked file.
