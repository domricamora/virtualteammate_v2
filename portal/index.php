<?php
/**
 * Portal front controller.
 *
 * Dispatches `?p=action` to a handler. Each handler:
 *   - enforces auth/role,
 *   - if POST, csrf_verify() + persist + audit_log() + flash + redirect,
 *   - otherwise queries the DB and renders a view.
 */

require __DIR__ . '/bootstrap.php';

$action = isset($_GET['p']) ? (string) $_GET['p'] : 'dashboard';

switch ($action) {
    /* ───────────────────────── Auth ─────────────────────────── */
    case 'login':           handle_login();              break;
    case 'logout':          handle_logout();             break;

    /* ───────────────────────── Dashboards ─────────────────────────── */
    case 'dashboard':       handle_dashboard();          break;

    /* ───────────────────────── Self ─────────────────────────── */
    case 'profile':         handle_profile();            break;
    case 'password':        handle_password_change();    break;

    /* ───────────────────────── Super admin CRUD ─────────────────────────── */
    case 'users':           handle_users_list();         break;
    case 'users.edit':      handle_users_edit();         break;
    case 'users.delete':    handle_users_delete();       break;

    case 'clients':         handle_clients_list();       break;
    case 'clients.edit':    handle_clients_edit();       break;
    case 'clients.delete':  handle_clients_delete();     break;

    case 'vts':             handle_vts_list();           break;
    case 'vts.edit':        handle_vts_edit();           break;
    case 'vts.delete':      handle_vts_delete();         break;

    case 'assignments':         handle_assignments_view();        break;
    case 'assignments.csm':     handle_assignment_csm_toggle();   break;
    case 'assignments.vt':      handle_assignment_vt_toggle();    break;

    /* ───────────────────────── Shared ─────────────────────────── */
    case 'meetings':        handle_meetings_list();      break;
    case 'meetings.edit':   handle_meetings_edit();      break;
    case 'meetings.delete': handle_meetings_delete();    break;

    case 'eod':             handle_eod_list();           break;
    case 'eod.edit':        handle_eod_edit();           break;
    case 'eod.delete':      handle_eod_delete();         break;

    case 'audit':           handle_audit_list();         break;
    case 'traffic':         handle_traffic_list();       break;

    /* ───────────────────────── HubSpot sync (super admin) ─────────────────────────── */
    case 'hubspot':                handle_hubspot_page();            break;
    case 'hubspot.save_settings':  handle_hubspot_save_settings();   break;
    case 'hubspot.test':           handle_hubspot_test();            break;
    case 'hubspot.control':        handle_hubspot_control();         break;
    case 'hubspot.step':           handle_hubspot_step();            break;
    case 'hubspot.purge':          handle_hubspot_purge();           break;
    /* Two-pipeline endpoints (talent + client). Each runs independently
       with its own pause/resume/reset and step polling. */
    case 'hubspot.talent_control': handle_hubspot_talent_control();  break;
    case 'hubspot.talent_step':    handle_hubspot_talent_step();     break;
    case 'hubspot.client_control': handle_hubspot_client_control();  break;
    case 'hubspot.client_step':    handle_hubspot_client_step();     break;
    /* Single-fetch endpoints — search HubSpot + sync one record by id. */
    case 'hubspot.talent_search':  handle_hubspot_talent_search();   break;
    case 'hubspot.talent_sync_one':handle_hubspot_talent_sync_one(); break;
    case 'hubspot.client_search':  handle_hubspot_client_search();   break;
    case 'hubspot.client_sync_one':handle_hubspot_client_sync_one(); break;
    case 'hubspot.seed_demo':      handle_hubspot_seed_demo();       break;
    case 'hubspot.purge_all':      handle_hubspot_purge_all();       break;

    /* ───────────────────────── CSMs (super admin) ─────────────────────────── */
    case 'csms':                   handle_csms_list();               break;
    case 'csms.view':              handle_csms_view();               break;

    /* ───────────────────────── Relationships (super admin) ─────────────────────────── */
    case 'relationships':          handle_relationships();           break;

    /* ───────────────────────── Detail views ─────────────────────────── */
    case 'vts.view':               handle_vts_view();                break;
    case 'clients.view':           handle_clients_view();            break;

    /* ───────────────────────── Client dashboard features ─────────────────────────── */
    case 'tasks':                  handle_tasks_list();              break;
    case 'tasks.edit':             handle_tasks_edit();              break;
    case 'tasks.toggle':           handle_tasks_toggle();            break;
    case 'tasks.delete':           handle_tasks_delete();            break;
    case 'workday':                handle_workday_list();            break;
    case 'notifications':          handle_notifications_list();      break;
    case 'notifications.read':     handle_notifications_read();      break;
    case 'resources':              handle_resources();               break;
    case 'my-vts':                 handle_my_vts();                  break;
    case 'messages':               handle_messages_list();           break;
    case 'messages.send':          handle_messages_send();           break;
    case 'productivity':           handle_productivity();            break;
    case 'avatar':                 handle_avatar_serve();            break;

    /* ───────────────────────── Media serve (auth-gated) ─────────────────────────── */
    case 'media':                  handle_media_serve();             break;

    default:
        http_response_code(404);
        render('error', ['title' => 'Not found', 'message' => 'Unknown action: ' . e($action)]);
        break;
}

/* ═════════════════════════════════════════════════════════════════════════
 * AUTH
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_login(): void
{
    if (current_user()) {
        redirect(portal_url('dashboard'));
    }

    $error = '';
    $email = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $email = trim((string) ($_POST['email'] ?? ''));
        $pw    = (string) ($_POST['password'] ?? '');
        $next  = (string) ($_POST['next'] ?? '');

        $stmt = db()->prepare('SELECT * FROM users WHERE email = :email AND active = 1');
        $stmt->execute([':email' => $email]);
        $u = $stmt->fetch();

        if ($u && password_verify($pw, $u['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['uid']  = (int) $u['id'];
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            db()->prepare('UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id')
                ->execute([':id' => $u['id']]);
            audit_log('login', 'user', (int) $u['id']);
            flash('success', 'Welcome back, ' . e(user_display_name($u)) . '.');
            redirect($next !== '' && str_starts_with($next, '/') ? $next : portal_url('dashboard'));
        } else {
            $error = 'Invalid email or password.';
            audit_log('login_failed', 'user', null, 'email=' . $email);
        }
    }

    $next = (string) ($_GET['next'] ?? '');
    render('login', ['_naked' => true, 'error' => $error, 'email' => $email, 'next' => $next]);
}

function handle_logout(): void
{
    $u = current_user();
    if ($u) {
        audit_log('logout', 'user', (int) $u['id']);
    }
    $_SESSION = [];
    if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    redirect(portal_url('login'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * DASHBOARDS — role-aware
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_dashboard(): void
{
    $u = require_login();
    switch ($u['role']) {
        case 'super_admin': render('dashboard-super', ['user' => $u, 'stats' => dashboard_super_stats(), 'traffic' => dashboard_traffic_summary()]); break;
        case 'client':      render('dashboard-client', ['user' => $u, 'data' => dashboard_client_data($u)]); break;
        case 'csm':         render('dashboard-csm', ['user' => $u, 'data' => dashboard_csm_data($u)]); break;
        case 'vt_hired':
        case 'vt_onpool':   render('dashboard-vt', ['user' => $u, 'data' => dashboard_vt_data($u)]); break;
        default:            render('error', ['title' => 'Unknown role', 'message' => 'No dashboard for role ' . e($u['role'])]); break;
    }
}

function dashboard_super_stats(): array
{
    $pdo = db();
    $count = fn(string $sql, array $p = []) => (int) $pdo->prepare($sql)->execute($p) || true ? (int) $pdo->query($sql)->fetchColumn() : 0;
    return [
        'users_total'    => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'users_clients'  => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn(),
        'users_csm'      => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role='csm'")->fetchColumn(),
        'users_hired'    => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role='vt_hired'")->fetchColumn(),
        'users_onpool'   => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role='vt_onpool'")->fetchColumn(),
        'clients_active' => (int) $pdo->query("SELECT COUNT(*) FROM clients WHERE contract_status='active'")->fetchColumn(),
        'meetings_upcoming' => (int) $pdo->query("SELECT COUNT(*) FROM meetings WHERE status='scheduled' AND scheduled_at >= datetime('now')")->fetchColumn(),
        'eod_today'      => (int) $pdo->query("SELECT COUNT(*) FROM eod_reports WHERE report_date = date('now')")->fetchColumn(),
        'traffic_today'  => traffic_table_exists() ? (int) $pdo->query("SELECT COUNT(*) FROM traffic WHERE date(created_at) = date('now')")->fetchColumn() : 0,
        'traffic_7d'     => traffic_table_exists() ? (int) $pdo->query("SELECT COUNT(*) FROM traffic WHERE created_at >= datetime('now','-7 days')")->fetchColumn() : 0,
        'traffic_visitors_7d' => traffic_table_exists() ? (int) $pdo->query("SELECT COUNT(DISTINCT ip) FROM traffic WHERE created_at >= datetime('now','-7 days')")->fetchColumn() : 0,
    ];
}

/** True if the traffic table has been created (portal installed after the traffic feature). */
function traffic_table_exists(): bool
{
    static $exists = null;
    if ($exists === null) {
        $exists = (bool) db()->query("SELECT name FROM sqlite_master WHERE type='table' AND name='traffic'")->fetchColumn();
    }
    return $exists;
}

/** Recent + aggregate traffic for the dashboard panel. */
function dashboard_traffic_summary(): array
{
    if (!traffic_table_exists()) {
        return ['recent' => [], 'top_countries' => [], 'top_pages' => []];
    }
    $pdo = db();
    $recent = $pdo->query(
        'SELECT path, ip, country, region, city, user_agent, created_at FROM traffic ORDER BY id DESC LIMIT 10'
    )->fetchAll();
    $topCountries = $pdo->query(
        "SELECT CASE WHEN country='' THEN 'Unknown' ELSE country END AS country, COUNT(*) AS n
         FROM traffic WHERE created_at >= datetime('now','-30 days')
         GROUP BY country ORDER BY n DESC LIMIT 6"
    )->fetchAll();
    $topPages = $pdo->query(
        "SELECT CASE WHEN path='' THEN '/' ELSE path END AS path, COUNT(*) AS n
         FROM traffic WHERE created_at >= datetime('now','-30 days')
         GROUP BY path ORDER BY n DESC LIMIT 6"
    )->fetchAll();
    return ['recent' => $recent, 'top_countries' => $topCountries, 'top_pages' => $topPages];
}

function dashboard_client_data(array $u): array
{
    $client = db()->prepare('SELECT * FROM clients WHERE user_id = :uid');
    $client->execute([':uid' => $u['id']]);
    $client = $client->fetch();
    if (!$client) {
        return ['client' => null, 'vts' => [], 'csms' => [], 'meetings' => [], 'tasks_active' => [], 'tasks_done_recent' => [], 'eod_recent' => [], 'workday_week' => [], 'notifications' => []];
    }
    $cid = (int) $client['id'];

    $vts  = client_hired_vts($cid);
    $csms = client_csms($cid);

    $meet = db()->prepare('SELECT * FROM meetings WHERE client_id = :cid ORDER BY scheduled_at DESC LIMIT 5');
    $meet->execute([':cid' => $cid]);

    // Active tasks (open) and the 5 most-recently completed for context.
    $stmt = db()->prepare(
        "SELECT t.*, u.first_name AS a_fn, u.last_name AS a_ln, u.email AS a_email
         FROM tasks t LEFT JOIN users u ON u.id = t.assignee_user_id
         WHERE t.client_id = :cid AND t.status = 'active'
         ORDER BY CASE t.priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END,
                  IFNULL(t.due_date, '9999-12-31'), t.created_at DESC"
    );
    $stmt->execute([':cid' => $cid]);
    $tasksActive = $stmt->fetchAll();

    $stmt = db()->prepare(
        "SELECT t.*, u.first_name AS a_fn, u.last_name AS a_ln
         FROM tasks t LEFT JOIN users u ON u.id = t.assignee_user_id
         WHERE t.client_id = :cid AND t.status = 'completed'
         ORDER BY t.completed_at DESC LIMIT 5"
    );
    $stmt->execute([':cid' => $cid]);
    $tasksDone = $stmt->fetchAll();

    // Last 5 EOD reports from this client's VTs.
    $stmt = db()->prepare(
        "SELECT er.*, u.first_name, u.last_name, u.email
         FROM eod_reports er
         JOIN client_vts cv ON cv.vt_user_id = er.vt_user_id
         JOIN users u ON u.id = er.vt_user_id
         WHERE cv.client_id = :cid AND cv.contract_status = 'active'
         ORDER BY er.report_date DESC, er.created_at DESC LIMIT 5"
    );
    $stmt->execute([':cid' => $cid]);
    $eodRecent = $stmt->fetchAll();

    // This-week workday rollup per assigned VT (minutes since Monday).
    $stmt = db()->prepare(
        "SELECT wl.vt_user_id, SUM(wl.minutes) AS minutes, COUNT(*) AS days
         FROM workday_logs wl
         JOIN client_vts cv ON cv.vt_user_id = wl.vt_user_id AND cv.client_id = wl.client_id
         WHERE cv.client_id = :cid AND wl.work_date >= date('now','weekday 1','-7 days')
         GROUP BY wl.vt_user_id"
    );
    $stmt->execute([':cid' => $cid]);
    $weekRows = $stmt->fetchAll();
    $weekByVt = [];
    foreach ($weekRows as $r) { $weekByVt[(int) $r['vt_user_id']] = ['minutes' => (int) $r['minutes'], 'days' => (int) $r['days']]; }

    // Recent notifications for the client user (top 5).
    $stmt = db()->prepare(
        "SELECT * FROM notifications WHERE user_id = :uid ORDER BY read_at IS NULL DESC, created_at DESC LIMIT 5"
    );
    $stmt->execute([':uid' => $u['id']]);
    $notes = $stmt->fetchAll();

    // Today's meetings (scheduled or completed, for this client).
    $stmt = db()->prepare(
        "SELECT m.*,
                CASE WHEN m.attendee_user_id IS NOT NULL
                     THEN (SELECT first_name || ' ' || last_name FROM users WHERE id = m.attendee_user_id)
                     ELSE NULL END AS attendee_name
         FROM meetings m
         WHERE m.client_id = :cid
           AND date(m.scheduled_at) = date('now','localtime')
         ORDER BY m.scheduled_at"
    );
    $stmt->execute([':cid' => $cid]);
    $todayMeetings = $stmt->fetchAll();

    // Recent messages — last 5 message receipts to this user, with sender name.
    $stmt = db()->prepare(
        "SELECT m.*, u.first_name AS s_fn, u.last_name AS s_ln, u.email AS s_email, u.photo_url AS s_photo
         FROM messages m
         JOIN users u ON u.id = m.sender_user_id
         WHERE m.receiver_user_id = :uid
         ORDER BY m.created_at DESC LIMIT 5"
    );
    $stmt->execute([':uid' => $u['id']]);
    $recentMessages = $stmt->fetchAll();

    // Full hired history (active + ended engagements) for the ROI Actual gauge.
    $stmt = db()->prepare(
        "SELECT u.first_name, u.last_name, u.email, cv.started_at, cv.ended_at, cv.contract_status,
                p.role_title, p.department
         FROM client_vts cv
         JOIN users u ON u.id = cv.vt_user_id
         LEFT JOIN vt_profiles p ON p.user_id = u.id
         WHERE cv.client_id = :cid
         ORDER BY cv.started_at"
    );
    $stmt->execute([':cid' => $cid]);
    $hiredHistory = $stmt->fetchAll();

    return [
        'client'             => $client,
        'vts'                => $vts,
        'csms'               => $csms,
        'meetings'           => $meet->fetchAll(),
        'tasks_active'       => $tasksActive,
        'tasks_done_recent'  => $tasksDone,
        'eod_recent'         => $eodRecent,
        'workday_week'       => $weekByVt,
        'notifications'      => $notes,
        'meetings_today'     => $todayMeetings,
        'recent_messages'    => $recentMessages,
        'hired_history'      => $hiredHistory,
    ];
}

function dashboard_csm_data(array $u): array
{
    $stmt = db()->prepare(
        'SELECT c.*, uu.first_name AS c_fn, uu.last_name AS c_ln, uu.email AS c_email
         FROM csm_clients cc
         JOIN clients c ON c.id = cc.client_id
         LEFT JOIN users uu ON uu.id = c.user_id
         WHERE cc.csm_user_id = :uid
         ORDER BY c.company_name'
    );
    $stmt->execute([':uid' => $u['id']]);
    $clients = $stmt->fetchAll();

    $meet = db()->prepare(
        'SELECT m.*, c.company_name FROM meetings m
         JOIN clients c ON c.id = m.client_id
         JOIN csm_clients cc ON cc.client_id = m.client_id
         WHERE cc.csm_user_id = :uid
         ORDER BY m.scheduled_at DESC LIMIT 10'
    );
    $meet->execute([':uid' => $u['id']]);

    return ['clients' => $clients, 'meetings' => $meet->fetchAll()];
}

function dashboard_vt_data(array $u): array
{
    $prof = db()->prepare('SELECT * FROM vt_profiles WHERE user_id = :uid');
    $prof->execute([':uid' => $u['id']]);
    $profile = $prof->fetch();

    $client = null;
    $csms   = [];
    if ($profile && $profile['status'] === 'hired') {
        $cv = db()->prepare(
            'SELECT cv.*, c.company_name, c.id AS cid
             FROM client_vts cv
             JOIN clients c ON c.id = cv.client_id
             WHERE cv.vt_user_id = :uid AND cv.contract_status = "active"
             LIMIT 1'
        );
        $cv->execute([':uid' => $u['id']]);
        $client = $cv->fetch() ?: null;
        if ($client) {
            $csms = client_csms((int) $client['cid']);
        }
    }

    $eod = db()->prepare('SELECT * FROM eod_reports WHERE vt_user_id = :uid ORDER BY report_date DESC LIMIT 5');
    $eod->execute([':uid' => $u['id']]);

    return [
        'profile'   => $profile,
        'client'    => $client,
        'csms'      => $csms,
        'eod_recent' => $eod->fetchAll(),
    ];
}

/* Lookups shared by multiple dashboards. */
function client_hired_vts(int $clientId): array
{
    $stmt = db()->prepare(
        'SELECT cv.*, u.first_name, u.last_name, u.email, u.photo_url, u.country,
                p.department, p.role_title, p.experience_years, p.english_level,
                p.workday_link AS profile_workday_link
         FROM client_vts cv
         JOIN users u ON u.id = cv.vt_user_id
         LEFT JOIN vt_profiles p ON p.user_id = cv.vt_user_id
         WHERE cv.client_id = :cid AND cv.contract_status = "active"
         ORDER BY u.first_name'
    );
    $stmt->execute([':cid' => $clientId]);
    return $stmt->fetchAll();
}

function client_csms(int $clientId): array
{
    $stmt = db()->prepare(
        'SELECT u.* FROM csm_clients cc
         JOIN users u ON u.id = cc.csm_user_id
         WHERE cc.client_id = :cid'
    );
    $stmt->execute([':cid' => $clientId]);
    return $stmt->fetchAll();
}

/* ═════════════════════════════════════════════════════════════════════════
 * SELF — profile + password
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_profile(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $first   = trim((string) ($_POST['first_name'] ?? ''));
        $last    = trim((string) ($_POST['last_name'] ?? ''));
        $phone   = trim((string) ($_POST['phone'] ?? ''));
        $country = trim((string) ($_POST['country'] ?? ''));
        $photo   = trim((string) ($_POST['photo_url'] ?? ''));
        $cover   = trim((string) ($_POST['cover_url'] ?? ''));

        // Handle file uploads for photo + cover (either or both). Files saved
        // to data/avatars/{user_id}/{kind}.{ext}; users.{photo_url,cover_url}
        // is updated to point at the in-portal serving URL.
        foreach (['photo' => 'photo_url', 'cover' => 'cover_url'] as $kind => $col) {
            $f = $_FILES[$kind . '_upload'] ?? null;
            if (!$f || empty($f['tmp_name']) || (int) $f['error'] !== UPLOAD_ERR_OK) { continue; }
            if ((int) $f['size'] > 8 * 1024 * 1024) { flash('error', ucfirst($kind) . ' upload must be under 8MB.'); continue; }
            $info = getimagesize($f['tmp_name']);
            if (!$info) { flash('error', 'Uploaded ' . $kind . ' is not a valid image.'); continue; }
            $extMap = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp'];
            $ext = $extMap[$info[2]] ?? null;
            if (!$ext) { flash('error', ucfirst($kind) . ' must be JPG/PNG/GIF/WebP.'); continue; }
            $dir = __DIR__ . '/../data/avatars/' . (int) $u['id'];
            if (!is_dir($dir) && !@mkdir($dir, 0775, true)) { flash('error', 'Could not create avatars folder.'); continue; }
            foreach (glob($dir . '/' . $kind . '.*') as $old) { @unlink($old); }
            $dest = $dir . '/' . $kind . '.' . $ext;
            if (!move_uploaded_file($f['tmp_name'], $dest)) { flash('error', 'Could not save ' . $kind . ' upload.'); continue; }
            // Bust caches per upload with a timestamp param.
            $served = 'index.php?p=avatar&id=' . (int) $u['id'] . '&k=' . $kind . '&t=' . time();
            if ($kind === 'photo') { $photo = $served; } else { $cover = $served; }
        }

        db()->prepare(
            'UPDATE users SET first_name=:fn, last_name=:ln, phone=:p, country=:c, photo_url=:ph, cover_url=:cv, updated_at=CURRENT_TIMESTAMP WHERE id=:id'
        )->execute([
            ':fn' => $first, ':ln' => $last, ':p' => $phone, ':c' => $country,
            ':ph' => $photo, ':cv' => $cover, ':id' => $u['id'],
        ]);
        audit_log('update', 'user.self', (int) $u['id']);
        flash('success', 'Profile updated.');
        redirect(portal_url('profile'));
    }
    render('profile', ['user' => $u]);
}

/**
 * Avatar serve — readfile of data/avatars/{user_id}/{kind}.{ext}.
 * Public (no auth) so cover/profile photos render on the dashboard without
 * a session bounce; only stored avatars are served, no path traversal.
 */
function handle_avatar_serve(): void
{
    $id   = (int) ($_GET['id'] ?? 0);
    $kind = preg_replace('#[^a-z]#i', '', (string) ($_GET['k'] ?? ''));
    if ($id < 1 || !in_array($kind, ['photo', 'cover'], true)) {
        http_response_code(404); echo 'Not found'; return;
    }
    $base = realpath(__DIR__ . '/../data/avatars');
    if ($base === false) { http_response_code(404); echo 'Not found'; return; }
    $glob = $base . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $kind . '.*';
    $matches = glob($glob);
    if (empty($matches)) {
        // Fall back to the SVG placeholder for missing avatars.
        $ph = __DIR__ . '/../images/photos/placeholder-avatar.svg';
        if (is_file($ph)) {
            header('Content-Type: image/svg+xml');
            header('Cache-Control: public, max-age=3600');
            readfile($ph);
            return;
        }
        http_response_code(404); return;
    }
    $file = $matches[0];
    $real = realpath($file);
    if ($real === false || !str_starts_with($real, $base)) { http_response_code(403); return; }
    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mime = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp'][$ext] ?? 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: public, max-age=3600');
    readfile($file);
}

function handle_password_change(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(portal_url('profile'));
    }
    csrf_verify();
    $old = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['new_password'] ?? '');
    $rep = (string) ($_POST['repeat_password'] ?? '');

    if (!password_verify($old, $u['password_hash'])) {
        flash('error', 'Current password is incorrect.');
        redirect(portal_url('profile'));
    }
    if (strlen($new) < 10) {
        flash('error', 'New password must be at least 10 characters.');
        redirect(portal_url('profile'));
    }
    if ($new !== $rep) {
        flash('error', 'New password and confirmation do not match.');
        redirect(portal_url('profile'));
    }

    db()->prepare('UPDATE users SET password_hash = :h, updated_at = CURRENT_TIMESTAMP WHERE id = :id')
        ->execute([':h' => password_hash($new, PASSWORD_DEFAULT), ':id' => $u['id']]);
    audit_log('password_change', 'user', (int) $u['id']);
    flash('success', 'Password updated.');
    redirect(portal_url('profile'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * SUPER ADMIN — Users CRUD
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_users_list(): void
{
    require_role('super_admin');
    $role = (string) ($_GET['role'] ?? '');
    $q    = trim((string) ($_GET['q'] ?? ''));
    $where = ['1=1'];
    $params = [];
    if (in_array($role, PORTAL_ROLES, true)) {
        $where[] = 'role = :role';
        $params[':role'] = $role;
    }
    if ($q !== '') {
        $where[] = '(email LIKE :q OR first_name LIKE :q OR last_name LIKE :q)';
        $params[':q'] = '%' . $q . '%';
    }
    $sql = 'SELECT * FROM users WHERE ' . implode(' AND ', $where) . ' ORDER BY role, last_name, first_name';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    render('users-list', ['users' => $stmt->fetchAll(), 'filter_role' => $role, 'q' => $q]);
}

function handle_users_edit(): void
{
    require_role('super_admin');
    $id   = (int) ($_GET['id'] ?? 0);
    $user = null;
    if ($id > 0) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch() ?: null;
        if (!$user) {
            flash('error', 'User not found.');
            redirect(portal_url('users'));
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $email = trim((string) ($_POST['email'] ?? ''));
        $role  = (string) ($_POST['role'] ?? '');
        $first = trim((string) ($_POST['first_name'] ?? ''));
        $last  = trim((string) ($_POST['last_name'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $country = trim((string) ($_POST['country'] ?? ''));
        $photo = trim((string) ($_POST['photo_url'] ?? ''));
        $active = isset($_POST['active']) ? 1 : 0;
        $pw    = (string) ($_POST['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Valid email required.'); redirect(portal_url('users.edit', ['id' => $id]));
        }
        if (!in_array($role, PORTAL_ROLES, true)) {
            flash('error', 'Invalid role.'); redirect(portal_url('users.edit', ['id' => $id]));
        }

        if ($id > 0) {
            $sql = 'UPDATE users SET email=:e, role=:r, first_name=:fn, last_name=:ln, phone=:p, country=:c, photo_url=:ph, active=:a, updated_at=CURRENT_TIMESTAMP';
            $params = [':e'=>$email, ':r'=>$role, ':fn'=>$first, ':ln'=>$last, ':p'=>$phone, ':c'=>$country, ':ph'=>$photo, ':a'=>$active, ':id'=>$id];
            if ($pw !== '') {
                if (strlen($pw) < 10) { flash('error', 'Password must be at least 10 characters.'); redirect(portal_url('users.edit', ['id' => $id])); }
                $sql .= ', password_hash=:h';
                $params[':h'] = password_hash($pw, PASSWORD_DEFAULT);
            }
            $sql .= ' WHERE id=:id';
            try {
                db()->prepare($sql)->execute($params);
                audit_log('update', 'user', $id, 'email=' . $email);
                $finalId = $id;
            } catch (Throwable $ex) {
                flash('error', 'Email already in use.'); redirect(portal_url('users.edit', ['id' => $id]));
            }
        } else {
            if (strlen($pw) < 10) { flash('error', 'Password (at least 10 chars) required for new user.'); redirect(portal_url('users.edit')); }
            try {
                $ins = db()->prepare(
                    'INSERT INTO users (email, password_hash, role, first_name, last_name, phone, country, photo_url, active)
                     VALUES (:e, :h, :r, :fn, :ln, :p, :c, :ph, :a)'
                );
                $ins->execute([
                    ':e'=>$email, ':h'=>password_hash($pw, PASSWORD_DEFAULT), ':r'=>$role,
                    ':fn'=>$first, ':ln'=>$last, ':p'=>$phone, ':c'=>$country, ':ph'=>$photo, ':a'=>$active,
                ]);
                $finalId = (int) db()->lastInsertId();
                audit_log('create', 'user', $finalId, 'email=' . $email);

                // If creating a VT, also create a vt_profile row.
                if (in_array($role, ['vt_hired','vt_onpool'], true)) {
                    db()->prepare('INSERT OR IGNORE INTO vt_profiles (user_id, status) VALUES (:u, :s)')
                        ->execute([':u' => $finalId, ':s' => $role === 'vt_hired' ? 'hired' : 'onpool']);
                }
                // If creating a client, also create a clients row (super admin can fill in details after).
                if ($role === 'client') {
                    db()->prepare('INSERT OR IGNORE INTO clients (user_id, company_name) VALUES (:u, :n)')
                        ->execute([':u' => $finalId, ':n' => trim($first . ' ' . $last) !== '' ? trim($first . ' ' . $last) . ' (Company)' : $email]);
                }
            } catch (Throwable $ex) {
                flash('error', 'Email already in use.'); redirect(portal_url('users.edit'));
            }
        }
        flash('success', 'Saved.');
        redirect(portal_url('users.edit', ['id' => $finalId]));
    }

    render('users-edit', ['user' => $user]);
}

function handle_users_delete(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(portal_url('users'));
    }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $me = current_user();
    if ($id === (int) $me['id']) {
        flash('error', 'You cannot delete your own account.');
        redirect(portal_url('users'));
    }
    db()->prepare('DELETE FROM users WHERE id = :id')->execute([':id' => $id]);
    audit_log('delete', 'user', $id);
    flash('success', 'User deleted.');
    redirect(portal_url('users'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * SUPER ADMIN — Clients CRUD
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_clients_list(): void
{
    require_role('super_admin');
    $q = trim((string) ($_GET['q'] ?? ''));
    $sql = 'SELECT c.*, u.email AS user_email FROM clients c LEFT JOIN users u ON u.id = c.user_id';
    $params = [];
    if ($q !== '') {
        $sql .= ' WHERE c.company_name LIKE :q OR c.company_email LIKE :q OR u.email LIKE :q';
        $params[':q'] = '%' . $q . '%';
    }
    $sql .= ' ORDER BY c.company_name';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    render('clients-list', ['clients' => $stmt->fetchAll(), 'q' => $q]);
}

function handle_clients_edit(): void
{
    require_role('super_admin');
    $id = (int) ($_GET['id'] ?? 0);
    $client = null;
    if ($id > 0) {
        $stmt = db()->prepare('SELECT * FROM clients WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $client = $stmt->fetch() ?: null;
        if (!$client) { flash('error', 'Client not found.'); redirect(portal_url('clients')); }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $userId    = (int) ($_POST['user_id'] ?? 0) ?: null;
        $company   = trim((string) ($_POST['company_name'] ?? ''));
        $email     = trim((string) ($_POST['company_email'] ?? ''));
        $domain    = trim((string) ($_POST['company_domain'] ?? ''));
        $billing   = trim((string) ($_POST['billing_contact_email'] ?? ''));
        $status    = (string) ($_POST['contract_status'] ?? 'active');
        $workday   = trim((string) ($_POST['workday_link'] ?? ''));
        $notes     = trim((string) ($_POST['notes'] ?? ''));

        if ($company === '') { flash('error', 'Company name required.'); redirect(portal_url('clients.edit', ['id'=>$id])); }

        if ($id > 0) {
            db()->prepare(
                'UPDATE clients SET user_id=:u, company_name=:n, company_email=:e, company_domain=:d, billing_contact_email=:b, contract_status=:s, workday_link=:w, notes=:no, updated_at=CURRENT_TIMESTAMP WHERE id=:id'
            )->execute([
                ':u'=>$userId, ':n'=>$company, ':e'=>$email, ':d'=>$domain, ':b'=>$billing, ':s'=>$status, ':w'=>$workday, ':no'=>$notes, ':id'=>$id,
            ]);
            audit_log('update', 'client', $id);
            $finalId = $id;
        } else {
            db()->prepare(
                'INSERT INTO clients (user_id, company_name, company_email, company_domain, billing_contact_email, contract_status, workday_link, notes)
                 VALUES (:u, :n, :e, :d, :b, :s, :w, :no)'
            )->execute([
                ':u'=>$userId, ':n'=>$company, ':e'=>$email, ':d'=>$domain, ':b'=>$billing, ':s'=>$status, ':w'=>$workday, ':no'=>$notes,
            ]);
            $finalId = (int) db()->lastInsertId();
            audit_log('create', 'client', $finalId, $company);
        }
        flash('success', 'Saved.');
        redirect(portal_url('clients.edit', ['id' => $finalId]));
    }

    $clientUsers = db()->query("SELECT id, email, first_name, last_name FROM users WHERE role='client' ORDER BY first_name")->fetchAll();
    render('clients-edit', ['client' => $client, 'client_users' => $clientUsers]);
}

function handle_clients_delete(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('clients')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    db()->prepare('DELETE FROM clients WHERE id = :id')->execute([':id' => $id]);
    audit_log('delete', 'client', $id);
    flash('success', 'Client deleted.');
    redirect(portal_url('clients'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * SUPER ADMIN — VT profiles CRUD
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_vts_list(): void
{
    require_role('super_admin');
    $status = (string) ($_GET['status'] ?? '');
    $q      = trim((string) ($_GET['q'] ?? ''));
    $sql = 'SELECT p.*, u.email, u.first_name, u.last_name, u.country, u.photo_url FROM vt_profiles p JOIN users u ON u.id = p.user_id WHERE 1=1';
    $params = [];
    if ($status === 'hired' || $status === 'onpool') {
        $sql .= ' AND p.status = :s'; $params[':s'] = $status;
    }
    if ($q !== '') {
        $sql .= ' AND (u.email LIKE :q OR u.first_name LIKE :q OR u.last_name LIKE :q OR p.role_title LIKE :q OR p.department LIKE :q)';
        $params[':q'] = '%' . $q . '%';
    }
    $sql .= ' ORDER BY u.first_name';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    render('vts-list', ['vts' => $stmt->fetchAll(), 'status' => $status, 'q' => $q]);
}

function handle_vts_edit(): void
{
    require_role('super_admin');
    $id = (int) ($_GET['id'] ?? 0);
    $profile = null;
    if ($id > 0) {
        $stmt = db()->prepare('SELECT p.*, u.email, u.first_name, u.last_name FROM vt_profiles p JOIN users u ON u.id = p.user_id WHERE p.id = :id');
        $stmt->execute([':id' => $id]);
        $profile = $stmt->fetch() ?: null;
        if (!$profile) { flash('error', 'VT profile not found.'); redirect(portal_url('vts')); }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $userId = (int) ($_POST['user_id'] ?? 0);
        if ($userId < 1) { flash('error', 'Linked user required.'); redirect(portal_url('vts.edit', ['id'=>$id])); }
        $fields = [
            'status'             => (string) ($_POST['status'] ?? 'onpool'),
            'department'         => trim((string) ($_POST['department'] ?? '')),
            'role_title'         => trim((string) ($_POST['role_title'] ?? '')),
            'experience_years'   => (int) ($_POST['experience_years'] ?? 0),
            'ehr_software'       => trim((string) ($_POST['ehr_software'] ?? '')),
            'english_level'      => trim((string) ($_POST['english_level'] ?? '')),
            'iq_band'            => trim((string) ($_POST['iq_band'] ?? '')),
            'technical_band'     => trim((string) ($_POST['technical_band'] ?? '')),
            'summary'            => trim((string) ($_POST['summary'] ?? '')),
            'experience_text'    => trim((string) ($_POST['experience_text'] ?? '')),
            'resume_url'         => trim((string) ($_POST['resume_url'] ?? '')),
            'video_url'          => trim((string) ($_POST['video_url'] ?? '')),
            'workday_tracker_id' => trim((string) ($_POST['workday_tracker_id'] ?? '')),
            'workday_link'       => trim((string) ($_POST['workday_link'] ?? '')),
        ];
        if (!in_array($fields['status'], ['onpool','hired'], true)) {
            flash('error', 'Invalid status.'); redirect(portal_url('vts.edit', ['id'=>$id]));
        }

        if ($id > 0) {
            $sets = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($fields)));
            $params = array_combine(array_map(fn($k)=>":$k",array_keys($fields)), array_values($fields));
            $params[':id'] = $id;
            db()->prepare("UPDATE vt_profiles SET $sets, updated_at=CURRENT_TIMESTAMP WHERE id=:id")->execute($params);
            audit_log('update', 'vt_profile', $id);
            $finalId = $id;
        } else {
            $cols = array_merge(['user_id'], array_keys($fields));
            $vals = array_merge([':user_id'], array_map(fn($k)=>":$k",array_keys($fields)));
            $sql  = 'INSERT INTO vt_profiles (' . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ')';
            $params = array_combine(array_map(fn($k)=>":$k",array_keys($fields)), array_values($fields));
            $params[':user_id'] = $userId;
            try {
                db()->prepare($sql)->execute($params);
                $finalId = (int) db()->lastInsertId();
                audit_log('create', 'vt_profile', $finalId);
            } catch (Throwable $ex) {
                flash('error', 'That user already has a VT profile.'); redirect(portal_url('vts.edit', ['id'=>$id]));
            }
        }

        // Keep user role in sync with the profile status.
        db()->prepare('UPDATE users SET role = :r, updated_at = CURRENT_TIMESTAMP WHERE id = :u')
            ->execute([':r' => $fields['status'] === 'hired' ? 'vt_hired' : 'vt_onpool', ':u' => $userId]);

        flash('success', 'Saved.');
        redirect(portal_url('vts.edit', ['id' => $finalId]));
    }

    $vtUsers = db()->query("SELECT id, email, first_name, last_name FROM users WHERE role IN ('vt_hired','vt_onpool') ORDER BY first_name")->fetchAll();
    render('vts-edit', ['profile' => $profile, 'vt_users' => $vtUsers]);
}

function handle_vts_delete(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('vts')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    db()->prepare('DELETE FROM vt_profiles WHERE id = :id')->execute([':id' => $id]);
    audit_log('delete', 'vt_profile', $id);
    flash('success', 'VT profile deleted.');
    redirect(portal_url('vts'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * SUPER ADMIN — Assignments (CSM ↔ Client, Client ↔ VT)
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_assignments_view(): void
{
    require_role('super_admin');
    $pdo = db();
    $clients = $pdo->query('SELECT id, company_name, contract_status FROM clients ORDER BY company_name')->fetchAll();
    $csms    = $pdo->query("SELECT id, first_name, last_name, email FROM users WHERE role='csm' AND active=1 ORDER BY first_name")->fetchAll();
    $hired   = $pdo->query("SELECT u.id, u.first_name, u.last_name, u.email, p.role_title FROM users u LEFT JOIN vt_profiles p ON p.user_id = u.id WHERE u.role='vt_hired' AND u.active=1 ORDER BY u.first_name")->fetchAll();
    $csmLinks = [];
    foreach ($pdo->query('SELECT csm_user_id, client_id FROM csm_clients')->fetchAll() as $r) {
        $csmLinks[(int) $r['csm_user_id']][(int) $r['client_id']] = true;
    }
    $vtLinks = [];
    foreach ($pdo->query('SELECT client_id, vt_user_id FROM client_vts WHERE contract_status = "active"')->fetchAll() as $r) {
        $vtLinks[(int) $r['vt_user_id']][(int) $r['client_id']] = true;
    }
    render('assignments', [
        'clients' => $clients, 'csms' => $csms, 'hired' => $hired,
        'csm_links' => $csmLinks, 'vt_links' => $vtLinks,
    ]);
}

function handle_assignment_csm_toggle(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('assignments')); }
    csrf_verify();
    $csm    = (int) ($_POST['csm_id'] ?? 0);
    $client = (int) ($_POST['client_id'] ?? 0);
    $on     = !empty($_POST['on']);
    if ($csm < 1 || $client < 1) { redirect(portal_url('assignments')); }
    if ($on) {
        db()->prepare('INSERT OR IGNORE INTO csm_clients (csm_user_id, client_id) VALUES (:c, :cl)')
            ->execute([':c'=>$csm, ':cl'=>$client]);
        audit_log('assign', 'csm_client', $client, 'csm=' . $csm);
    } else {
        db()->prepare('DELETE FROM csm_clients WHERE csm_user_id=:c AND client_id=:cl')
            ->execute([':c'=>$csm, ':cl'=>$client]);
        audit_log('unassign', 'csm_client', $client, 'csm=' . $csm);
    }
    redirect(portal_url('assignments'));
}

function handle_assignment_vt_toggle(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('assignments')); }
    csrf_verify();
    $vt     = (int) ($_POST['vt_id'] ?? 0);
    $client = (int) ($_POST['client_id'] ?? 0);
    $on     = !empty($_POST['on']);
    if ($vt < 1 || $client < 1) { redirect(portal_url('assignments')); }
    if ($on) {
        db()->prepare('INSERT OR IGNORE INTO client_vts (client_id, vt_user_id, contract_status) VALUES (:cl, :v, "active")')
            ->execute([':cl'=>$client, ':v'=>$vt]);
        audit_log('assign', 'client_vt', $client, 'vt=' . $vt);
    } else {
        db()->prepare('DELETE FROM client_vts WHERE client_id=:cl AND vt_user_id=:v')
            ->execute([':cl'=>$client, ':v'=>$vt]);
        audit_log('unassign', 'client_vt', $client, 'vt=' . $vt);
    }
    redirect(portal_url('assignments'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * MEETINGS (shared across roles, scoped by role)
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_meetings_list(): void
{
    $u = require_login();
    $sql = 'SELECT m.*, c.company_name,
                   uo.first_name AS org_fn, uo.last_name AS org_ln,
                   ua.first_name AS att_fn, ua.last_name AS att_ln
            FROM meetings m
            JOIN clients c ON c.id = m.client_id
            JOIN users uo ON uo.id = m.organizer_user_id
            LEFT JOIN users ua ON ua.id = m.attendee_user_id';
    $params = [];
    switch ($u['role']) {
        case 'super_admin':
            break;
        case 'client':
            $sql .= ' JOIN clients cc ON cc.id = m.client_id AND cc.user_id = :uid';
            $params[':uid'] = $u['id'];
            break;
        case 'csm':
            $sql .= ' JOIN csm_clients cc ON cc.client_id = m.client_id AND cc.csm_user_id = :uid';
            $params[':uid'] = $u['id'];
            break;
        case 'vt_hired':
        case 'vt_onpool':
            $sql .= ' WHERE m.attendee_user_id = :uid';
            $params[':uid'] = $u['id'];
            break;
    }
    $sql .= ' ORDER BY m.scheduled_at DESC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    render('meetings-list', ['meetings' => $stmt->fetchAll(), 'user' => $u]);
}

function handle_meetings_edit(): void
{
    $u = require_login();
    if (!in_array($u['role'], ['super_admin','client','csm'], true)) {
        http_response_code(403);
        render('error', ['title'=>'Forbidden','message'=>'Only super admin / client / CSM can schedule meetings.']);
        return;
    }
    $id = (int) ($_GET['id'] ?? 0);
    $meeting = null;
    if ($id > 0) {
        $stmt = db()->prepare('SELECT * FROM meetings WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $meeting = $stmt->fetch() ?: null;
        if (!$meeting) { flash('error', 'Meeting not found.'); redirect(portal_url('meetings')); }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $clientId = (int) ($_POST['client_id'] ?? 0);
        $attendee = (int) ($_POST['attendee_user_id'] ?? 0) ?: null;
        $role     = (string) ($_POST['meeting_with_role'] ?? '');
        $when     = trim((string) ($_POST['scheduled_at'] ?? ''));
        $duration = max(15, (int) ($_POST['duration_minutes'] ?? 30));
        $topic    = trim((string) ($_POST['topic'] ?? ''));
        $notes    = trim((string) ($_POST['notes'] ?? ''));
        $status   = (string) ($_POST['status'] ?? 'scheduled');

        if ($clientId < 1 || $when === '' || !in_array($role, ['csm','vt'], true)) {
            flash('error', 'Client, attendee role, and date/time required.');
            redirect(portal_url('meetings.edit', ['id'=>$id]));
        }
        if (!in_array($status, ['scheduled','completed','cancelled'], true)) { $status = 'scheduled'; }

        if ($id > 0) {
            db()->prepare(
                'UPDATE meetings SET client_id=:c, attendee_user_id=:a, meeting_with_role=:r, scheduled_at=:w, duration_minutes=:d, topic=:t, notes=:n, status=:s, updated_at=CURRENT_TIMESTAMP WHERE id=:id'
            )->execute([':c'=>$clientId, ':a'=>$attendee, ':r'=>$role, ':w'=>$when, ':d'=>$duration, ':t'=>$topic, ':n'=>$notes, ':s'=>$status, ':id'=>$id]);
            audit_log('update', 'meeting', $id);
            $finalId = $id;
        } else {
            db()->prepare(
                'INSERT INTO meetings (client_id, organizer_user_id, attendee_user_id, meeting_with_role, scheduled_at, duration_minutes, topic, notes, status)
                 VALUES (:c, :o, :a, :r, :w, :d, :t, :n, :s)'
            )->execute([':c'=>$clientId, ':o'=>$u['id'], ':a'=>$attendee, ':r'=>$role, ':w'=>$when, ':d'=>$duration, ':t'=>$topic, ':n'=>$notes, ':s'=>$status]);
            $finalId = (int) db()->lastInsertId();
            audit_log('create', 'meeting', $finalId);
        }
        flash('success', 'Meeting saved.');
        redirect(portal_url('meetings'));
    }

    // Scope options for the form by role.
    if ($u['role'] === 'client') {
        $stmt = db()->prepare('SELECT id, company_name FROM clients WHERE user_id = :u');
        $stmt->execute([':u'=>$u['id']]);
        $clients = $stmt->fetchAll();
    } elseif ($u['role'] === 'csm') {
        $stmt = db()->prepare('SELECT c.id, c.company_name FROM csm_clients cc JOIN clients c ON c.id = cc.client_id WHERE cc.csm_user_id = :u');
        $stmt->execute([':u'=>$u['id']]);
        $clients = $stmt->fetchAll();
    } else {
        $clients = db()->query('SELECT id, company_name FROM clients ORDER BY company_name')->fetchAll();
    }
    $candidates = db()->query("SELECT id, first_name, last_name, email, role FROM users WHERE role IN ('csm','vt_hired') AND active=1 ORDER BY role, first_name")->fetchAll();
    render('meetings-edit', ['meeting'=>$meeting, 'clients'=>$clients, 'candidates'=>$candidates, 'user'=>$u]);
}

function handle_meetings_delete(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('meetings')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if (!is_super_admin()) {
        $check = db()->prepare('SELECT organizer_user_id FROM meetings WHERE id = :id');
        $check->execute([':id'=>$id]);
        $orgId = (int) $check->fetchColumn();
        if ($orgId !== (int) $u['id']) {
            flash('error', 'Only the organizer or a super admin can delete this meeting.');
            redirect(portal_url('meetings'));
        }
    }
    db()->prepare('DELETE FROM meetings WHERE id = :id')->execute([':id'=>$id]);
    audit_log('delete', 'meeting', $id);
    flash('success', 'Meeting deleted.');
    redirect(portal_url('meetings'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * EOD REPORTS
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_eod_list(): void
{
    $u = require_login();
    $sql = 'SELECT r.*, u.first_name, u.last_name, u.email FROM eod_reports r JOIN users u ON u.id = r.vt_user_id';
    $params = [];
    switch ($u['role']) {
        case 'super_admin':
            break;
        case 'vt_hired':
        case 'vt_onpool':
            $sql .= ' WHERE r.vt_user_id = :uid';
            $params[':uid'] = $u['id'];
            break;
        case 'client':
            $sql .= ' JOIN client_vts cv ON cv.vt_user_id = r.vt_user_id
                      JOIN clients c ON c.id = cv.client_id AND c.user_id = :uid';
            $params[':uid'] = $u['id'];
            break;
        case 'csm':
            $sql .= ' JOIN client_vts cv ON cv.vt_user_id = r.vt_user_id
                      JOIN csm_clients cc ON cc.client_id = cv.client_id AND cc.csm_user_id = :uid';
            $params[':uid'] = $u['id'];
            break;
    }
    $sql .= ' ORDER BY r.report_date DESC, r.updated_at DESC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    render('eod-list', ['reports' => $stmt->fetchAll(), 'user' => $u]);
}

function handle_eod_edit(): void
{
    $u = require_login();
    $isVt = in_array($u['role'], ['vt_hired','vt_onpool'], true);
    if (!$isVt && !is_super_admin()) {
        http_response_code(403);
        render('error', ['title'=>'Forbidden','message'=>'Only VTs and super admin can edit EOD reports.']);
        return;
    }
    $id = (int) ($_GET['id'] ?? 0);
    $report = null;
    if ($id > 0) {
        $stmt = db()->prepare('SELECT * FROM eod_reports WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $report = $stmt->fetch() ?: null;
        if (!$report) { flash('error', 'Report not found.'); redirect(portal_url('eod')); }
        if ($isVt && (int) $report['vt_user_id'] !== (int) $u['id']) {
            http_response_code(403);
            render('error', ['title'=>'Forbidden','message'=>'You can only edit your own EOD reports.']);
            return;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $vtUser = $isVt ? (int) $u['id'] : (int) ($_POST['vt_user_id'] ?? 0);
        $date   = trim((string) ($_POST['report_date'] ?? date('Y-m-d')));
        $fields = [
            'best_work'          => trim((string) ($_POST['best_work'] ?? '')),
            'help_needed'        => trim((string) ($_POST['help_needed'] ?? '')),
            'focus_next'         => trim((string) ($_POST['focus_next'] ?? '')),
            'pending_waiting_on' => trim((string) ($_POST['pending_waiting_on'] ?? '')),
            'kpi_name'           => trim((string) ($_POST['kpi_name'] ?? '')),
            'kpi_target'         => trim((string) ($_POST['kpi_target'] ?? '')),
            'kpi_achieved'       => trim((string) ($_POST['kpi_achieved'] ?? '')),
        ];
        if ($vtUser < 1 || $date === '') {
            flash('error', 'VT and report date required.');
            redirect(portal_url('eod.edit', ['id'=>$id]));
        }
        if ($id > 0) {
            $sets = implode(', ', array_map(fn($k)=>"$k = :$k", array_keys($fields)));
            $params = array_combine(array_map(fn($k)=>":$k", array_keys($fields)), array_values($fields));
            $params[':id'] = $id;
            db()->prepare("UPDATE eod_reports SET $sets, updated_at=CURRENT_TIMESTAMP WHERE id=:id")->execute($params);
            audit_log('update', 'eod_report', $id);
            $finalId = $id;
        } else {
            $cols = array_merge(['vt_user_id','report_date'], array_keys($fields));
            $vals = array_merge([':vt_user_id',':report_date'], array_map(fn($k)=>":$k",array_keys($fields)));
            $sql  = 'INSERT INTO eod_reports (' . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ')';
            $params = array_combine(array_map(fn($k)=>":$k",array_keys($fields)), array_values($fields));
            $params[':vt_user_id']  = $vtUser;
            $params[':report_date'] = $date;
            try {
                db()->prepare($sql)->execute($params);
                $finalId = (int) db()->lastInsertId();
                audit_log('create', 'eod_report', $finalId);
            } catch (Throwable $ex) {
                flash('error', 'A report for that VT + date already exists.');
                redirect(portal_url('eod'));
            }
        }
        flash('success', 'EOD report saved.');
        redirect(portal_url('eod'));
    }

    $vtUsers = $isVt ? [] : db()->query("SELECT id, first_name, last_name, email FROM users WHERE role IN ('vt_hired','vt_onpool') ORDER BY first_name")->fetchAll();
    render('eod-edit', ['report'=>$report, 'user'=>$u, 'vt_users'=>$vtUsers, 'is_vt'=>$isVt]);
}

function handle_eod_delete(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('eod')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if (!is_super_admin()) {
        $check = db()->prepare('SELECT vt_user_id FROM eod_reports WHERE id = :id');
        $check->execute([':id'=>$id]);
        $owner = (int) $check->fetchColumn();
        if ($owner !== (int) $u['id']) {
            flash('error', 'You can only delete your own EOD reports.');
            redirect(portal_url('eod'));
        }
    }
    db()->prepare('DELETE FROM eod_reports WHERE id = :id')->execute([':id'=>$id]);
    audit_log('delete', 'eod_report', $id);
    flash('success', 'EOD report deleted.');
    redirect(portal_url('eod'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * AUDIT LOG (super admin)
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_audit_list(): void
{
    require_role('super_admin');
    $stmt = db()->query(
        'SELECT a.*, u.email AS actor_email FROM audit_log a LEFT JOIN users u ON u.id = a.actor_user_id ORDER BY a.created_at DESC LIMIT 500'
    );
    render('audit-list', ['rows' => $stmt->fetchAll()]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * TRAFFIC MONITORING (super admin) — homepage / marketing pageviews
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_traffic_list(): void
{
    require_role('super_admin');

    if (!traffic_table_exists()) {
        render('traffic-list', [
            'rows' => [], 'stats' => null, 'top_countries' => [], 'top_pages' => [],
            'q' => '', 'not_ready' => true,
        ]);
        return;
    }

    $pdo = db();
    $q   = trim((string) ($_GET['q'] ?? ''));

    $where = '1=1';
    $params = [];
    if ($q !== '') {
        $where = '(ip LIKE :q OR country LIKE :q OR city LIKE :q OR path LIKE :q OR region LIKE :q)';
        $params[':q'] = '%' . $q . '%';
    }

    $rows = $pdo->prepare("SELECT * FROM traffic WHERE $where ORDER BY id DESC LIMIT 300");
    $rows->execute($params);

    $stats = [
        'today'        => (int) $pdo->query("SELECT COUNT(*) FROM traffic WHERE date(created_at)=date('now')")->fetchColumn(),
        'views_7d'     => (int) $pdo->query("SELECT COUNT(*) FROM traffic WHERE created_at >= datetime('now','-7 days')")->fetchColumn(),
        'views_30d'    => (int) $pdo->query("SELECT COUNT(*) FROM traffic WHERE created_at >= datetime('now','-30 days')")->fetchColumn(),
        'visitors_30d' => (int) $pdo->query("SELECT COUNT(DISTINCT ip) FROM traffic WHERE created_at >= datetime('now','-30 days')")->fetchColumn(),
        'total'        => (int) $pdo->query("SELECT COUNT(*) FROM traffic")->fetchColumn(),
    ];
    $topCountries = $pdo->query(
        "SELECT CASE WHEN country='' THEN 'Unknown' ELSE country END AS country, COUNT(*) AS n,
                COUNT(DISTINCT ip) AS visitors
         FROM traffic WHERE created_at >= datetime('now','-30 days')
         GROUP BY country ORDER BY n DESC LIMIT 12"
    )->fetchAll();
    $topPages = $pdo->query(
        "SELECT CASE WHEN path='' THEN '/' ELSE path END AS path, COUNT(*) AS n
         FROM traffic WHERE created_at >= datetime('now','-30 days')
         GROUP BY path ORDER BY n DESC LIMIT 12"
    )->fetchAll();

    render('traffic-list', [
        'rows' => $rows->fetchAll(), 'stats' => $stats,
        'top_countries' => $topCountries, 'top_pages' => $topPages,
        'q' => $q, 'not_ready' => false,
    ]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * HUBSPOT SYNC (super admin only)
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_hubspot_page(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    render('hubspot', [
        'settings'     => hs_settings(),
        'state'        => hs_state_load(),
        'talent_state' => hs_talent_state_load(),
        'client_state' => hs_client_state_load(),
        'test_result'  => $_SESSION['hs_test_result'] ?? null,
    ]);
    unset($_SESSION['hs_test_result']);
}

function handle_hubspot_save_settings(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('hubspot')); }
    csrf_verify();

    $keys = array_keys(hs_defaults());
    foreach ($keys as $k) {
        if ($k === 'hs_import_media') {
            set_setting($k, isset($_POST[$k]) ? '1' : '0');
        } else {
            $v = trim((string) ($_POST[$k] ?? ''));
            // Don't blank out the token if the form sent an empty string AND we already have one.
            if ($k === 'hs_token' && $v === '' && get_setting('hs_token', '') !== '') {
                continue;
            }
            set_setting($k, $v);
        }
    }
    audit_log('hs_save_settings', 'hubspot');
    flash('success', 'HubSpot settings saved.');
    redirect(portal_url('hubspot'));
}

function handle_hubspot_test(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('hubspot')); }
    csrf_verify();

    $settings = hs_settings();
    $hs       = new HubSpotClient((string) $settings['hs_token']);
    $resp     = $hs->request('GET', '/account-info/v3/details');
    $_SESSION['hs_test_result'] = $resp['ok']
        ? ['ok'=>true,  'msg' => 'Connected. Portal ID: ' . ($resp['data']['portalId'] ?? 'unknown')]
        : ['ok'=>false, 'msg' => $resp['error'] ?: ('HTTP ' . $resp['status'])];
    audit_log('hs_test', 'hubspot', null, $_SESSION['hs_test_result']['msg']);
    redirect(portal_url('hubspot'));
}

function handle_hubspot_control(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('hubspot')); }
    csrf_verify();
    $action = (string) ($_POST['action'] ?? '');
    if (!in_array($action, ['start','pause','resume','reset'], true)) {
        flash('error', 'Unknown sync control action.');
        redirect(portal_url('hubspot'));
    }
    hs_control($action);
    audit_log('hs_control', 'hubspot', null, $action);
    redirect(portal_url('hubspot'));
}

function handle_hubspot_step(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'POST required']);
        return;
    }
    csrf_verify();
    $state = hs_step();
    header('Content-Type: application/json');
    echo json_encode($state, JSON_UNESCAPED_SLASHES);
}

/* ─────── Two-pipeline endpoints (talent + client) ─────── */

function handle_hubspot_talent_control(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('hubspot')); }
    csrf_verify();
    $action = (string) ($_POST['action'] ?? '');
    if (!in_array($action, ['start','pause','resume','reset'], true)) {
        flash('error', 'Unknown talent sync action.');
        redirect(portal_url('hubspot'));
    }
    hs_talent_control($action);
    audit_log('hs_talent_control', 'hubspot', null, $action);
    flash('success', 'Talent sync: ' . $action . '.');
    redirect(portal_url('hubspot'));
}

function handle_hubspot_talent_step(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'POST required']);
        return;
    }
    csrf_verify();
    $state = hs_talent_step();
    header('Content-Type: application/json');
    echo json_encode($state, JSON_UNESCAPED_SLASHES);
}

function handle_hubspot_client_control(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('hubspot')); }
    csrf_verify();
    $action = (string) ($_POST['action'] ?? '');
    if (!in_array($action, ['start','pause','resume','reset'], true)) {
        flash('error', 'Unknown client sync action.');
        redirect(portal_url('hubspot'));
    }
    hs_client_control($action);
    audit_log('hs_client_control', 'hubspot', null, $action);
    flash('success', 'Client sync: ' . $action . '.');
    redirect(portal_url('hubspot'));
}

function handle_hubspot_client_step(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'POST required']);
        return;
    }
    csrf_verify();
    $state = hs_client_step();
    header('Content-Type: application/json');
    echo json_encode($state, JSON_UNESCAPED_SLASHES);
}

/* ─── Phase 6: single-fetch handlers ───
 * search: GET or POST, params q (string), returns up to 10 HubSpot matches as JSON.
 * sync_one: POST, params id (hubspot id), runs the per-record processor inline. */

function handle_hubspot_talent_search(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    $q = trim((string) ($_REQUEST['q'] ?? ''));
    header('Content-Type: application/json');
    echo json_encode(hs_talent_search($q), JSON_UNESCAPED_SLASHES);
}

function handle_hubspot_talent_sync_one(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'POST required']);
        return;
    }
    csrf_verify();
    $id = trim((string) ($_POST['id'] ?? ''));
    $res = hs_talent_sync_one($id);
    if (!empty($res['ok'])) {
        audit_log('hs_talent_sync_one', 'user', (int) ($res['user_id'] ?? 0), 'contact=' . $id . ' action=' . (string) ($res['action'] ?? ''));
    }
    header('Content-Type: application/json');
    echo json_encode($res, JSON_UNESCAPED_SLASHES);
}

function handle_hubspot_client_search(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    $q = trim((string) ($_REQUEST['q'] ?? ''));
    header('Content-Type: application/json');
    echo json_encode(hs_client_search($q), JSON_UNESCAPED_SLASHES);
}

function handle_hubspot_client_sync_one(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'POST required']);
        return;
    }
    csrf_verify();
    $id = trim((string) ($_POST['id'] ?? ''));
    $res = hs_client_sync_one($id);
    if (!empty($res['ok'])) {
        audit_log('hs_client_sync_one', 'client', (int) ($res['client_id'] ?? 0), 'company=' . $id);
    }
    header('Content-Type: application/json');
    echo json_encode($res, JSON_UNESCAPED_SLASHES);
}

/**
 * DANGER: wipe everything imported from HubSpot — synced users (VT + CSM),
 * synced client companies + their linked login users, all downloaded media
 * files, and the sync state. Requires the typed confirmation "DELETE".
 * Wrapped in a transaction so a mid-purge failure leaves the DB consistent.
 */
/* ═════════════════════════════════════════════════════════════════════════
 * DEMO USERS — idempotent seeder for one of each role (client/csm/vt_hired/
 * vt_onpool) plus their relationships. Emails use the 'demo-' prefix so the
 * Phase 9 "purge ALL" sweep can spare them.
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_hubspot_seed_demo(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('hubspot')); }
    csrf_verify();

    $pdo = db();
    $created = 0;
    $existed = 0;

    $demos = [
        ['email' => 'demo-client@virtualteammate.com',     'role' => 'client',    'fn' => 'Demo',   'ln' => 'Client'],
        ['email' => 'demo-csm@virtualteammate.com',        'role' => 'csm',       'fn' => 'Demo',   'ln' => 'CSM'],
        ['email' => 'demo-vt-hired@virtualteammate.com',   'role' => 'vt_hired',  'fn' => 'Maya',   'ln' => 'Reyes'],
        ['email' => 'demo-vt-hired-2@virtualteammate.com', 'role' => 'vt_hired',  'fn' => 'Carlos', 'ln' => 'Diaz'],
        ['email' => 'demo-vt-hired-3@virtualteammate.com', 'role' => 'vt_hired',  'fn' => 'Aisha',  'ln' => 'Khan'],
        ['email' => 'demo-vt-onpool@virtualteammate.com',  'role' => 'vt_onpool', 'fn' => 'Demo',   'ln' => 'On-Pool'],
    ];
    $ids = [];
    $hiredKeys = ['demo-vt-hired@virtualteammate.com','demo-vt-hired-2@virtualteammate.com','demo-vt-hired-3@virtualteammate.com'];
    $hiredIds  = [];

    foreach ($demos as $d) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e' => $d['email']]);
        $uid = (int) ($stmt->fetchColumn() ?: 0);
        if ($uid > 0) {
            $ids[$d['role']] = $uid;
            $existed++;
            continue;
        }
        $pdo->prepare(
            'INSERT INTO users (email, password_hash, role, first_name, last_name, country, active)
             VALUES (:e, :h, :r, :fn, :ln, "US", 1)'
        )->execute([
            ':e'  => $d['email'],
            ':h'  => password_hash(hs_default_password($d['role']), PASSWORD_DEFAULT),
            ':r'  => $d['role'], ':fn' => $d['fn'], ':ln' => $d['ln'],
        ]);
        $newId = (int) $pdo->lastInsertId();
        // For VT hired we need to keep all three IDs (not just the last), so collect them.
        if (in_array($d['email'], $hiredKeys, true)) { $hiredIds[$d['email']] = $newId; }
        // For non-VT roles, just stash by role.
        $ids[$d['role']] = $newId;
        $created++;
    }
    // If a VT-hired user existed already, capture its id.
    foreach ($hiredKeys as $he) {
        if (isset($hiredIds[$he])) { continue; }
        $s = $pdo->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
        $s->execute([':e' => $he]);
        $hiredIds[$he] = (int) ($s->fetchColumn() ?: 0);
    }

    // Ensure a clients row backs the demo client user.
    $clientRowId = 0;
    if (isset($ids['client'])) {
        $stmt = $pdo->prepare('SELECT id FROM clients WHERE user_id = :u LIMIT 1');
        $stmt->execute([':u' => $ids['client']]);
        $clientRowId = (int) ($stmt->fetchColumn() ?: 0);
        if ($clientRowId === 0) {
            $pdo->prepare(
                'INSERT INTO clients (user_id, company_name, company_email, contract_status)
                 VALUES (:u, :n, :e, "active")'
            )->execute([
                ':u' => $ids['client'],
                ':n' => 'Demo Client Practice',
                ':e' => 'demo-client@virtualteammate.com',
            ]);
            $clientRowId = (int) $pdo->lastInsertId();
        }
    }

    // CSM -> client link
    if ($clientRowId && isset($ids['csm'])) {
        $pdo->prepare('INSERT OR IGNORE INTO csm_clients (csm_user_id, client_id) VALUES (:csm, :c)')
            ->execute([':csm' => $ids['csm'], ':c' => $clientRowId]);
    }

    // All 3 hired VTs -> client link, with staggered start dates so the ROI
    // gauge actually shows lifetime months.
    $hiredStartOffsets = [
        'demo-vt-hired@virtualteammate.com'   => '-9 months',
        'demo-vt-hired-2@virtualteammate.com' => '-5 months',
        'demo-vt-hired-3@virtualteammate.com' => '-2 months',
    ];
    if ($clientRowId) {
        foreach ($hiredIds as $email => $uid) {
            if ($uid <= 0) { continue; }
            $started = date('Y-m-d H:i:s', strtotime($hiredStartOffsets[$email] ?? 'now'));
            $pdo->prepare(
                'INSERT OR IGNORE INTO client_vts (client_id, vt_user_id, contract_status, started_at, workday_tracker_id)
                 VALUES (:c, :v, "active", :s, :w)'
            )->execute([
                ':c' => $clientRowId, ':v' => $uid, ':s' => $started,
                ':w' => 'demo-tracker-' . $uid,
            ]);
        }
    }

    // VT profile rows for each VT demo.
    $vtSpecs = [
        'demo-vt-hired@virtualteammate.com'   => ['fn'=>'Maya',  'role'=>'Medical Receptionist', 'dept'=>'Healthcare',   'years'=>5],
        'demo-vt-hired-2@virtualteammate.com' => ['fn'=>'Carlos','role'=>'Medical Biller',       'dept'=>'Healthcare',   'years'=>7],
        'demo-vt-hired-3@virtualteammate.com' => ['fn'=>'Aisha', 'role'=>'Executive Assistant',  'dept'=>'Admin Support','years'=>4],
        'demo-vt-onpool@virtualteammate.com'  => ['fn'=>'Demo',  'role'=>'On-Pool Candidate',    'dept'=>'Pool',         'years'=>3],
    ];
    foreach ($vtSpecs as $email => $spec) {
        $uid = (int) ($hiredIds[$email] ?? ($ids['vt_onpool'] ?? 0));
        if ($email === 'demo-vt-onpool@virtualteammate.com') { $uid = (int) ($ids['vt_onpool'] ?? 0); }
        if ($uid <= 0) { continue; }
        $stmt = $pdo->prepare('SELECT id FROM vt_profiles WHERE user_id = :u LIMIT 1');
        $stmt->execute([':u' => $uid]);
        if ($stmt->fetchColumn()) { continue; }
        $status = $email === 'demo-vt-onpool@virtualteammate.com' ? 'onpool' : 'hired';
        $pdo->prepare(
            'INSERT INTO vt_profiles (user_id, status, department, role_title, english_level, experience_years, summary, experience_text, workday_tracker_id)
             VALUES (:u, :s, :dep, :role, "C1", :yrs, :sm, :ex, :w)'
        )->execute([
            ':u'   => $uid,
            ':s'   => $status,
            ':dep' => $spec['dept'],
            ':role'=> $spec['role'],
            ':yrs' => $spec['years'],
            ':sm'  => $spec['fn'] . ' is a ' . $status . ' VT seeded for portal testing.',
            ':ex'  => $spec['years'] . '+ years of professional experience.',
            ':w'   => 'demo-tracker-' . $uid,
        ]);
    }

    audit_log('hs_seed_demo', 'user', null, "created={$created} existed={$existed}");
    flash('success', "Demo users seeded — created {$created}, already existed {$existed}.");
    redirect(portal_url('hubspot'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * PURGE ALL — wipe every VT/CSM/client user + their media so the user can
 * start fresh. Spares super_admin, demo users (email starts with "demo-"),
 * and any manually-created portal accounts in other roles.
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_hubspot_purge_all(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('hubspot')); }
    csrf_verify();

    if (strtoupper(trim((string) ($_POST['confirm'] ?? ''))) !== 'PURGE ALL') {
        flash('error', 'Type PURGE ALL in the confirmation box to confirm.');
        redirect(portal_url('hubspot'));
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
        // Snapshot client user_ids (so we can delete those user rows after the
        // clients table — clients.user_id is ON DELETE SET NULL, not CASCADE).
        $stmt = $pdo->query("SELECT user_id FROM clients WHERE user_id IS NOT NULL");
        $clientUserIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'user_id');

        // All clients (every clients row).
        $cClients = $pdo->exec("DELETE FROM clients");

        // Non-demo, non-super_admin users in the synced roles. Demo users
        // (email starts with 'demo-') are spared so login testing keeps working.
        $cUsers = $pdo->exec(
            "DELETE FROM users
              WHERE role IN ('vt_hired','vt_onpool','csm','client')
                AND email NOT LIKE 'demo-%'"
        );

        $pdo->commit();
    } catch (Throwable $ex) {
        $pdo->rollBack();
        flash('error', 'Purge failed: ' . $ex->getMessage());
        redirect(portal_url('hubspot'));
    }

    // Wipe downloaded media for VT and any other entity directories. Skip
    // anything under data/media/<entity>/<id>/ for IDs that match a demo user.
    $demoIds = [];
    foreach ($pdo->query("SELECT id FROM users WHERE email LIKE 'demo-%'") as $r) {
        $demoIds[(int) $r['id']] = true;
    }
    $fileCount = 0;
    foreach (['vt', 'client', 'csm'] as $entity) {
        $base = __DIR__ . '/../data/media/' . $entity;
        if (!is_dir($base)) { continue; }
        foreach (glob($base . '/*', GLOB_ONLYDIR) ?: [] as $idDir) {
            $idNum = (int) basename($idDir);
            if (isset($demoIds[$idNum])) { continue; } // spare demo media
            $iter = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($idDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($iter as $f) {
                $f->isDir() ? @rmdir($f->getPathname()) : (@unlink($f->getPathname()) && $fileCount++);
            }
            @rmdir($idDir);
        }
    }

    hs_control('reset');

    audit_log('hs_purge_all', 'hubspot', null, "clients=$cClients users=$cUsers media=$fileCount");
    flash('success', "Hard-purged: {$cClients} clients, {$cUsers} users, {$fileCount} media files. Demo users spared. Sync state reset.");
    redirect(portal_url('hubspot'));
}

function handle_hubspot_purge(): void
{
    require_role('super_admin');
    require_once __DIR__ . '/hubspot.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('hubspot')); }
    csrf_verify();

    if (strtoupper(trim((string) ($_POST['confirm'] ?? ''))) !== 'DELETE') {
        flash('error', 'Type DELETE in the confirmation box to confirm the purge.');
        redirect(portal_url('hubspot'));
    }

    $pdo = db();
    $cClients = $cClientUsers = $cUsers = 0;
    $pdo->beginTransaction();
    try {
        // Snapshot the client login users tied to HubSpot-synced clients so
        // we can delete those user rows too (clients.user_id is ON DELETE SET NULL).
        $clientUserIds = [];
        foreach ($pdo->query("SELECT user_id FROM clients WHERE hubspot_company_id != '' AND user_id IS NOT NULL") as $r) {
            $clientUserIds[] = (int) $r['user_id'];
        }

        // Synced clients (FK cascades csm_clients, client_vts, meetings).
        $cClients = $pdo->exec("DELETE FROM clients WHERE hubspot_company_id != ''");

        // Their login users.
        if ($clientUserIds) {
            $in = implode(',', array_fill(0, count($clientUserIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($in)");
            $stmt->execute($clientUserIds);
            $cClientUsers = $stmt->rowCount();
        }

        // VT and CSM users marked by HubSpot (FK cascades vt_profiles, eod_reports, links).
        $cUsers = $pdo->exec("DELETE FROM users WHERE hubspot_contact_id != ''");

        $pdo->commit();
    } catch (Throwable $ex) {
        $pdo->rollBack();
        flash('error', 'Purge failed: ' . $ex->getMessage());
        redirect(portal_url('hubspot'));
    }

    // Wipe downloaded media (data/media/vt/*).
    $mediaDir  = __DIR__ . '/../data/media/vt';
    $fileCount = 0;
    if (is_dir($mediaDir)) {
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($mediaDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iter as $f) {
            $f->isDir() ? @rmdir($f->getPathname()) : (@unlink($f->getPathname()) && $fileCount++);
        }
    }

    hs_control('reset');

    $totalUsers = $cUsers + $cClientUsers;
    audit_log('hs_purge', 'hubspot', null, "clients=$cClients users=$totalUsers media_files=$fileCount");
    flash('success', "Purged: {$cClients} HubSpot clients, {$totalUsers} synced users, {$fileCount} media files. Sync state reset.");
    redirect(portal_url('hubspot'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * CSMs (super admin)
 * ═════════════════════════════════════════════════════════════════════════ */

/* ═════════════════════════════════════════════════════════════════════════
 * RELATIONSHIPS — three-table dashboard mirroring the WP plugin
 * (clients -> CSMs+VTs, CSMs -> clients+VTs, VTs -> client+CSM)
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_relationships(): void
{
    require_role('super_admin');
    $u   = current_user();
    $pdo = db();

    $clients = $pdo->query(
        "SELECT id, company_name, contract_status, hubspot_company_id, hubspot_owner_id
         FROM clients ORDER BY company_name COLLATE NOCASE"
    )->fetchAll();

    // CSM <-> client links, fetched once and bucketed both ways.
    $csmsByClient   = [];
    $clientsByCsm   = [];
    foreach ($pdo->query(
        "SELECT cc.client_id, u.id AS uid, u.email, u.first_name, u.last_name
         FROM csm_clients cc JOIN users u ON u.id = cc.csm_user_id"
    ) as $r) {
        $csmsByClient[(int) $r['client_id']][] = $r;
        $clientsByCsm[(int) $r['uid']][]       = (int) $r['client_id'];
    }

    // VT <-> client links, same approach.
    $vtsByClient    = [];
    $clientsByVt    = [];
    foreach ($pdo->query(
        "SELECT cv.client_id, cv.contract_status, u.id AS uid, u.email, u.first_name, u.last_name, u.role
         FROM client_vts cv JOIN users u ON u.id = cv.vt_user_id"
    ) as $r) {
        $vtsByClient[(int) $r['client_id']][] = $r;
        $clientsByVt[(int) $r['uid']][]       = (int) $r['client_id'];
    }

    $allCsms = $pdo->query(
        "SELECT id, email, first_name, last_name, country, hubspot_owner_id
         FROM users WHERE role = 'csm' ORDER BY last_name, first_name, email"
    )->fetchAll();

    $allVts = $pdo->query(
        "SELECT id, email, first_name, last_name, country, role
         FROM users WHERE role IN ('vt_hired','vt_onpool') ORDER BY last_name, first_name, email"
    )->fetchAll();

    // Lookup table so the VT/CSM views can show client names without re-querying.
    $clientNames = [];
    foreach ($clients as $c) { $clientNames[(int) $c['id']] = $c['company_name']; }

    render('relationships', [
        'user'         => $u,
        'clients'      => $clients,
        'csmsByClient' => $csmsByClient,
        'vtsByClient'  => $vtsByClient,
        'allCsms'      => $allCsms,
        'clientsByCsm' => $clientsByCsm,
        'allVts'       => $allVts,
        'clientsByVt'  => $clientsByVt,
        'clientNames'  => $clientNames,
    ]);
}

function handle_csms_list(): void
{
    require_role('super_admin');
    $q = trim((string) ($_GET['q'] ?? ''));
    $sql = "SELECT u.*,
                   (SELECT COUNT(*) FROM csm_clients cc WHERE cc.csm_user_id = u.id) AS clients_count
            FROM users u
            WHERE u.role = 'csm'";
    $params = [];
    if ($q !== '') {
        $sql .= ' AND (u.email LIKE :q OR u.first_name LIKE :q OR u.last_name LIKE :q)';
        $params[':q'] = '%' . $q . '%';
    }
    $sql .= ' ORDER BY u.first_name, u.last_name';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    render('csms-list', ['csms' => $stmt->fetchAll(), 'q' => $q]);
}

function handle_csms_view(): void
{
    require_role('super_admin');
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare("SELECT * FROM users WHERE id = :id AND role = 'csm'");
    $stmt->execute([':id' => $id]);
    $csm = $stmt->fetch();
    if (!$csm) { flash('error', 'CSM not found.'); redirect(portal_url('csms')); }

    $cl = db()->prepare(
        'SELECT c.* FROM csm_clients cc JOIN clients c ON c.id = cc.client_id WHERE cc.csm_user_id = :u ORDER BY c.company_name'
    );
    $cl->execute([':u' => $id]);
    $clients = $cl->fetchAll();

    $mt = db()->prepare(
        'SELECT m.*, c.company_name FROM meetings m
         JOIN clients c ON c.id = m.client_id
         JOIN csm_clients cc ON cc.client_id = m.client_id
         WHERE cc.csm_user_id = :u ORDER BY m.scheduled_at DESC LIMIT 10'
    );
    $mt->execute([':u' => $id]);

    render('csms-view', ['csm' => $csm, 'clients' => $clients, 'meetings' => $mt->fetchAll()]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * Detail (view-only) pages
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_vts_view(): void
{
    require_role('super_admin','csm','client');
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare(
        "SELECT p.*, u.email, u.first_name, u.last_name, u.phone, u.country, u.photo_url, u.active, u.last_login_at, u.hubspot_contact_id
         FROM vt_profiles p JOIN users u ON u.id = p.user_id
         WHERE p.id = :id"
    );
    $stmt->execute([':id' => $id]);
    $vt = $stmt->fetch();
    if (!$vt) { flash('error', 'VT profile not found.'); redirect(portal_url('vts')); }

    $clientStmt = db()->prepare(
        'SELECT c.*, cv.started_at, cv.contract_status AS cv_status, cv.workday_tracker_id, cv.workday_link AS cv_workday_link
         FROM client_vts cv JOIN clients c ON c.id = cv.client_id
         WHERE cv.vt_user_id = :u'
    );
    $clientStmt->execute([':u' => (int) $vt['user_id']]);
    $clients = $clientStmt->fetchAll();

    $csmStmt = db()->prepare(
        'SELECT DISTINCT u.id, u.first_name, u.last_name, u.email
         FROM client_vts cv
         JOIN csm_clients cc ON cc.client_id = cv.client_id
         JOIN users u ON u.id = cc.csm_user_id
         WHERE cv.vt_user_id = :u'
    );
    $csmStmt->execute([':u' => (int) $vt['user_id']]);
    $csms = $csmStmt->fetchAll();

    $eodStmt = db()->prepare(
        'SELECT * FROM eod_reports WHERE vt_user_id = :u ORDER BY report_date DESC LIMIT 10'
    );
    $eodStmt->execute([':u' => (int) $vt['user_id']]);

    render('vts-view', [
        'vt' => $vt, 'clients' => $clients, 'csms' => $csms, 'eod_recent' => $eodStmt->fetchAll(),
    ]);
}

function handle_clients_view(): void
{
    require_role('super_admin','csm','client');
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare('SELECT * FROM clients WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $client = $stmt->fetch();
    if (!$client) { flash('error', 'Client not found.'); redirect(portal_url('clients')); }

    $login = null;
    if (!empty($client['user_id'])) {
        $u = db()->prepare('SELECT * FROM users WHERE id = :id');
        $u->execute([':id' => (int) $client['user_id']]);
        $login = $u->fetch() ?: null;
    }

    $vts = client_hired_vts((int) $client['id']);
    $csms = client_csms((int) $client['id']);

    $mt = db()->prepare(
        'SELECT m.*,
                uo.first_name AS org_fn, uo.last_name AS org_ln,
                ua.first_name AS att_fn, ua.last_name AS att_ln
         FROM meetings m
         JOIN users uo ON uo.id = m.organizer_user_id
         LEFT JOIN users ua ON ua.id = m.attendee_user_id
         WHERE m.client_id = :c ORDER BY m.scheduled_at DESC LIMIT 10'
    );
    $mt->execute([':c' => (int) $client['id']]);

    render('clients-view', [
        'client'   => $client,
        'login'    => $login,
        'vts'      => $vts,
        'csms'     => $csms,
        'meetings' => $mt->fetchAll(),
    ]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * CLIENT DASHBOARD FEATURES — tasks, workday log, notifications.
 * Mirrors the staging dashboard's task/calendar widgets, EOD pane, and
 * notification bell so a client can run day-to-day work in-portal.
 * ═════════════════════════════════════════════════════════════════════════ */

/** Resolve the client_id the current user is scoped to (or 0 for super_admin). */
function tasks_resolve_client_id(array $u): int
{
    if ($u['role'] === 'super_admin') {
        return (int) ($_GET['client_id'] ?? $_POST['client_id'] ?? 0);
    }
    if ($u['role'] === 'client') {
        $stmt = db()->prepare('SELECT id FROM clients WHERE user_id = :uid LIMIT 1');
        $stmt->execute([':uid' => $u['id']]);
        return (int) ($stmt->fetchColumn() ?: 0);
    }
    if ($u['role'] === 'csm') {
        // CSM scoped to one of their assigned clients (?client_id=X).
        $cid  = (int) ($_GET['client_id'] ?? $_POST['client_id'] ?? 0);
        if ($cid <= 0) { return 0; }
        $stmt = db()->prepare('SELECT 1 FROM csm_clients WHERE csm_user_id = :uid AND client_id = :cid LIMIT 1');
        $stmt->execute([':uid' => $u['id'], ':cid' => $cid]);
        return $stmt->fetchColumn() ? $cid : 0;
    }
    return 0;
}

function handle_tasks_list(): void
{
    $u = require_login();
    if (!in_array($u['role'], ['super_admin','client','csm','vt_hired'], true)) {
        render('error', ['title' => 'Forbidden', 'message' => 'No task list for your role.']);
        return;
    }

    if ($u['role'] === 'vt_hired') {
        // VT sees tasks assigned to them across all their client engagements.
        $stmt = db()->prepare(
            "SELECT t.*, c.company_name FROM tasks t
             LEFT JOIN clients c ON c.id = t.client_id
             WHERE t.assignee_user_id = :uid
             ORDER BY t.status = 'active' DESC, IFNULL(t.due_date,'9999-12-31'),
                      CASE t.priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END,
                      t.created_at DESC"
        );
        $stmt->execute([':uid' => $u['id']]);
        render('tasks-list', ['user' => $u, 'tasks' => $stmt->fetchAll(), 'scope' => 'mine', 'assignees' => []]);
        return;
    }

    $cid = tasks_resolve_client_id($u);
    if ($cid <= 0) {
        render('error', ['title' => 'No client', 'message' => 'No client context found for tasks.']);
        return;
    }

    $stmt = db()->prepare(
        "SELECT t.*, u.first_name AS a_fn, u.last_name AS a_ln, u.email AS a_email
         FROM tasks t LEFT JOIN users u ON u.id = t.assignee_user_id
         WHERE t.client_id = :cid
         ORDER BY t.status = 'active' DESC,
                  CASE t.priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END,
                  IFNULL(t.due_date,'9999-12-31'), t.created_at DESC"
    );
    $stmt->execute([':cid' => $cid]);
    $tasks = $stmt->fetchAll();

    $assignees = client_hired_vts($cid);

    render('tasks-list', ['user' => $u, 'tasks' => $tasks, 'scope' => 'client', 'client_id' => $cid, 'assignees' => $assignees]);
}

function handle_tasks_edit(): void
{
    $u  = require_login();
    $id = (int) ($_REQUEST['id'] ?? 0);
    $pdo = db();

    // Load existing task (if editing) and verify scope access.
    $task = null;
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $task = $stmt->fetch();
        if (!$task) { render('error', ['title' => 'Not found', 'message' => 'Task not found.']); return; }
    }

    $cid = $task ? (int) $task['client_id'] : tasks_resolve_client_id($u);
    if ($cid <= 0) { render('error', ['title' => 'No client', 'message' => 'No client context for this task.']); return; }
    if ($u['role'] !== 'super_admin' && !tasks_user_can_edit_client($u, $cid)) {
        render('error', ['title' => 'Forbidden', 'message' => 'You can not edit tasks for this client.']);
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $title    = trim((string) ($_POST['title'] ?? ''));
        $desc     = trim((string) ($_POST['description'] ?? ''));
        $assignee = (int) ($_POST['assignee_user_id'] ?? 0) ?: null;
        $priority = (string) ($_POST['priority'] ?? 'normal');
        if (!in_array($priority, ['low','normal','high','urgent'], true)) { $priority = 'normal'; }
        $due      = trim((string) ($_POST['due_date'] ?? ''));
        if ($due === '') { $due = null; }

        if ($title === '') {
            flash('error', 'Title is required.');
            redirect(portal_url('tasks.edit', $id ? ['id' => $id] : ($u['role'] === 'super_admin' ? ['client_id' => $cid] : [])));
        }

        if ($task) {
            $pdo->prepare(
                'UPDATE tasks SET title=:t, description=:d, assignee_user_id=:a, priority=:p, due_date=:dd, updated_at=CURRENT_TIMESTAMP
                 WHERE id=:id'
            )->execute([':t'=>$title, ':d'=>$desc, ':a'=>$assignee, ':p'=>$priority, ':dd'=>$due, ':id'=>$id]);
            audit_log('task_update', 'task', $id);
            flash('success', 'Task updated.');
        } else {
            $pdo->prepare(
                'INSERT INTO tasks (client_id, assignee_user_id, created_by, title, description, priority, due_date, status)
                 VALUES (:c, :a, :cb, :t, :d, :p, :dd, "active")'
            )->execute([
                ':c'=>$cid, ':a'=>$assignee, ':cb'=>$u['id'],
                ':t'=>$title, ':d'=>$desc, ':p'=>$priority, ':dd'=>$due,
            ]);
            $newId = (int) $pdo->lastInsertId();
            audit_log('task_create', 'task', $newId);
            if ($assignee) {
                tasks_notify_assignee($assignee, $newId, $title, $cid);
            }
            flash('success', 'Task created.');
        }
        redirect(portal_url('tasks', $u['role'] === 'super_admin' || $u['role'] === 'csm' ? ['client_id' => $cid] : []));
    }

    $assignees = client_hired_vts($cid);
    render('tasks-edit', ['user' => $u, 'task' => $task, 'client_id' => $cid, 'assignees' => $assignees]);
}

function handle_tasks_toggle(): void
{
    $u  = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('tasks')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) { redirect(portal_url('tasks')); }
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $task = $stmt->fetch();
    if (!$task) { flash('error', 'Task not found.'); redirect(portal_url('tasks')); }
    $cid = (int) $task['client_id'];
    // Authorize: super_admin, the linked client user, an assigned CSM, or the assignee VT.
    $authorized = $u['role'] === 'super_admin'
        || tasks_user_can_edit_client($u, $cid)
        || ((int) ($task['assignee_user_id'] ?? 0) === (int) $u['id']);
    if (!$authorized) { flash('error', 'Not allowed.'); redirect(portal_url('tasks')); }

    $newStatus  = $task['status'] === 'active' ? 'completed' : 'active';
    $completed  = $newStatus === 'completed' ? date('Y-m-d H:i:s') : null;
    $pdo->prepare('UPDATE tasks SET status=:s, completed_at=:c, updated_at=CURRENT_TIMESTAMP WHERE id=:id')
        ->execute([':s' => $newStatus, ':c' => $completed, ':id' => $id]);
    audit_log('task_' . ($newStatus === 'completed' ? 'complete' : 'reopen'), 'task', $id);
    flash('success', $newStatus === 'completed' ? 'Marked complete.' : 'Re-opened.');
    redirect(portal_url('tasks', $u['role'] === 'super_admin' || $u['role'] === 'csm' ? ['client_id' => $cid] : []));
}

function handle_tasks_delete(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('tasks')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) { redirect(portal_url('tasks')); }
    $pdo = db();
    $stmt = $pdo->prepare('SELECT client_id FROM tasks WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $cid = (int) ($stmt->fetchColumn() ?: 0);
    if ($cid <= 0) { redirect(portal_url('tasks')); }
    if ($u['role'] !== 'super_admin' && !tasks_user_can_edit_client($u, $cid)) {
        flash('error', 'Not allowed.'); redirect(portal_url('tasks'));
    }
    $pdo->prepare('DELETE FROM tasks WHERE id = :id')->execute([':id' => $id]);
    audit_log('task_delete', 'task', $id);
    flash('success', 'Task deleted.');
    redirect(portal_url('tasks', $u['role'] === 'super_admin' || $u['role'] === 'csm' ? ['client_id' => $cid] : []));
}

function tasks_user_can_edit_client(array $u, int $cid): bool
{
    if ($u['role'] === 'super_admin') { return true; }
    if ($u['role'] === 'client') {
        $stmt = db()->prepare('SELECT 1 FROM clients WHERE id = :c AND user_id = :u');
        $stmt->execute([':c' => $cid, ':u' => $u['id']]);
        return (bool) $stmt->fetchColumn();
    }
    if ($u['role'] === 'csm') {
        $stmt = db()->prepare('SELECT 1 FROM csm_clients WHERE csm_user_id = :u AND client_id = :c');
        $stmt->execute([':u' => $u['id'], ':c' => $cid]);
        return (bool) $stmt->fetchColumn();
    }
    return false;
}

function tasks_notify_assignee(int $userId, int $taskId, string $title, int $cid): void
{
    db()->prepare(
        "INSERT INTO notifications (user_id, kind, title, body, link)
         VALUES (:u, 'task', :t, :b, :l)"
    )->execute([
        ':u' => $userId,
        ':t' => 'New task assigned: ' . mb_substr($title, 0, 80),
        ':b' => 'A new task has been assigned to you.',
        ':l' => 'index.php?p=tasks&highlight=' . $taskId,
    ]);
}

function handle_workday_list(): void
{
    $u = require_login();
    if (!in_array($u['role'], ['super_admin','client','csm','vt_hired'], true)) {
        render('error', ['title' => 'Forbidden', 'message' => 'No workday view for your role.']);
        return;
    }
    $pdo = db();

    if ($u['role'] === 'vt_hired') {
        $stmt = $pdo->prepare(
            "SELECT wl.*, c.company_name FROM workday_logs wl
             LEFT JOIN clients c ON c.id = wl.client_id
             WHERE wl.vt_user_id = :uid
             ORDER BY wl.work_date DESC LIMIT 60"
        );
        $stmt->execute([':uid' => $u['id']]);
        render('workday-list', ['user' => $u, 'logs' => $stmt->fetchAll(), 'scope' => 'mine']);
        return;
    }

    $cid = tasks_resolve_client_id($u);
    if ($cid <= 0) { render('error', ['title' => 'No client', 'message' => 'No client context for workday view.']); return; }
    $stmt = $pdo->prepare(
        "SELECT wl.*, u.first_name, u.last_name FROM workday_logs wl
         JOIN users u ON u.id = wl.vt_user_id
         WHERE wl.client_id = :cid
         ORDER BY wl.work_date DESC, u.last_name LIMIT 200"
    );
    $stmt->execute([':cid' => $cid]);
    render('workday-list', ['user' => $u, 'logs' => $stmt->fetchAll(), 'scope' => 'client', 'client_id' => $cid]);
}

function handle_notifications_list(): void
{
    $u = require_login();
    $stmt = db()->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 100");
    $stmt->execute([':uid' => $u['id']]);
    render('notifications-list', ['user' => $u, 'notifications' => $stmt->fetchAll()]);
}

function handle_notifications_read(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('notifications')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        db()->prepare('UPDATE notifications SET read_at = CURRENT_TIMESTAMP WHERE id = :id AND user_id = :uid')
            ->execute([':id' => $id, ':uid' => $u['id']]);
    } else {
        db()->prepare('UPDATE notifications SET read_at = CURRENT_TIMESTAMP WHERE user_id = :uid AND read_at IS NULL')
            ->execute([':uid' => $u['id']]);
    }
    redirect(portal_url('notifications'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * RESOURCES — Downloads/playbook tab, mirrors the staging
 * [client_resources] shortcode.
 * ═════════════════════════════════════════════════════════════════════════ */

function resources_definitions(): array
{
    return [
        ['title' => 'Outcome-Based Responsibilities (OBR) Worksheet', 'description' => 'Define outcomes and accountability clearly — then reuse it for every role.',                   'url' => 'https://baa78665-b905-489f-a89a-dea3af4d293d.filesusr.com/ugd/739bab_d2efa46efff347d49c2bd8e129a541af.pdf', 'accent' => '#3919BA'],
        ['title' => 'OBR Guide (Healthcare)',                         'description' => 'Examples and best practices tailored for healthcare workflows.',                           'url' => 'https://baa78665-b905-489f-a89a-dea3af4d293d.filesusr.com/ugd/e9e902_13ea922a5746474094b89ac726b0ee2c.pdf', 'accent' => '#16a34a'],
        ['title' => 'OBR Guide (Business)',                           'description' => 'A simple blueprint for outcomes, KPIs, and ownership across teams.',                        'url' => 'https://baa78665-b905-489f-a89a-dea3af4d293d.filesusr.com/ugd/e9e902_cdd544927f544dad96b408d9590f53ba.pdf', 'accent' => '#2563eb'],
        ['title' => 'A Decade of Change',                             'description' => 'A quick read on modern staffing and what it means for your business today.',               'url' => 'https://baa78665-b905-489f-a89a-dea3af4d293d.filesusr.com/ugd/3a288b_ef4040c0dc39463aa55f3da4defc39ed.pdf', 'accent' => '#ea580c'],
        ['title' => 'Minutes to Millions Workbook',                   'description' => 'Delegate, systematize operations, and reclaim strategic focus to scale.',                  'url' => 'https://virtualteammate.com/wp-content/uploads/2025/12/BBYT-Workbook.pdf',                                   'accent' => '#9333ea'],
        ['title' => 'Time Clarity Worksheet',                         'description' => 'Find time drains, prioritize high-value work, and structure your day with clarity.',       'url' => 'https://virtualteammate.com/wp-content/uploads/2025/11/VTM%20-%20Time%20Clarity%20(1).pdf',                  'accent' => '#0ea5e9'],
    ];
}

function handle_resources(): void
{
    require_login();
    render('resources', [
        'resources' => resources_definitions(),
        'playbook'  => [
            'title' => 'Virtual Teammate Client Playbook',
            'desc'  => 'Start here to align expectations, simplify communication, and establish a smooth workflow — so your Virtual Teammate can deliver faster.',
            'url'   => 'https://virtualteammate.com/client-playbook-3/',
        ],
    ]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * MY VTs — dedicated tab listing assigned Virtual Teammates, modeled on
 * the staging [selected_va_list] shortcode (large circular avatar cards).
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_my_vts(): void
{
    $u = require_login();
    $pdo = db();
    if ($u['role'] === 'client') {
        $stmt = $pdo->prepare('SELECT id FROM clients WHERE user_id = :uid LIMIT 1');
        $stmt->execute([':uid' => $u['id']]);
        $cid = (int) ($stmt->fetchColumn() ?: 0);
        if ($cid === 0) { render('error', ['title'=>'No client', 'message'=>'No client record linked.']); return; }
        $vts = client_hired_vts($cid);
    } elseif ($u['role'] === 'csm') {
        // Union of VTs across all clients assigned to this CSM.
        $stmt = $pdo->prepare(
            "SELECT DISTINCT vp.*, u.first_name, u.last_name, u.email, u.country, u.photo_url, cv.client_id, c.company_name
             FROM csm_clients cc
             JOIN client_vts cv ON cv.client_id = cc.client_id AND cv.contract_status = 'active'
             JOIN users u ON u.id = cv.vt_user_id
             LEFT JOIN vt_profiles vp ON vp.user_id = u.id
             LEFT JOIN clients c ON c.id = cv.client_id
             WHERE cc.csm_user_id = :uid
             ORDER BY u.last_name, u.first_name"
        );
        $stmt->execute([':uid' => $u['id']]);
        $vts = $stmt->fetchAll();
    } elseif ($u['role'] === 'super_admin') {
        $stmt = $pdo->query(
            "SELECT vp.*, u.first_name, u.last_name, u.email, u.country, u.photo_url
             FROM vt_profiles vp JOIN users u ON u.id = vp.user_id
             ORDER BY vp.status = 'hired' DESC, u.last_name, u.first_name"
        );
        $vts = $stmt->fetchAll();
    } else {
        render('error', ['title'=>'Forbidden', 'message'=>'My VTs is a client/CSM/admin view.']);
        return;
    }
    render('my-vts', ['user' => $u, 'vts' => $vts]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * MESSAGES — minimal 1:1 chat, modeled on the [va_chat_window] shortcode.
 * Polling-based (no websocket). Authorized to messages between paired
 * client <-> hired VT, CSM <-> their clients/VTs, super_admin <-> anyone.
 * ═════════════════════════════════════════════════════════════════════════ */

/* ═════════════════════════════════════════════════════════════════════════
 * ROI CALCULATOR — Value Creation calc, modeled on the staging
 * [roi_savings_calculator] shortcode. Two panels: Actual (hired VTs +
 * months engaged) and Scenario (bi-weekly cost comparison).
 * ═════════════════════════════════════════════════════════════════════════ */

/* ═════════════════════════════════════════════════════════════════════════
 * PRODUCTIVITY REPORTS — Unifies workday tracker (external links per VT)
 * with EOD reports into a single nav item. Workday entries link out to
 * https://workdaytracker.com/ (per VT's workday_link / workday_tracker_id).
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_productivity(): void
{
    $u   = require_login();
    if (!in_array($u['role'], ['super_admin','client','csm','vt_hired'], true)) {
        render('error', ['title' => 'Forbidden', 'message' => 'No productivity reports for your role.']);
        return;
    }
    $pdo = db();
    $vts = [];
    $eod = [];

    if ($u['role'] === 'vt_hired') {
        // VT sees their own workday tracker link + their own EOD reports.
        $stmt = $pdo->prepare(
            'SELECT u.id AS user_id, u.first_name, u.last_name, u.email, u.photo_url,
                    p.workday_link, p.workday_tracker_id, p.role_title, p.department,
                    cv.client_id, cv.workday_link AS cv_workday_link, cv.workday_tracker_id AS cv_workday_tracker_id,
                    c.company_name
             FROM users u
             LEFT JOIN vt_profiles p ON p.user_id = u.id
             LEFT JOIN client_vts  cv ON cv.vt_user_id = u.id AND cv.contract_status = "active"
             LEFT JOIN clients      c ON c.id = cv.client_id
             WHERE u.id = :uid'
        );
        $stmt->execute([':uid' => $u['id']]);
        $vts = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM eod_reports WHERE vt_user_id = :uid ORDER BY report_date DESC LIMIT 30");
        $stmt->execute([':uid' => $u['id']]);
        $eod = $stmt->fetchAll();

    } elseif ($u['role'] === 'client') {
        $stmt = $pdo->prepare('SELECT id, company_name FROM clients WHERE user_id = :uid LIMIT 1');
        $stmt->execute([':uid' => $u['id']]);
        $client = $stmt->fetch();
        if (!$client) { render('error', ['title' => 'No client', 'message' => 'No client record linked.']); return; }
        $cid = (int) $client['id'];

        $stmt = $pdo->prepare(
            'SELECT u.id AS user_id, u.first_name, u.last_name, u.email, u.photo_url,
                    p.workday_link AS profile_workday_link, p.workday_tracker_id AS profile_tracker_id,
                    p.role_title, p.department,
                    cv.workday_link AS cv_workday_link, cv.workday_tracker_id AS cv_workday_tracker_id
             FROM client_vts cv
             JOIN users u ON u.id = cv.vt_user_id
             LEFT JOIN vt_profiles p ON p.user_id = u.id
             WHERE cv.client_id = :cid AND cv.contract_status = "active"
             ORDER BY u.first_name'
        );
        $stmt->execute([':cid' => $cid]);
        $vts = $stmt->fetchAll();

        $stmt = $pdo->prepare(
            "SELECT er.*, u.first_name, u.last_name, u.email
             FROM eod_reports er
             JOIN client_vts cv ON cv.vt_user_id = er.vt_user_id AND cv.contract_status = 'active'
             JOIN users u ON u.id = er.vt_user_id
             WHERE cv.client_id = :cid
             ORDER BY er.report_date DESC, er.created_at DESC LIMIT 30"
        );
        $stmt->execute([':cid' => $cid]);
        $eod = $stmt->fetchAll();

    } elseif ($u['role'] === 'csm') {
        $stmt = $pdo->prepare(
            "SELECT DISTINCT u.id AS user_id, u.first_name, u.last_name, u.email, u.photo_url,
                    p.workday_link AS profile_workday_link, p.workday_tracker_id AS profile_tracker_id,
                    p.role_title, p.department,
                    cv.workday_link AS cv_workday_link, cv.workday_tracker_id AS cv_workday_tracker_id,
                    c.company_name
             FROM csm_clients cc
             JOIN client_vts cv ON cv.client_id = cc.client_id AND cv.contract_status = 'active'
             JOIN users u ON u.id = cv.vt_user_id
             LEFT JOIN vt_profiles p ON p.user_id = u.id
             LEFT JOIN clients c ON c.id = cv.client_id
             WHERE cc.csm_user_id = :uid
             ORDER BY u.first_name"
        );
        $stmt->execute([':uid' => $u['id']]);
        $vts = $stmt->fetchAll();

        $stmt = $pdo->prepare(
            "SELECT er.*, u.first_name, u.last_name FROM eod_reports er
             JOIN users u ON u.id = er.vt_user_id
             JOIN client_vts cv ON cv.vt_user_id = er.vt_user_id AND cv.contract_status = 'active'
             JOIN csm_clients cc ON cc.client_id = cv.client_id
             WHERE cc.csm_user_id = :uid
             ORDER BY er.report_date DESC, er.created_at DESC LIMIT 50"
        );
        $stmt->execute([':uid' => $u['id']]);
        $eod = $stmt->fetchAll();

    } else { // super_admin
        $stmt = $pdo->query(
            "SELECT u.id AS user_id, u.first_name, u.last_name, u.email, u.photo_url,
                    p.workday_link AS profile_workday_link, p.workday_tracker_id AS profile_tracker_id,
                    p.role_title, p.department
             FROM users u
             LEFT JOIN vt_profiles p ON p.user_id = u.id
             WHERE u.role IN ('vt_hired','vt_onpool')
             ORDER BY u.first_name LIMIT 50"
        );
        $vts = $stmt->fetchAll();
        $stmt = $pdo->query("SELECT er.*, u.first_name, u.last_name FROM eod_reports er JOIN users u ON u.id = er.vt_user_id ORDER BY er.report_date DESC LIMIT 50");
        $eod = $stmt->fetchAll();
    }

    render('productivity', ['user' => $u, 'vts' => $vts, 'eod' => $eod]);
}

function messages_conversation_key(int $a, int $b): string
{
    if ($a > $b) { [$a, $b] = [$b, $a]; }
    return $a . ':' . $b;
}

/** People the current user is allowed to chat with. */
function messages_contacts(array $u): array
{
    $pdo = db();
    if ($u['role'] === 'super_admin') {
        return $pdo->query(
            "SELECT id, first_name, last_name, email, role, photo_url FROM users
             WHERE id != " . (int) $u['id'] . " AND role != 'vt_onpool'
             ORDER BY last_name, first_name"
        )->fetchAll();
    }
    if ($u['role'] === 'client') {
        $stmt = $pdo->prepare(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.role, u.photo_url
             FROM clients c
             JOIN client_vts cv ON cv.client_id = c.id AND cv.contract_status = 'active'
             JOIN users u ON u.id = cv.vt_user_id
             WHERE c.user_id = :uid
             UNION
             SELECT u.id, u.first_name, u.last_name, u.email, u.role, u.photo_url
             FROM clients c
             JOIN csm_clients cc ON cc.client_id = c.id
             JOIN users u ON u.id = cc.csm_user_id
             WHERE c.user_id = :uid"
        );
        $stmt->execute([':uid' => $u['id']]);
        return $stmt->fetchAll();
    }
    if ($u['role'] === 'csm') {
        $stmt = $pdo->prepare(
            "SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, u.role, u.photo_url
             FROM csm_clients cc
             LEFT JOIN clients c ON c.id = cc.client_id
             LEFT JOIN client_vts cv ON cv.client_id = cc.client_id AND cv.contract_status = 'active'
             LEFT JOIN users u ON u.id = COALESCE(cv.vt_user_id, c.user_id)
             WHERE cc.csm_user_id = :uid AND u.id IS NOT NULL"
        );
        $stmt->execute([':uid' => $u['id']]);
        return $stmt->fetchAll();
    }
    if ($u['role'] === 'vt_hired') {
        $stmt = $pdo->prepare(
            "SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, u.role, u.photo_url
             FROM client_vts cv
             JOIN clients c ON c.id = cv.client_id
             LEFT JOIN csm_clients cc ON cc.client_id = c.id
             LEFT JOIN users u ON u.id = COALESCE(c.user_id, cc.csm_user_id)
             WHERE cv.vt_user_id = :uid AND cv.contract_status = 'active' AND u.id IS NOT NULL"
        );
        $stmt->execute([':uid' => $u['id']]);
        return $stmt->fetchAll();
    }
    return [];
}

function handle_messages_list(): void
{
    $u = require_login();
    if (!in_array($u['role'], ['super_admin','client','csm','vt_hired'], true)) {
        render('error', ['title' => 'Forbidden', 'message' => 'Messages not available for your role.']);
        return;
    }
    $pdo = db();
    $contacts = messages_contacts($u);
    $with = (int) ($_GET['with'] ?? 0);

    $messages = [];
    $partner  = null;
    if ($with > 0) {
        // Authorize: $with must be in our contacts list (or super_admin sees all).
        $allowed = $u['role'] === 'super_admin';
        foreach ($contacts as $c) { if ((int) $c['id'] === $with) { $allowed = true; $partner = $c; break; } }
        if ($allowed) {
            if (!$partner) {
                $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, role, photo_url FROM users WHERE id = :id');
                $stmt->execute([':id' => $with]);
                $partner = $stmt->fetch() ?: null;
            }
            if ($partner) {
                $key = messages_conversation_key((int) $u['id'], $with);
                $stmt = $pdo->prepare(
                    "SELECT * FROM messages WHERE conversation_key = :k ORDER BY created_at ASC LIMIT 500"
                );
                $stmt->execute([':k' => $key]);
                $messages = $stmt->fetchAll();
                // Mark partner-sent messages as read.
                $pdo->prepare("UPDATE messages SET read_at = CURRENT_TIMESTAMP WHERE conversation_key = :k AND sender_user_id = :p AND read_at IS NULL")
                    ->execute([':k' => $key, ':p' => $with]);
            }
        }
    }

    // Unread badge counts per contact.
    $unreadByContact = [];
    foreach ($contacts as $c) {
        $key = messages_conversation_key((int) $u['id'], (int) $c['id']);
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM messages WHERE conversation_key = :k AND sender_user_id = :p AND read_at IS NULL');
        $stmt->execute([':k' => $key, ':p' => (int) $c['id']]);
        $unreadByContact[(int) $c['id']] = (int) $stmt->fetchColumn();
    }

    render('messages', [
        'user'     => $u,
        'contacts' => $contacts,
        'partner'  => $partner,
        'messages' => $messages,
        'unread'   => $unreadByContact,
    ]);
}

function handle_messages_send(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('messages')); }
    csrf_verify();
    $with = (int) ($_POST['with'] ?? 0);
    $body = trim((string) ($_POST['body'] ?? ''));
    if ($with <= 0 || $body === '') { redirect(portal_url('messages', $with ? ['with' => $with] : [])); }

    // Authorize the recipient is in our contacts.
    $contacts = messages_contacts($u);
    $ok = $u['role'] === 'super_admin';
    foreach ($contacts as $c) { if ((int) $c['id'] === $with) { $ok = true; break; } }
    if (!$ok) { flash('error', 'You can not message that user.'); redirect(portal_url('messages')); }

    $key = messages_conversation_key((int) $u['id'], $with);
    db()->prepare(
        'INSERT INTO messages (conversation_key, sender_user_id, receiver_user_id, body)
         VALUES (:k, :s, :r, :b)'
    )->execute([':k' => $key, ':s' => $u['id'], ':r' => $with, ':b' => mb_substr($body, 0, 4000)]);

    // Surface to the recipient as a notification.
    db()->prepare(
        "INSERT INTO notifications (user_id, kind, title, body, link)
         VALUES (:u, 'message', :t, :b, :l)"
    )->execute([
        ':u' => $with,
        ':t' => 'New message from ' . trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: $u['email'],
        ':b' => mb_substr($body, 0, 160),
        ':l' => 'index.php?p=messages&with=' . (int) $u['id'],
    ]);

    redirect(portal_url('messages', ['with' => $with]));
}

/* ═════════════════════════════════════════════════════════════════════════
 * MEDIA SERVE — auth-gated readfile of data/media/<entity>/<id>/<kind>.<ext>
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_media_serve(): void
{
    $u      = require_login();
    $entity = preg_replace('#[^a-z0-9_]#i', '', (string) ($_GET['e'] ?? ''));
    $id     = (int) ($_GET['id'] ?? 0);
    $kind   = preg_replace('#[^a-z0-9_]#i', '', (string) ($_GET['k'] ?? ''));

    if ($entity === '' || $id < 1 || $kind === '') {
        http_response_code(400); echo 'Bad request'; return;
    }

    // Authorization: super admin sees everything; otherwise the VT themselves,
    // the linked client of an active engagement, or the assigned CSM.
    if (!is_super_admin()) {
        $allowed = false;
        if ($entity === 'vt') {
            if ((int) $u['id'] === $id) {
                $allowed = true;
            } elseif ($u['role'] === 'client') {
                $stmt = db()->prepare(
                    'SELECT 1 FROM client_vts cv JOIN clients c ON c.id = cv.client_id
                     WHERE c.user_id = :uid AND cv.vt_user_id = :vt LIMIT 1'
                );
                $stmt->execute([':uid' => $u['id'], ':vt' => $id]);
                $allowed = (bool) $stmt->fetchColumn();
            } elseif ($u['role'] === 'csm') {
                $stmt = db()->prepare(
                    'SELECT 1 FROM csm_clients cc
                     JOIN client_vts cv ON cv.client_id = cc.client_id
                     WHERE cc.csm_user_id = :uid AND cv.vt_user_id = :vt LIMIT 1'
                );
                $stmt->execute([':uid' => $u['id'], ':vt' => $id]);
                $allowed = (bool) $stmt->fetchColumn();
            }
        }
        if (!$allowed) {
            http_response_code(403); echo 'Forbidden'; return;
        }
    }

    $base = realpath(__DIR__ . '/../data/media');
    if ($base === false) { http_response_code(404); echo 'Not found'; return; }
    $glob = $base . DIRECTORY_SEPARATOR . $entity . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $kind . '.*';
    $matches = glob($glob);
    if (empty($matches)) { http_response_code(404); echo 'Not found'; return; }
    $file = $matches[0];
    // Defense in depth: confirm the resolved file is still under $base.
    $real = realpath($file);
    if ($real === false || !str_starts_with($real, $base)) { http_response_code(403); echo 'Forbidden'; return; }

    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mime = [
        'jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp',
        'pdf'=>'application/pdf','doc'=>'application/msword','docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'mp4'=>'video/mp4','mov'=>'video/quicktime','m4v'=>'video/x-m4v','webm'=>'video/webm',
    ][$ext] ?? 'application/octet-stream';

    // Browser-renderable formats display inline; office docs force a download.
    $inlineExts  = ['jpg','jpeg','png','gif','webp','pdf','mp4','m4v','webm','mov'];
    $disposition = in_array($ext, $inlineExts, true) ? 'inline' : 'attachment';
    $safeName    = preg_replace('#[^A-Za-z0-9._-]#', '_', basename($file));

    $size = filesize($file);

    // Kill any output buffering so streaming + partial responses aren't corrupted.
    while (ob_get_level() > 0) { ob_end_clean(); }

    header('Content-Type: ' . $mime);
    header('Accept-Ranges: bytes'); // Required so <video> can seek/scrub.
    header('Cache-Control: private, max-age=3600');
    header('Content-Disposition: ' . $disposition . '; filename="' . $safeName . '"');

    // Range request handling — critical for <video> in Chrome/Safari/iOS.
    $rangeHeader = $_SERVER['HTTP_RANGE'] ?? '';
    if ($rangeHeader !== '' && preg_match('/bytes=(\d+)-(\d*)/', $rangeHeader, $m)) {
        $start = (int) $m[1];
        $end   = ($m[2] !== '') ? (int) $m[2] : $size - 1;
        if ($end >= $size) { $end = $size - 1; }
        if ($start > $end || $start < 0) {
            http_response_code(416);
            header('Content-Range: bytes */' . $size);
            return;
        }
        $length = $end - $start + 1;
        http_response_code(206);
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
        header('Content-Length: ' . $length);

        // Stream the requested byte range; never load the whole file in memory.
        $fp = fopen($file, 'rb');
        if ($fp === false) { return; }
        fseek($fp, $start);
        $buf       = 8192;
        $remaining = $length;
        while ($remaining > 0 && !feof($fp)) {
            $read  = $remaining > $buf ? $buf : $remaining;
            $chunk = fread($fp, $read);
            if ($chunk === false) { break; }
            echo $chunk;
            @flush();
            $remaining -= strlen($chunk);
        }
        fclose($fp);
        return;
    }

    // Full content (no range header).
    header('Content-Length: ' . $size);
    readfile($file);
}
