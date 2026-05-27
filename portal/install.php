<?php
/**
 * One-shot portal installer.
 *
 * - Creates data/portal.sqlite if missing and applies schema.sql.
 * - Seeds aiagent@virtualteammate.com as super_admin with a generated
 *   20-char password — shown once in the browser + written to
 *   data/SUPERADMIN_CREDENTIALS.txt (gitignored, web-blocked).
 *
 * Idempotent: re-running on an existing DB will NOT change the super admin
 * password. Delete data/portal.sqlite to start fresh.
 */

require __DIR__ . '/bootstrap.php';

const SUPER_ADMIN_EMAIL = 'aiagent@virtualteammate.com';

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

/* ── Idempotent additive migrations (safe to run repeatedly) ── */
$migrations = [
    'users'   => ['hubspot_contact_id TEXT NOT NULL DEFAULT ""'],
    'clients' => ['hubspot_company_id TEXT NOT NULL DEFAULT ""'],
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
// Indexes for the new HubSpot columns (idempotent).
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_users_hubspot_contact   ON users(hubspot_contact_id)');
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_clients_hubspot_company ON clients(hubspot_company_id)');

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
