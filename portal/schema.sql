-- Virtual Teammate portal — SQLite schema.
-- Mirrors the conceptual model of the WP portal (clients, CSMs, VTs,
-- assignments, meetings, EOD reports, audit) without WordPress' user/post-type
-- abstraction. All CREATEs are IF NOT EXISTS so install.php is idempotent.

PRAGMA foreign_keys = ON;
PRAGMA journal_mode = WAL;

CREATE TABLE IF NOT EXISTS users (
  id              INTEGER PRIMARY KEY AUTOINCREMENT,
  email           TEXT    NOT NULL UNIQUE,
  password_hash   TEXT    NOT NULL,
  role            TEXT    NOT NULL CHECK (role IN ('super_admin','client','csm','vt_hired','vt_onpool')),
  first_name      TEXT    NOT NULL DEFAULT '',
  last_name       TEXT    NOT NULL DEFAULT '',
  phone           TEXT    NOT NULL DEFAULT '',
  country         TEXT    NOT NULL DEFAULT '',
  photo_url       TEXT    NOT NULL DEFAULT '',
  active          INTEGER NOT NULL DEFAULT 1,
  last_login_at   TEXT,
  created_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  summary               TEXT    NOT NULL DEFAULT '',
  experience_text       TEXT    NOT NULL DEFAULT '',
  resume_url            TEXT    NOT NULL DEFAULT '',
  video_url             TEXT    NOT NULL DEFAULT '',
  workday_tracker_id    TEXT    NOT NULL DEFAULT '',
  workday_link          TEXT    NOT NULL DEFAULT '',
  created_at            TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at            TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
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

CREATE INDEX IF NOT EXISTS idx_users_role        ON users(role);
CREATE INDEX IF NOT EXISTS idx_clients_user      ON clients(user_id);
CREATE INDEX IF NOT EXISTS idx_vt_status         ON vt_profiles(status);
CREATE INDEX IF NOT EXISTS idx_meetings_client   ON meetings(client_id);
CREATE INDEX IF NOT EXISTS idx_meetings_attendee ON meetings(attendee_user_id);
CREATE INDEX IF NOT EXISTS idx_eod_vt            ON eod_reports(vt_user_id, report_date);
CREATE INDEX IF NOT EXISTS idx_audit_actor       ON audit_log(actor_user_id, created_at);
