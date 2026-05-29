-- Virtual Teammate portal — SQLite schema.
-- Mirrors the conceptual model of the WP portal (clients, CSMs, VTs,
-- assignments, meetings, EOD reports, audit) without WordPress' user/post-type
-- abstraction. All CREATEs are IF NOT EXISTS so install.php is idempotent.

PRAGMA foreign_keys = ON;
PRAGMA journal_mode = WAL;

CREATE TABLE IF NOT EXISTS users (
  id                   INTEGER PRIMARY KEY AUTOINCREMENT,
  email                TEXT    NOT NULL UNIQUE,
  password_hash        TEXT    NOT NULL,
  role                 TEXT    NOT NULL CHECK (role IN ('super_admin','client','csm','vt_hired','vt_onpool')),
  first_name           TEXT    NOT NULL DEFAULT '',
  last_name            TEXT    NOT NULL DEFAULT '',
  full_name            TEXT    NOT NULL DEFAULT '',
  phone                TEXT    NOT NULL DEFAULT '',
  country              TEXT    NOT NULL DEFAULT '',
  job_title            TEXT    NOT NULL DEFAULT '',
  photo_url            TEXT    NOT NULL DEFAULT '',
  photo_source_url     TEXT    NOT NULL DEFAULT '',
  cover_url            TEXT    NOT NULL DEFAULT '',
  hubspot_contact_id   TEXT    NOT NULL DEFAULT '',
  hubspot_owner_id     TEXT    NOT NULL DEFAULT '',
  vt_status            TEXT    NOT NULL DEFAULT '',
  hs_lead_status       TEXT    NOT NULL DEFAULT '',
  is_hired             INTEGER NOT NULL DEFAULT 0,
  assigned_reviewer_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
  active               INTEGER NOT NULL DEFAULT 1,
  last_login_at        TEXT,
  created_at           TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at           TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
  id                    INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id               INTEGER UNIQUE REFERENCES users(id) ON DELETE SET NULL,
  company_name          TEXT    NOT NULL,
  company_email         TEXT    NOT NULL DEFAULT '',
  company_domain        TEXT    NOT NULL DEFAULT '',
  billing_contact_email TEXT    NOT NULL DEFAULT '',
  contract_status       TEXT    NOT NULL DEFAULT 'active',
  workday_link          TEXT    NOT NULL DEFAULT '',
  notes                 TEXT    NOT NULL DEFAULT '',
  hubspot_company_id    TEXT    NOT NULL DEFAULT '',
  hubspot_owner_id      TEXT    NOT NULL DEFAULT '',
  created_at            TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at            TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vt_profiles (
  id                    INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id               INTEGER NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
  status                TEXT    NOT NULL DEFAULT 'onpool' CHECK (status IN ('onpool','hired')),
  department            TEXT    NOT NULL DEFAULT '',
  role_title            TEXT    NOT NULL DEFAULT '',
  experience_years      INTEGER NOT NULL DEFAULT 0,
  ehr_software          TEXT    NOT NULL DEFAULT '',
  english_level         TEXT    NOT NULL DEFAULT '',
  iq_band               TEXT    NOT NULL DEFAULT '',
  technical_band        TEXT    NOT NULL DEFAULT '',
  primary_skills        TEXT    NOT NULL DEFAULT '',
  predictive_index      TEXT    NOT NULL DEFAULT '',
  quiz_tier             TEXT    NOT NULL DEFAULT '',
  engagement_score      TEXT    NOT NULL DEFAULT '',
  predictive_contact_score TEXT NOT NULL DEFAULT '',
  personality_profile   TEXT    NOT NULL DEFAULT '',
  ci_role               TEXT    NOT NULL DEFAULT '',
  disc_profile          TEXT    NOT NULL DEFAULT '',
  hipaa_certified       TEXT    NOT NULL DEFAULT '',
  summary               TEXT    NOT NULL DEFAULT '',
  experience_text       TEXT    NOT NULL DEFAULT '',
  resume_url            TEXT    NOT NULL DEFAULT '',
  resume_source_url     TEXT    NOT NULL DEFAULT '',
  video_url             TEXT    NOT NULL DEFAULT '',
  video_source_url      TEXT    NOT NULL DEFAULT '',
  workday_tracker_id    TEXT    NOT NULL DEFAULT '',
  workday_link          TEXT    NOT NULL DEFAULT '',
  created_at            TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at            TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- HubSpot property audit trail. Every property pulled for a VT contact is
-- dumped here as a key/value row so we can debug mappings without re-fetching.
CREATE TABLE IF NOT EXISTS vt_profile_meta (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id       INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  meta_key      TEXT    NOT NULL,
  meta_value    TEXT,
  record_state  TEXT    NOT NULL DEFAULT 'active',
  created_at    TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (user_id, meta_key, record_state)
);

-- Company profile data attached to a client row. Adds the website/industry/
-- description fields we pull from the HubSpot company object.
CREATE TABLE IF NOT EXISTS company_profiles (
  id              INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id       INTEGER NOT NULL UNIQUE REFERENCES clients(id) ON DELETE CASCADE,
  website         TEXT    NOT NULL DEFAULT '',
  industry        TEXT    NOT NULL DEFAULT '',
  company_size    TEXT    NOT NULL DEFAULT '',
  description     TEXT    NOT NULL DEFAULT '',
  address         TEXT    NOT NULL DEFAULT '',
  city            TEXT    NOT NULL DEFAULT '',
  state           TEXT    NOT NULL DEFAULT '',
  record_state    TEXT    NOT NULL DEFAULT 'active',
  created_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS csm_clients (
  csm_user_id  INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  client_id    INTEGER NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
  assigned_at  TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (csm_user_id, client_id)
);

CREATE TABLE IF NOT EXISTS client_vts (
  client_id           INTEGER NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
  vt_user_id          INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  started_at          TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ended_at            TEXT,
  contract_status     TEXT    NOT NULL DEFAULT 'active',
  workday_tracker_id  TEXT    NOT NULL DEFAULT '',
  workday_link        TEXT    NOT NULL DEFAULT '',
  PRIMARY KEY (client_id, vt_user_id)
);

CREATE TABLE IF NOT EXISTS meetings (
  id                  INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id           INTEGER NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
  organizer_user_id   INTEGER NOT NULL REFERENCES users(id),
  attendee_user_id    INTEGER REFERENCES users(id) ON DELETE SET NULL,
  meeting_with_role   TEXT    NOT NULL CHECK (meeting_with_role IN ('csm','vt')),
  scheduled_at        TEXT    NOT NULL,
  duration_minutes    INTEGER NOT NULL DEFAULT 30,
  topic               TEXT    NOT NULL DEFAULT '',
  notes               TEXT    NOT NULL DEFAULT '',
  status              TEXT    NOT NULL DEFAULT 'scheduled' CHECK (status IN ('scheduled','completed','cancelled')),
  created_at          TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS eod_reports (
  id                  INTEGER PRIMARY KEY AUTOINCREMENT,
  vt_user_id          INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  report_date         TEXT    NOT NULL,
  best_work           TEXT    NOT NULL DEFAULT '',
  help_needed         TEXT    NOT NULL DEFAULT '',
  focus_next          TEXT    NOT NULL DEFAULT '',
  pending_waiting_on  TEXT    NOT NULL DEFAULT '',
  kpi_name            TEXT    NOT NULL DEFAULT '',
  kpi_target          TEXT    NOT NULL DEFAULT '',
  kpi_achieved        TEXT    NOT NULL DEFAULT '',
  created_at          TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (vt_user_id, report_date)
);

-- Tasks: lightweight to-do items a client (or CSM on a client's behalf) creates
-- and assigns to a specific VT (or leaves unassigned for the team). Modeled on
-- the staging dashboard's "Active Tasks / Completed Tasks" widget.
CREATE TABLE IF NOT EXISTS tasks (
  id              INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id       INTEGER NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
  assignee_user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
  created_by      INTEGER NOT NULL REFERENCES users(id),
  title           TEXT    NOT NULL,
  description     TEXT    NOT NULL DEFAULT '',
  priority        TEXT    NOT NULL DEFAULT 'normal' CHECK (priority IN ('low','normal','high','urgent')),
  status          TEXT    NOT NULL DEFAULT 'active' CHECK (status IN ('active','completed','cancelled')),
  due_date        TEXT,
  completed_at    TEXT,
  created_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Messages: 1:1 chat threads between portal users. conversation_key is a
-- canonical "smaller_id:larger_id" string so the same conversation is keyed
-- consistently regardless of which side sends first.
CREATE TABLE IF NOT EXISTS messages (
  id              INTEGER PRIMARY KEY AUTOINCREMENT,
  conversation_key TEXT   NOT NULL,
  sender_user_id  INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  receiver_user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  body            TEXT    NOT NULL,
  read_at         TEXT,
  created_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Notifications: per-user, simple bell-style dashboard items.
CREATE TABLE IF NOT EXISTS notifications (
  id              INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id         INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  kind            TEXT    NOT NULL DEFAULT 'info',
  title           TEXT    NOT NULL,
  body            TEXT    NOT NULL DEFAULT '',
  link            TEXT    NOT NULL DEFAULT '',
  read_at         TEXT,
  created_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Workday logs: a row per VT per work-day with start/end timestamps and a
-- minutes-worked total. Lets the client dashboard show recent-hours summary
-- without needing a live time-clock integration.
CREATE TABLE IF NOT EXISTS workday_logs (
  id              INTEGER PRIMARY KEY AUTOINCREMENT,
  vt_user_id      INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  client_id       INTEGER REFERENCES clients(id) ON DELETE SET NULL,
  work_date       TEXT    NOT NULL,
  started_at      TEXT,
  ended_at        TEXT,
  minutes         INTEGER NOT NULL DEFAULT 0,
  notes           TEXT    NOT NULL DEFAULT '',
  created_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (vt_user_id, work_date)
);

CREATE TABLE IF NOT EXISTS audit_log (
  id             INTEGER PRIMARY KEY AUTOINCREMENT,
  actor_user_id  INTEGER REFERENCES users(id) ON DELETE SET NULL,
  action         TEXT    NOT NULL,
  entity_type    TEXT    NOT NULL DEFAULT '',
  entity_id      INTEGER,
  details        TEXT    NOT NULL DEFAULT '',
  ip             TEXT    NOT NULL DEFAULT '',
  created_at     TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Generic key/value store for app-level settings (HubSpot token, sync filter values, etc.).
CREATE TABLE IF NOT EXISTS app_settings (
  key    TEXT PRIMARY KEY,
  value  TEXT NOT NULL DEFAULT '',
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Marketing-site traffic log (one row per pageview beacon).
CREATE TABLE IF NOT EXISTS traffic (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  path         TEXT    NOT NULL DEFAULT '',
  ip           TEXT    NOT NULL DEFAULT '',
  country      TEXT    NOT NULL DEFAULT '',
  region       TEXT    NOT NULL DEFAULT '',
  city         TEXT    NOT NULL DEFAULT '',
  user_agent   TEXT    NOT NULL DEFAULT '',
  referrer     TEXT    NOT NULL DEFAULT '',
  created_at   TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Per-IP geolocation cache so we don't re-hit the geo API for repeat visitors.
CREATE TABLE IF NOT EXISTS geo_cache (
  ip          TEXT PRIMARY KEY,
  country     TEXT NOT NULL DEFAULT '',
  region      TEXT NOT NULL DEFAULT '',
  city        TEXT NOT NULL DEFAULT '',
  lat         REAL,
  lon         REAL,
  resolved_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_traffic_created ON traffic(created_at);
CREATE INDEX IF NOT EXISTS idx_traffic_path    ON traffic(path);
CREATE INDEX IF NOT EXISTS idx_traffic_ip      ON traffic(ip);

CREATE INDEX IF NOT EXISTS idx_users_role           ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_hubspot_contact ON users(hubspot_contact_id);
CREATE INDEX IF NOT EXISTS idx_users_hubspot_owner   ON users(hubspot_owner_id);
CREATE INDEX IF NOT EXISTS idx_clients_user         ON clients(user_id);
CREATE INDEX IF NOT EXISTS idx_clients_hubspot_company ON clients(hubspot_company_id);
CREATE INDEX IF NOT EXISTS idx_clients_hubspot_owner ON clients(hubspot_owner_id);
CREATE INDEX IF NOT EXISTS idx_vt_status            ON vt_profiles(status);
CREATE INDEX IF NOT EXISTS idx_meetings_client      ON meetings(client_id);
CREATE INDEX IF NOT EXISTS idx_meetings_attendee    ON meetings(attendee_user_id);
CREATE INDEX IF NOT EXISTS idx_eod_vt               ON eod_reports(vt_user_id, report_date);
CREATE INDEX IF NOT EXISTS idx_audit_actor          ON audit_log(actor_user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_tasks_client         ON tasks(client_id, status);
CREATE INDEX IF NOT EXISTS idx_tasks_assignee       ON tasks(assignee_user_id, status);
CREATE INDEX IF NOT EXISTS idx_notifications_user   ON notifications(user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_workday_vt           ON workday_logs(vt_user_id, work_date);
CREATE INDEX IF NOT EXISTS idx_workday_client       ON workday_logs(client_id, work_date);
CREATE INDEX IF NOT EXISTS idx_messages_conv        ON messages(conversation_key, created_at);
CREATE INDEX IF NOT EXISTS idx_messages_receiver    ON messages(receiver_user_id, read_at);
