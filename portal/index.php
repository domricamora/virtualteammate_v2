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

    /* ───────────────────────── CSMs (super admin) ─────────────────────────── */
    case 'csms':                   handle_csms_list();               break;
    case 'csms.view':              handle_csms_view();               break;

    /* ───────────────────────── Detail views ─────────────────────────── */
    case 'vts.view':               handle_vts_view();                break;
    case 'clients.view':           handle_clients_view();            break;

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
        return ['client' => null, 'vts' => [], 'csms' => [], 'meetings' => []];
    }
    $vts  = client_hired_vts((int) $client['id']);
    $csms = client_csms((int) $client['id']);
    $meet = db()->prepare('SELECT * FROM meetings WHERE client_id = :cid ORDER BY scheduled_at DESC LIMIT 5');
    $meet->execute([':cid' => $client['id']]);
    return [
        'client'   => $client,
        'vts'      => $vts,
        'csms'     => $csms,
        'meetings' => $meet->fetchAll(),
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
        'SELECT cv.*, u.first_name, u.last_name, u.email, u.photo_url,
                p.department, p.role_title, p.workday_link AS profile_workday_link
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
        db()->prepare(
            'UPDATE users SET first_name=:fn, last_name=:ln, phone=:p, country=:c, photo_url=:ph, updated_at=CURRENT_TIMESTAMP WHERE id=:id'
        )->execute([
            ':fn' => $first, ':ln' => $last, ':p' => $phone, ':c' => $country, ':ph' => $photo, ':id' => $u['id'],
        ]);
        audit_log('update', 'user.self', (int) $u['id']);
        flash('success', 'Profile updated.');
        redirect(portal_url('profile'));
    }
    render('profile', ['user' => $u]);
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
        'settings'   => hs_settings(),
        'state'      => hs_state_load(),
        'test_result'=> $_SESSION['hs_test_result'] ?? null,
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

/**
 * DANGER: wipe everything imported from HubSpot — synced users (VT + CSM),
 * synced client companies + their linked login users, all downloaded media
 * files, and the sync state. Requires the typed confirmation "DELETE".
 * Wrapped in a transaction so a mid-purge failure leaves the DB consistent.
 */
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

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: private, max-age=3600');
    header('Content-Disposition: inline; filename="' . basename($file) . '"');
    readfile($file);
}
