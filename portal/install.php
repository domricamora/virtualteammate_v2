<?php
/**
 * One-shot portal installer.
 *
 * - Creates data/portal.sqlite if missing and applies schema.sql.
 * - Seeds nricamora@virtualteammate.com as super_admin with a generated
 *   20-char password — shown once in the browser + written to
 *   data/SUPERADMIN_CREDENTIALS.txt (gitignored, web-blocked).
 *
 * Idempotent: re-running on an existing DB will NOT change the super admin
 * password. Delete data/portal.sqlite to start fresh.
 */

require __DIR__ . '/bootstrap.php';

const SUPER_ADMIN_EMAIL = 'nricamora@virtualteammate.com';

$messages = [];
$dbPath   = PORTAL_DB_PATH;
$dataDir  = dirname($dbPath);

if (!is_dir($dataDir) && !@mkdir($dataDir, 0775, true) && !is_dir($dataDir)) {
    die('Could not create data directory: ' . e($dataDir));
}

$freshDb = !file_exists($dbPath);

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');

$schemaSql = file_get_contents(PORTAL_SCHEMA);
if ($schemaSql === false) {
    die('Could not read schema.sql at ' . e(PORTAL_SCHEMA));
}
$pdo->exec($schemaSql);
$messages[] = $freshDb
    ? 'Created data/portal.sqlite and applied schema.'
    : 'Schema verified (no destructive changes).';

/* ── Rebuild users.role CHECK to allow vt_ci + vt_onboarded (idempotent).
   SQLite can't ALTER a CHECK constraint in place, so we detect the old
   CHECK via PRAGMA table_info and recreate when needed. Safe no-op when
   the constraint is already current. */
$usersTableSql = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'")->fetchColumn();
// Trigger rebuild if any of the new columns are missing (signals an old schema)
// OR if the CHECK still contains vt_onboarded (legacy state we no longer want).
$usersNeedsRebuild = $usersTableSql && (
    strpos((string) $usersTableSql, 'full_name') === false ||
    strpos((string) $usersTableSql, 'vt_onboarded') !== false
);
if ($usersNeedsRebuild) {
    $cols = $pdo->query('PRAGMA table_info(users)')->fetchAll(PDO::FETCH_ASSOC);
    $names = array_column($cols, 'name');
    $colList = implode(', ', array_map(fn($n) => '"' . $n . '"', $names));
    $pdo->exec('PRAGMA foreign_keys = OFF');
    // legacy_alter_table = ON prevents SQLite from rewriting child-table FK
    // references when we RENAME away the table being rebuilt. Without this,
    // every FK pointing at users gets silently rewritten to `users_old`.
    $pdo->exec('PRAGMA legacy_alter_table = ON');
    $pdo->beginTransaction();
    try {
        $pdo->exec('ALTER TABLE users RENAME TO users_old');
        $pdo->exec(
            'CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                role TEXT NOT NULL CHECK (role IN ("super_admin","client","csm","vt_hired","vt_onpool")),
                first_name TEXT NOT NULL DEFAULT "",
                last_name TEXT NOT NULL DEFAULT "",
                full_name TEXT NOT NULL DEFAULT "",
                phone TEXT NOT NULL DEFAULT "",
                country TEXT NOT NULL DEFAULT "",
                job_title TEXT NOT NULL DEFAULT "",
                photo_url TEXT NOT NULL DEFAULT "",
                photo_source_url TEXT NOT NULL DEFAULT "",
                cover_url TEXT NOT NULL DEFAULT "",
                hubspot_contact_id TEXT NOT NULL DEFAULT "",
                hubspot_owner_id TEXT NOT NULL DEFAULT "",
                vt_status TEXT NOT NULL DEFAULT "",
                hs_lead_status TEXT NOT NULL DEFAULT "",
                is_hired INTEGER NOT NULL DEFAULT 0,
                assigned_reviewer_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                active INTEGER NOT NULL DEFAULT 1,
                last_login_at TEXT,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );
        $pdo->exec("INSERT INTO users ($colList) SELECT $colList FROM users_old");
        $pdo->exec('DROP TABLE users_old');
        $pdo->commit();
        // Defensive: even with legacy_alter_table ON, some SQLite builds still
        // rewrite a few FK refs. Sweep sqlite_master and undo any leakage.
        $pdo->exec('PRAGMA writable_schema = ON');
        $pdo->exec(
            "UPDATE sqlite_master SET sql = REPLACE(sql, '\"users_old\"', 'users')
             WHERE sql LIKE '%users_old%' AND name != 'users_old'"
        );
        $pdo->exec(
            "UPDATE sqlite_master SET sql = REPLACE(sql, 'users_old', 'users')
             WHERE sql LIKE '%users_old%' AND name != 'users_old'"
        );
        $pdo->exec('PRAGMA writable_schema = OFF');
        $messages[] = 'Rebuilt users table CHECK + repaired child-table FK refs.';
    } catch (Throwable $ex) {
        $pdo->rollBack();
        $messages[] = 'Skip users CHECK rebuild: ' . $ex->getMessage();
    }
    $pdo->exec('PRAGMA legacy_alter_table = OFF');
    $pdo->exec('PRAGMA foreign_keys = ON');
}

/* ── Idempotent additive migrations (safe to run repeatedly) ── */
$migrations = [
    'users'       => [
        'hubspot_contact_id   TEXT NOT NULL DEFAULT ""',
        'hubspot_owner_id     TEXT NOT NULL DEFAULT ""',
        'photo_source_url     TEXT NOT NULL DEFAULT ""',
        'cover_url            TEXT NOT NULL DEFAULT ""',
        'full_name            TEXT NOT NULL DEFAULT ""',
        'job_title            TEXT NOT NULL DEFAULT ""',
        'vt_status            TEXT NOT NULL DEFAULT ""',
        'hs_lead_status       TEXT NOT NULL DEFAULT ""',
        'is_hired             INTEGER NOT NULL DEFAULT 0',
        'assigned_reviewer_id INTEGER',
        // Email notifications. 1 = on (default), 0 = off. When on, notify()
        // also sends the branded email. Users can opt out per-account.
        'notify_by_email      INTEGER NOT NULL DEFAULT 1',
    ],
    'clients'     => [
        'hubspot_company_id TEXT NOT NULL DEFAULT ""',
        'hubspot_owner_id   TEXT NOT NULL DEFAULT ""',
    ],
    'vt_profiles' => [
        'resume_source_url        TEXT NOT NULL DEFAULT ""',
        'video_source_url         TEXT NOT NULL DEFAULT ""',
        'primary_skills           TEXT NOT NULL DEFAULT ""',
        'predictive_index         TEXT NOT NULL DEFAULT ""',
        'quiz_tier                TEXT NOT NULL DEFAULT ""',
        'engagement_score         TEXT NOT NULL DEFAULT ""',
        'predictive_contact_score TEXT NOT NULL DEFAULT ""',
        'personality_profile      TEXT NOT NULL DEFAULT ""',
        'ci_role                  TEXT NOT NULL DEFAULT ""',
        'disc_profile             TEXT NOT NULL DEFAULT ""',
        'hipaa_certified          TEXT NOT NULL DEFAULT ""',
    ],
    'meetings'    => [
        'end_at        TEXT NOT NULL DEFAULT ""',
        'meeting_link  TEXT NOT NULL DEFAULT ""',
        'call_app      TEXT NOT NULL DEFAULT "zoom"',
    ],
];
foreach ($migrations as $table => $columns) {
    $existing = [];
    foreach ($pdo->query("PRAGMA table_info($table)") as $row) {
        $existing[] = $row['name'];
    }
    foreach ($columns as $columnSql) {
        $colName = strtok($columnSql, ' ');
        if (!in_array($colName, $existing, true)) {
            try {
                $pdo->exec("ALTER TABLE $table ADD COLUMN $columnSql");
                $messages[] = "Added column $table.$colName.";
            } catch (Throwable $ex) {
                $messages[] = "Skip $table.$colName: " . $ex->getMessage();
            }
        }
    }
}
// One-time: turn email notifications ON for all existing users. Guarded by an
// app_settings flag so it runs exactly once — re-running the installer won't
// re-enable anyone who has since opted out.
try {
    $done = $pdo->query("SELECT value FROM app_settings WHERE key = 'mig_email_notify_default_on'")->fetchColumn();
    if (!$done) {
        $n = $pdo->exec('UPDATE users SET notify_by_email = 1 WHERE notify_by_email = 0');
        $pdo->prepare(
            "INSERT INTO app_settings (key, value, updated_at) VALUES ('mig_email_notify_default_on', '1', CURRENT_TIMESTAMP)
             ON CONFLICT(key) DO UPDATE SET value = '1', updated_at = CURRENT_TIMESTAMP"
        )->execute();
        $messages[] = "Enabled email notifications for {$n} existing user(s).";
    }
} catch (Throwable $ex) {
    $messages[] = 'Skip email-notify backfill: ' . $ex->getMessage();
}

// Indexes for the HubSpot ID columns (idempotent).
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_users_hubspot_contact   ON users(hubspot_contact_id)');
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_users_hubspot_owner    ON users(hubspot_owner_id)');
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_clients_hubspot_company ON clients(hubspot_company_id)');
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_clients_hubspot_owner  ON clients(hubspot_owner_id)');

// Audit / profile-meta + company_profiles tables for vtadmin-parity sync.
$pdo->exec("
CREATE TABLE IF NOT EXISTS vt_profile_meta (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  meta_key TEXT NOT NULL,
  meta_value TEXT,
  record_state TEXT NOT NULL DEFAULT 'active',
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (user_id, meta_key, record_state)
);
CREATE INDEX IF NOT EXISTS idx_vt_profile_meta_user ON vt_profile_meta(user_id, meta_key);
CREATE TABLE IF NOT EXISTS company_profiles (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id INTEGER NOT NULL UNIQUE REFERENCES clients(id) ON DELETE CASCADE,
  website TEXT NOT NULL DEFAULT '',
  industry TEXT NOT NULL DEFAULT '',
  company_size TEXT NOT NULL DEFAULT '',
  description TEXT NOT NULL DEFAULT '',
  address TEXT NOT NULL DEFAULT '',
  city TEXT NOT NULL DEFAULT '',
  state TEXT NOT NULL DEFAULT '',
  record_state TEXT NOT NULL DEFAULT 'active',
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
");
// Client-dashboard feature tables (idempotent — schema.sql already declares them
// for fresh installs; this exec re-runs the CREATE TABLE IF NOT EXISTS lines
// on upgrades from a pre-feature DB).
$pdo->exec("
CREATE TABLE IF NOT EXISTS tasks (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id INTEGER NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
  assignee_user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
  created_by INTEGER NOT NULL REFERENCES users(id),
  title TEXT NOT NULL,
  description TEXT NOT NULL DEFAULT '',
  priority TEXT NOT NULL DEFAULT 'normal' CHECK (priority IN ('low','normal','high','urgent')),
  status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active','completed','cancelled')),
  due_date TEXT,
  completed_at TEXT,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS notifications (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  kind TEXT NOT NULL DEFAULT 'info',
  title TEXT NOT NULL,
  body TEXT NOT NULL DEFAULT '',
  link TEXT NOT NULL DEFAULT '',
  read_at TEXT,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS workday_logs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  vt_user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  client_id INTEGER REFERENCES clients(id) ON DELETE SET NULL,
  work_date TEXT NOT NULL,
  started_at TEXT,
  ended_at TEXT,
  minutes INTEGER NOT NULL DEFAULT 0,
  notes TEXT NOT NULL DEFAULT '',
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (vt_user_id, work_date)
);
CREATE INDEX IF NOT EXISTS idx_tasks_client       ON tasks(client_id, status);
CREATE INDEX IF NOT EXISTS idx_tasks_assignee     ON tasks(assignee_user_id, status);
CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_workday_vt         ON workday_logs(vt_user_id, work_date);
CREATE INDEX IF NOT EXISTS idx_workday_client     ON workday_logs(client_id, work_date);
CREATE TABLE IF NOT EXISTS messages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  conversation_key TEXT NOT NULL,
  sender_user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  receiver_user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  body TEXT NOT NULL,
  read_at TEXT,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_messages_conv     ON messages(conversation_key, created_at);
CREATE INDEX IF NOT EXISTS idx_messages_receiver ON messages(receiver_user_id, read_at);
CREATE TABLE IF NOT EXISTS meeting_attendees (
  meeting_id INTEGER NOT NULL REFERENCES meetings(id) ON DELETE CASCADE,
  user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  invited_at TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (meeting_id, user_id)
);
CREATE INDEX IF NOT EXISTS idx_meeting_attendees_user ON meeting_attendees(user_id);
-- Backfill: copy the legacy single attendee into meeting_attendees for any
-- meeting whose row isn't already represented. Safe to re-run.
INSERT OR IGNORE INTO meeting_attendees (meeting_id, user_id, invited_at)
SELECT m.id, m.attendee_user_id, m.created_at
FROM meetings m
WHERE m.attendee_user_id IS NOT NULL;
CREATE TABLE IF NOT EXISTS task_attachments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  task_id INTEGER NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
  uploaded_by INTEGER NOT NULL REFERENCES users(id),
  original_name TEXT NOT NULL,
  ext TEXT NOT NULL,
  mime TEXT NOT NULL DEFAULT '',
  size_bytes INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_task_attachments_task ON task_attachments(task_id);
");

/* ── tasks.client_id: legacy schemas declared NOT NULL. Rebuild the table
   so super_admin can create cross-client tasks (client_id = NULL). Same
   FK-rewrite caveat as the users rebuild → wrap in legacy_alter_table. */
$tasksTableSql = (string) $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='tasks'")->fetchColumn();
if ($tasksTableSql !== '' && preg_match('#client_id\s+INTEGER\s+NOT\s+NULL#i', $tasksTableSql)) {
    $cols = $pdo->query('PRAGMA table_info(tasks)')->fetchAll(PDO::FETCH_ASSOC);
    $names = array_column($cols, 'name');
    $colList = implode(', ', array_map(fn($n) => '"' . $n . '"', $names));
    $pdo->exec('PRAGMA foreign_keys = OFF');
    $pdo->exec('PRAGMA legacy_alter_table = ON');
    $pdo->beginTransaction();
    try {
        $pdo->exec('ALTER TABLE tasks RENAME TO tasks_old');
        $pdo->exec(
            "CREATE TABLE tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER REFERENCES clients(id) ON DELETE CASCADE,
                assignee_user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                created_by INTEGER NOT NULL REFERENCES users(id),
                title TEXT NOT NULL,
                description TEXT NOT NULL DEFAULT '',
                priority TEXT NOT NULL DEFAULT 'normal' CHECK (priority IN ('low','normal','high','urgent')),
                status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active','completed','cancelled')),
                due_date TEXT,
                completed_at TEXT,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )"
        );
        $pdo->exec("INSERT INTO tasks ($colList) SELECT $colList FROM tasks_old");
        $pdo->exec('DROP TABLE tasks_old');
        $pdo->commit();
        $pdo->exec('PRAGMA writable_schema = ON');
        $pdo->exec("UPDATE sqlite_master SET sql = REPLACE(sql, 'tasks_old', 'tasks') WHERE sql LIKE '%tasks_old%' AND name != 'tasks_old'");
        $pdo->exec('PRAGMA writable_schema = OFF');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_tasks_client   ON tasks(client_id, status)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_tasks_assignee ON tasks(assignee_user_id, status)');
        $messages[] = 'Rebuilt tasks table to allow NULL client_id (cross-client super-admin tasks).';
    } catch (Throwable $ex) {
        $pdo->rollBack();
        $messages[] = 'Skip tasks rebuild: ' . $ex->getMessage();
    }
    $pdo->exec('PRAGMA legacy_alter_table = OFF');
    $pdo->exec('PRAGMA foreign_keys = ON');
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
$stmt->execute([':e' => SUPER_ADMIN_EMAIL]);
$existing = $stmt->fetchColumn();

$generatedPassword = null;

if ($existing) {
    $messages[] = 'Super admin already exists (id=' . (int) $existing . '). Password unchanged.';
} else {
    $generatedPassword = generate_password(20);
    $insert = $pdo->prepare(
        'INSERT INTO users (email, password_hash, role, first_name, last_name, active)
         VALUES (:email, :hash, :role, :fn, :ln, 1)'
    );
    $insert->execute([
        ':email' => SUPER_ADMIN_EMAIL,
        ':hash'  => password_hash($generatedPassword, PASSWORD_DEFAULT),
        ':role'  => 'super_admin',
        ':fn'    => 'Super',
        ':ln'    => 'Admin',
    ]);
    $messages[] = 'Created super admin account.';

    $credsFile = $dataDir . '/SUPERADMIN_CREDENTIALS.txt';
    $payload   = "Virtual Teammate portal — super admin credentials\n"
               . str_repeat('=', 60) . "\n\n"
               . "Email:    " . SUPER_ADMIN_EMAIL . "\n"
               . "Password: " . $generatedPassword . "\n\n"
               . "Generated: " . date('c') . "\n"
               . "Delete this file after you've stored the password securely.\n";
    @file_put_contents($credsFile, $payload);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>VT Portal — Installer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="assets/portal.css">
</head>
<body class="portal-naked">
<main class="naked-card">
  <header class="naked-h">
    <div class="naked-eyebrow">VT Portal</div>
    <h1>Installer</h1>
    <p class="naked-sub">One-shot setup. Safe to re-run; the super admin password is only generated on the first run.</p>
  </header>

  <section class="naked-section">
    <h2>What just happened</h2>
    <ul class="install-log">
      <?php foreach ($messages as $m): ?>
        <li><?= e($m) ?></li>
      <?php endforeach; ?>
    </ul>
  </section>

  <?php if ($generatedPassword): ?>
    <section class="naked-section install-creds">
      <h2>Save these credentials now</h2>
      <p class="install-warn">
        Shown <strong>once</strong>. They've also been written to
        <code>data/SUPERADMIN_CREDENTIALS.txt</code> (gitignored, web-blocked).
        Delete that file after storing the password somewhere safe.
      </p>
      <div class="creds-row">
        <div class="creds-cell">
          <span class="lbl">Email</span>
          <code><?= e(SUPER_ADMIN_EMAIL) ?></code>
        </div>
        <div class="creds-cell">
          <span class="lbl">Password</span>
          <code><?= e($generatedPassword) ?></code>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <footer class="naked-foot">
    <a href="index.php?p=login" class="btn-portal-primary">Go to login &rarr;</a>
    <a href="<?= e(site_url()) ?>" class="naked-back">&larr; Back to marketing site</a>
  </footer>
</main>
</body>
</html>
