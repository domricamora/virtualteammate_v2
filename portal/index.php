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
    case 'audit.delete':    handle_audit_delete();       break;
    case 'audit.clear':     handle_audit_clear();        break;
    case 'traffic':         handle_traffic_list();       break;
    case 'traffic.delete':  handle_traffic_delete();     break;
    case 'traffic.clear':   handle_traffic_clear();      break;

    /* ───────────────────────── Site cache (super admin) ─────────────────────────── */
    case 'cache.toggle':    handle_cache_toggle();       break;
    case 'cache.flush':     handle_cache_flush();        break;

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

    /* ───────────────────────── Email composer (super admin) ─────────────────────────── */
    case 'email':                  handle_email_compose();           break;
    case 'email.send':             handle_email_send();              break;
    case 'email.settings':         handle_email_save_settings();     break;

    /* ───────────────────────── Leads (super admin) ─────────────────────────── */
    case 'leads':                  handle_leads_list();              break;
    case 'leads.delete':           handle_leads_delete();            break;

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
    case 'tasks.attach':           handle_tasks_attach();            break;
    case 'tasks.attachment':       handle_tasks_attachment_serve();  break;
    case 'tasks.attachment.delete':handle_tasks_attachment_delete(); break;
    case 'workday':                handle_workday_list();            break;
    case 'notifications':              handle_notifications_list();           break;
    case 'notifications.read':         handle_notifications_read();           break;
    case 'notifications.delete':       handle_notifications_delete();         break;
    case 'notifications.delete_all':   handle_notifications_delete_all();     break;
    case 'notifications.toggle_email': handle_notifications_toggle_email();   break;
    case 'resources':              handle_resources();               break;
    case 'knowledge-center':       handle_knowledge_center();        break;
    case 'payslips':               handle_payslips();                break;
    case 'my-team':                handle_my_team();                 break;
    case 'vtm-apps':               handle_vtm_apps();                break;
    case 'refer':                  handle_refer();                   break;
    case 'my-vts':                 handle_my_vts();                  break;
    case 'my-clients':             handle_my_clients();              break;
    case 'vts.profile_json':       handle_vts_profile_json();        break;
    case 'messages':               handle_messages_list();           break;
    case 'messages.send':          handle_messages_send();           break;
    case 'messages.fetch':         handle_messages_fetch();          break;
    case 'messages.clear':         handle_messages_clear();          break;
    case 'invoices':               handle_invoices();                break;
    case 'request-vt':             handle_request_vt();              break;
    case 'request-vt.decide':      handle_request_decide();          break;
    case 'special-links':          handle_special_links();           break;
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
        case 'super_admin': render('dashboard-super', ['user' => $u, 'stats' => dashboard_super_stats(), 'traffic' => dashboard_traffic_summary(), 'trend' => dashboard_trend_series(14), 'cache' => cache_state()]); break;
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

/** Daily leads + traffic (page-view) counts for the last N days, oldest→newest,
 *  for the animated dashboard chart. Missing days are zero-filled. */
function dashboard_trend_series(int $days = 14): array
{
    $pdo   = db();
    $since = "datetime('now','-" . ($days - 1) . " days','start of day')";
    $byLeads = $byTraffic = [];
    try {
        if ((bool) $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='leads'")->fetchColumn()) {
            foreach ($pdo->query("SELECT date(created_at) d, COUNT(*) n FROM leads WHERE created_at >= $since GROUP BY d") as $r) {
                $byLeads[$r['d']] = (int) $r['n'];
            }
        }
    } catch (Throwable $_) {}
    try {
        if (traffic_table_exists()) {
            foreach ($pdo->query("SELECT date(created_at) d, COUNT(*) n FROM traffic WHERE created_at >= $since GROUP BY d") as $r) {
                $byTraffic[$r['d']] = (int) $r['n'];
            }
        }
    } catch (Throwable $_) {}
    $labels = $leads = $traffic = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $d = gmdate('Y-m-d', time() - $i * 86400);
        $labels[]  = $d;
        $leads[]   = $byLeads[$d] ?? 0;
        $traffic[] = $byTraffic[$d] ?? 0;
    }
    return ['labels' => $labels, 'leads' => $leads, 'traffic' => $traffic];
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

    // Recent messages — last 5 receipts to this user. Messages live in the
    // separate chat DB, so fetch them there then resolve sender names from the
    // main DB (no cross-database join).
    $recentMessages = [];
    try {
        $cstmt = chatdb()->prepare(
            "SELECT * FROM messages WHERE receiver_user_id = :uid ORDER BY id DESC LIMIT 5"
        );
        $cstmt->execute([':uid' => $u['id']]);
        $recentMessages = $cstmt->fetchAll();
        if ($recentMessages) {
            $ids   = implode(',', array_unique(array_map(static fn($m) => (int) $m['sender_user_id'], $recentMessages)));
            $names = db()->query(
                "SELECT id, first_name AS s_fn, last_name AS s_ln, email AS s_email, photo_url AS s_photo
                 FROM users WHERE id IN ({$ids})"
            )->fetchAll(PDO::FETCH_UNIQUE);
            foreach ($recentMessages as &$m) {
                $n = $names[(int) $m['sender_user_id']] ?? [];
                $m['s_fn'] = $n['s_fn'] ?? ''; $m['s_ln'] = $n['s_ln'] ?? '';
                $m['s_email'] = $n['s_email'] ?? ''; $m['s_photo'] = $n['s_photo'] ?? '';
            }
            unset($m);
        }
    } catch (Throwable $_) {}

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

    // 14-day activity trend (EOD submissions, VT assignments, meetings),
    // scoped to this CSM's accounts. Zero-filled for the animated chart.
    $pdo       = db();
    $days      = 14;
    $ids       = array_map(static fn ($c) => (int) $c['id'], $clients);
    $inList    = $ids ? implode(',', $ids) : '0';
    $since     = "datetime('now','-" . ($days - 1) . " days','start of day')";
    $sinceDate = gmdate('Y-m-d', time() - ($days - 1) * 86400);
    $byTasks = $byMeet = $byEod = [];
    try { foreach ($pdo->query("SELECT date(created_at) d, COUNT(*) n FROM tasks WHERE client_id IN ({$inList}) AND created_at >= {$since} GROUP BY d") as $r) { $byTasks[$r['d']] = (int) $r['n']; } } catch (Throwable $_) {}
    try { foreach ($pdo->query("SELECT date(scheduled_at) d, COUNT(*) n FROM meetings WHERE client_id IN ({$inList}) AND scheduled_at >= {$since} GROUP BY d") as $r) { $byMeet[$r['d']] = (int) $r['n']; } } catch (Throwable $_) {}
    try { foreach ($pdo->query("SELECT report_date d, COUNT(*) n FROM eod_reports WHERE vt_user_id IN (SELECT vt_user_id FROM client_vts WHERE client_id IN ({$inList})) AND report_date >= '{$sinceDate}' GROUP BY report_date") as $r) { $byEod[$r['d']] = (int) $r['n']; } } catch (Throwable $_) {}
    $labels = $eodS = $taskS = $meetS = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $d = gmdate('Y-m-d', time() - $i * 86400);
        $labels[] = $d; $eodS[] = $byEod[$d] ?? 0; $taskS[] = $byTasks[$d] ?? 0; $meetS[] = $byMeet[$d] ?? 0;
    }

    $vtCount = 0;
    try {
        $vc = $pdo->prepare(
            "SELECT COUNT(DISTINCT cv.vt_user_id)
             FROM csm_clients cc
             JOIN client_vts cv ON cv.client_id = cc.client_id AND cv.contract_status = 'active'
             WHERE cc.csm_user_id = :uid"
        );
        $vc->execute([':uid' => $u['id']]);
        $vtCount = (int) $vc->fetchColumn();
    } catch (Throwable $_) {}

    return [
        'clients'  => $clients,
        'meetings' => $meet->fetchAll(),
        'vt_count' => $vtCount,
        'trend'    => ['labels' => $labels, 'eod' => $eodS, 'tasks' => $taskS, 'meetings' => $meetS],
    ];
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
            "SELECT cv.*, c.company_name, c.id AS cid
             FROM client_vts cv
             JOIN clients c ON c.id = cv.client_id
             WHERE cv.vt_user_id = :uid AND cv.contract_status = 'active'
             LIMIT 1"
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
        "SELECT cv.*, u.first_name, u.last_name, u.email, u.photo_url, u.country,
                p.department, p.role_title, p.experience_years, p.english_level,
                p.workday_link AS profile_workday_link
         FROM client_vts cv
         JOIN users u ON u.id = cv.vt_user_id
         LEFT JOIN vt_profiles p ON p.user_id = cv.vt_user_id
         WHERE cv.client_id = :cid AND cv.contract_status = 'active'
         ORDER BY u.first_name"
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
        // The URL fields render blank (we don't echo the stored URL back). So a
        // blank field means "keep what's already saved"; a pasted URL replaces
        // it; an uploaded file (handled below) overrides either.
        $photoIn = trim((string) ($_POST['photo_url'] ?? ''));
        $coverIn = trim((string) ($_POST['cover_url'] ?? ''));
        $photo   = $photoIn !== '' ? $photoIn : (string) ($u['photo_url'] ?? '');
        $cover   = $coverIn !== '' ? $coverIn : (string) ($u['cover_url'] ?? '');

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
                    'INSERT INTO users (email, password_hash, role, first_name, last_name, phone, country, photo_url, active, notify_by_email)
                     VALUES (:e, :h, :r, :fn, :ln, :p, :c, :ph, :a, 1)'
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
    $mediaFiles = delete_user_media($id);   // remove photo (vtmedia) + resume/video (data/media)
    db()->prepare('DELETE FROM users WHERE id = :id')->execute([':id' => $id]);
    audit_log('delete', 'user', $id, "media_files={$mediaFiles}");
    flash('success', 'User deleted' . ($mediaFiles ? " (and {$mediaFiles} media file" . ($mediaFiles === 1 ? '' : 's') . ')' : '') . '.');
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
    // Resolve the owning user so we can also remove their media files on disk.
    $st = db()->prepare('SELECT user_id FROM vt_profiles WHERE id = :id');
    $st->execute([':id' => $id]);
    $uid = (int) ($st->fetchColumn() ?: 0);
    $mediaFiles = delete_user_media($uid);
    db()->prepare('DELETE FROM vt_profiles WHERE id = :id')->execute([':id' => $id]);
    audit_log('delete', 'vt_profile', $id, "user_id={$uid} media_files={$mediaFiles}");
    flash('success', 'VT profile deleted' . ($mediaFiles ? " (and {$mediaFiles} media file" . ($mediaFiles === 1 ? '' : 's') . ')' : '') . '.');
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
    foreach ($pdo->query("SELECT client_id, vt_user_id FROM client_vts WHERE contract_status = 'active'")->fetchAll() as $r) {
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
        db()->prepare("INSERT OR IGNORE INTO client_vts (client_id, vt_user_id, contract_status) VALUES (:cl, :v, 'active')")
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
            // VT sees any meeting they're invited to — through the
            // meeting_attendees table (preferred) OR the legacy single
            // attendee_user_id column (back-compat).
            $sql .= ' WHERE (m.attendee_user_id = :uid
                         OR EXISTS (SELECT 1 FROM meeting_attendees ma
                                    WHERE ma.meeting_id = m.id AND ma.user_id = :uid))';
            $params[':uid'] = $u['id'];
            break;
    }
    $sql .= ' ORDER BY m.scheduled_at DESC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $meetings = $stmt->fetchAll();

    // Pull every attendee for the visible meetings in one query, then group
    // by meeting_id so the view can render the full invitee list.
    $attendeesByMeeting = [];
    if ($meetings) {
        $ids = array_map(fn($m) => (int) $m['id'], $meetings);
        $ph  = implode(',', array_fill(0, count($ids), '?'));
        $a = db()->prepare(
            "SELECT ma.meeting_id, u.id, u.first_name, u.last_name, u.email, u.role
             FROM meeting_attendees ma
             JOIN users u ON u.id = ma.user_id
             WHERE ma.meeting_id IN ($ph)
             ORDER BY u.first_name, u.last_name"
        );
        $a->execute($ids);
        foreach ($a as $row) {
            $attendeesByMeeting[(int) $row['meeting_id']][] = [
                'id'         => (int) $row['id'],
                'first_name' => $row['first_name'],
                'last_name'  => $row['last_name'],
                'email'      => $row['email'],
                'role'       => $row['role'],
            ];
        }
    }
    render('meetings-list', [
        'meetings'             => $meetings,
        'user'                 => $u,
        'attendees_by_meeting' => $attendeesByMeeting,
    ]);
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
        // Tighten scope: a client may only edit meetings on their own account;
        // a csm may only edit meetings on a client they're assigned to.
        if (!meetings_user_can_manage($u, (int) $meeting['client_id'])) {
            flash('error', 'You can not edit this meeting.');
            redirect(portal_url('meetings'));
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $clientId    = (int) ($_POST['client_id'] ?? 0);
        // Multi-attendee: accept attendee_user_ids[] from the multi-select
        // picker. Fall back to the legacy single-select attendee_user_id so
        // older form posts (or other entry points) still work.
        $rawAttendees = $_POST['attendee_user_ids'] ?? null;
        $attendeeIds  = [];
        if (is_array($rawAttendees)) {
            foreach ($rawAttendees as $aid) {
                $aid = (int) $aid;
                if ($aid > 0 && !in_array($aid, $attendeeIds, true)) { $attendeeIds[] = $aid; }
            }
        } else {
            $legacyAttendee = (int) ($_POST['attendee_user_id'] ?? 0);
            if ($legacyAttendee > 0) { $attendeeIds[] = $legacyAttendee; }
        }
        // The legacy single-attendee column stays in sync with the FIRST
        // attendee so old code paths (dashboard "Today's meetings" widget,
        // older queries) still surface a name.
        $primaryAttendee = $attendeeIds[0] ?? null;
        $role        = (string) ($_POST['meeting_with_role'] ?? 'csm');
        $topic       = trim((string) ($_POST['topic'] ?? ''));
        $notes       = trim((string) ($_POST['notes'] ?? ''));
        $status      = (string) ($_POST['status'] ?? 'scheduled');
        $meetingLink = trim((string) ($_POST['meeting_link'] ?? ''));
        $callApp     = strtolower(trim((string) ($_POST['call_app'] ?? 'zoom')));
        if (!in_array($callApp, ['zoom','google_meet','teams','webex','phone','other'], true)) {
            $callApp = 'other';
        }

        // Combine Day + Starts/Ends. The form posts three separate inputs to
        // match the user-facing layout; the DB still keeps a canonical
        // scheduled_at + end_at (ISO datetime).
        $day   = trim((string) ($_POST['day']   ?? ''));
        $start = trim((string) ($_POST['start'] ?? ''));
        $end   = trim((string) ($_POST['end']   ?? ''));
        // Fallback if the legacy combined input is posted.
        $legacy = trim((string) ($_POST['scheduled_at'] ?? ''));
        $startAt = '';
        $endAt   = '';
        if ($day !== '' && $start !== '') {
            $startAt = $day . ' ' . $start . (strlen($start) === 5 ? ':00' : '');
        } elseif ($legacy !== '') {
            $startAt = str_replace('T', ' ', $legacy);
        }
        if ($day !== '' && $end !== '') {
            $endAt = $day . ' ' . $end . (strlen($end) === 5 ? ':00' : '');
        }

        // Derive duration from start/end so old reports keep working.
        $duration = 30;
        if ($startAt !== '' && $endAt !== '') {
            try {
                $diff = (new DateTime($endAt))->getTimestamp() - (new DateTime($startAt))->getTimestamp();
                if ($diff > 0) { $duration = (int) round($diff / 60); }
            } catch (Throwable $_) {}
        } else {
            $duration = max(15, (int) ($_POST['duration_minutes'] ?? 30));
        }

        if ($clientId < 1 || $startAt === '' || $topic === '' || !in_array($role, ['csm','vt'], true)) {
            flash('error', 'Meeting name, client account, and day/start time are required.');
            redirect(portal_url('meetings.edit', ['id'=>$id]));
        }
        // Defense-in-depth: the posted client_id must be one the caller can
        // attach a meeting to (super_admin: any; client: own; csm: assigned).
        if (!meetings_user_can_manage($u, $clientId)) {
            flash('error', 'You can not schedule a meeting on that client account.');
            redirect(portal_url('meetings.edit', ['id'=>$id]));
        }
        // Same check for EVERY attendee — must be one of the role-scoped
        // candidates we'd render in the form (prevents form-tampering with
        // arbitrary user ids).
        if ($attendeeIds) {
            $allowedAttendeeIds = array_map(static fn($c) => (int) $c['id'], meetings_scoped_candidates($u));
            foreach ($attendeeIds as $aid) {
                if (!in_array($aid, $allowedAttendeeIds, true)) {
                    flash('error', 'One of the selected invitees is not available for your account.');
                    redirect(portal_url('meetings.edit', ['id'=>$id]));
                }
            }
        }
        if ($endAt !== '' && $endAt < $startAt) {
            flash('error', 'End time must be after start time.');
            redirect(portal_url('meetings.edit', ['id'=>$id]));
        }
        if (!in_array($status, ['scheduled','completed','cancelled'], true)) { $status = 'scheduled'; }

        if ($id > 0) {
            db()->prepare(
                'UPDATE meetings
                 SET client_id=:c, attendee_user_id=:a, meeting_with_role=:r,
                     scheduled_at=:w, end_at=:e, duration_minutes=:d,
                     meeting_link=:ml, call_app=:ca,
                     topic=:t, notes=:n, status=:s, updated_at=CURRENT_TIMESTAMP
                 WHERE id=:id'
            )->execute([
                ':c'=>$clientId, ':a'=>$primaryAttendee, ':r'=>$role,
                ':w'=>$startAt, ':e'=>$endAt, ':d'=>$duration,
                ':ml'=>$meetingLink, ':ca'=>$callApp,
                ':t'=>$topic, ':n'=>$notes, ':s'=>$status, ':id'=>$id,
            ]);
            audit_log('update', 'meeting', $id);
            $finalId = $id;
        } else {
            db()->prepare(
                'INSERT INTO meetings
                   (client_id, organizer_user_id, attendee_user_id, meeting_with_role,
                    scheduled_at, end_at, duration_minutes, meeting_link, call_app,
                    topic, notes, status)
                 VALUES (:c, :o, :a, :r, :w, :e, :d, :ml, :ca, :t, :n, :s)'
            )->execute([
                ':c'=>$clientId, ':o'=>$u['id'], ':a'=>$primaryAttendee, ':r'=>$role,
                ':w'=>$startAt, ':e'=>$endAt, ':d'=>$duration,
                ':ml'=>$meetingLink, ':ca'=>$callApp,
                ':t'=>$topic, ':n'=>$notes, ':s'=>$status,
            ]);
            $finalId = (int) db()->lastInsertId();
            audit_log('create', 'meeting', $finalId);
        }

        // Sync attendees: figure out who's NEW (vs already on the meeting)
        // so we only fire one notification per actually-new invite.
        $existingAttendees = [];
        if ($id > 0) {
            $existingAttendees = db()->query("SELECT user_id FROM meeting_attendees WHERE meeting_id = {$finalId}")
                ->fetchAll(PDO::FETCH_COLUMN);
            $existingAttendees = array_map('intval', $existingAttendees);
        }
        // Wipe + re-insert is simplest and lets us also drop people who
        // were removed from the invite list.
        db()->prepare('DELETE FROM meeting_attendees WHERE meeting_id = :m')->execute([':m' => $finalId]);
        $insAtt = db()->prepare('INSERT OR IGNORE INTO meeting_attendees (meeting_id, user_id) VALUES (:m, :u)');
        foreach ($attendeeIds as $aid) {
            $insAtt->execute([':m' => $finalId, ':u' => $aid]);
        }
        // Notify everyone newly added (don't re-spam the existing attendees
        // every time the organizer edits the notes).
        $organizerName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
        if ($organizerName === '') { $organizerName = (string) ($u['email'] ?? 'A teammate'); }
        $newlyInvited = array_diff($attendeeIds, $existingAttendees);
        if ($newlyInvited) {
            meetings_notify_invitees($newlyInvited, $finalId, $topic, $organizerName, $startAt);
        }
        // On EDIT (not create), ping the existing attendees with a
        // "details changed" notice — minus the organizer themselves and
        // minus anyone who was just newly invited (they get the invite).
        if ($id > 0) {
            $kept = array_diff($existingAttendees, $newlyInvited);
            $kept = array_diff($kept, [(int) $u['id']]);
            $whenLabel = '';
            if ($startAt !== '') {
                try { $whenLabel = (new DateTime($startAt))->format('M j, Y · g:i a'); }
                catch (Throwable $_) { $whenLabel = $startAt; }
            }
            foreach ($kept as $uid) {
                notify(
                    (int) $uid, 'meeting',
                    'Meeting updated: ' . mb_substr($topic !== '' ? $topic : 'Untitled meeting', 0, 80),
                    $organizerName . ' updated the details'
                        . ($whenLabel !== '' ? ' for the meeting on ' . $whenLabel : '') . '.',
                    'index.php?p=meetings.edit&id=' . $finalId,
                );
            }
        }

        flash('success', 'Meeting saved.');
        redirect(portal_url('meetings'));
    }

    // Client + attendee scoping for the form. Clients see only their own
    // accounts + only CSMs and VTs actually assigned to them; csms see only
    // the clients they manage + the VTs in those engagements; super_admin
    // sees the entire org.
    $clients    = meetings_scoped_clients($u);
    $candidates = meetings_scoped_candidates($u);

    // Existing attendees on edit — feeds the multi-select preselection.
    $selectedAttendeeIds = [];
    if ($meeting) {
        $stmt = db()->prepare('SELECT user_id FROM meeting_attendees WHERE meeting_id = :m');
        $stmt->execute([':m' => (int) $meeting['id']]);
        $selectedAttendeeIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
    render('meetings-edit', [
        'meeting'              => $meeting,
        'clients'              => $clients,
        'candidates'           => $candidates,
        'user'                 => $u,
        'selected_attendee_ids'=> $selectedAttendeeIds,
    ]);
}

/**
 * Send a "you've been invited" notification to a list of user ids.
 * Used by handle_meetings_edit when attendees are added.
 */
function meetings_notify_invitees(array $userIds, int $meetingId, string $topic, string $organizerName, string $startAt): void
{
    if (!$userIds) { return; }
    $whenLabel = '';
    if ($startAt !== '') {
        try { $whenLabel = (new DateTime($startAt))->format('M j, Y · g:i a'); }
        catch (Throwable $_) { $whenLabel = $startAt; }
    }
    $title = 'Meeting invite: ' . mb_substr($topic !== '' ? $topic : 'Untitled meeting', 0, 80);
    $body  = $organizerName . ' invited you to a meeting'
           . ($whenLabel !== '' ? ' on ' . $whenLabel : '') . '.';
    $link  = 'index.php?p=meetings.edit&id=' . $meetingId;
    foreach ($userIds as $uid) { notify((int) $uid, 'meeting', $title, $body, $link); }
}

/** True if $u may edit / delete a meeting on $clientId. */
function meetings_user_can_manage(array $u, int $clientId): bool
{
    if ($u['role'] === 'super_admin') { return true; }
    if ($clientId <= 0) { return false; }
    if ($u['role'] === 'client') {
        $stmt = db()->prepare('SELECT 1 FROM clients WHERE id = :c AND user_id = :u');
        $stmt->execute([':c' => $clientId, ':u' => $u['id']]);
        return (bool) $stmt->fetchColumn();
    }
    if ($u['role'] === 'csm') {
        $stmt = db()->prepare('SELECT 1 FROM csm_clients WHERE client_id = :c AND csm_user_id = :u');
        $stmt->execute([':c' => $clientId, ':u' => $u['id']]);
        return (bool) $stmt->fetchColumn();
    }
    return false;
}

/** Clients the user is allowed to attach a meeting to. */
function meetings_scoped_clients(array $u): array
{
    if ($u['role'] === 'client') {
        $stmt = db()->prepare('SELECT id, company_name FROM clients WHERE user_id = :u ORDER BY company_name');
        $stmt->execute([':u' => $u['id']]);
        return $stmt->fetchAll();
    }
    if ($u['role'] === 'csm') {
        $stmt = db()->prepare(
            'SELECT c.id, c.company_name FROM csm_clients cc
             JOIN clients c ON c.id = cc.client_id
             WHERE cc.csm_user_id = :u ORDER BY c.company_name'
        );
        $stmt->execute([':u' => $u['id']]);
        return $stmt->fetchAll();
    }
    return db()->query('SELECT id, company_name FROM clients ORDER BY company_name')->fetchAll();
}

/** Attendee candidates the user may schedule with. */
function meetings_scoped_candidates(array $u): array
{
    if ($u['role'] === 'super_admin') {
        return db()->query(
            "SELECT id, first_name, last_name, email, role FROM users
             WHERE role IN ('csm','vt_hired','client') AND active = 1
             ORDER BY role, first_name"
        )->fetchAll();
    }
    if ($u['role'] === 'client') {
        // Their CSM(s) on csm_clients + the hired VTs in client_vts.
        $stmt = db()->prepare(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.role
             FROM clients c
             JOIN csm_clients cc ON cc.client_id = c.id
             JOIN users u ON u.id = cc.csm_user_id AND u.active = 1
             WHERE c.user_id = :u
             UNION
             SELECT u.id, u.first_name, u.last_name, u.email, u.role
             FROM clients c
             JOIN client_vts cv ON cv.client_id = c.id AND cv.contract_status = 'active'
             JOIN users u ON u.id = cv.vt_user_id AND u.active = 1
             WHERE c.user_id = :u
             ORDER BY role, first_name"
        );
        $stmt->execute([':u' => $u['id']]);
        return $stmt->fetchAll();
    }
    if ($u['role'] === 'csm') {
        // The client login users + the VTs across their assigned clients.
        $stmt = db()->prepare(
            "SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, u.role
             FROM csm_clients cc
             JOIN clients c ON c.id = cc.client_id
             LEFT JOIN client_vts cv ON cv.client_id = c.id AND cv.contract_status = 'active'
             JOIN users u ON u.id IN (c.user_id, cv.vt_user_id) AND u.active = 1
             WHERE cc.csm_user_id = :u
             ORDER BY u.role, u.first_name"
        );
        $stmt->execute([':u' => $u['id']]);
        return $stmt->fetchAll();
    }
    return [];
}

function handle_meetings_delete(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('meetings')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = db()->prepare('SELECT id, organizer_user_id, client_id, topic, scheduled_at FROM meetings WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) { redirect(portal_url('meetings')); }
    // Allowed if the caller can manage the client account (super_admin /
    // client on their account / csm on assigned client) OR is the organizer.
    $canDel = meetings_user_can_manage($u, (int) $row['client_id'])
           || ((int) $row['organizer_user_id'] === (int) $u['id']);
    if (!$canDel) {
        flash('error', 'You can not delete this meeting.');
        redirect(portal_url('meetings'));
    }
    // Capture attendees BEFORE delete (the CASCADE wipes meeting_attendees).
    $atts = db()->prepare('SELECT user_id FROM meeting_attendees WHERE meeting_id = :m');
    $atts->execute([':m' => $id]);
    $attendeeIds = array_map('intval', $atts->fetchAll(PDO::FETCH_COLUMN));

    db()->prepare('DELETE FROM meetings WHERE id = :id')->execute([':id'=>$id]);
    audit_log('delete', 'meeting', $id);

    // Notify attendees the meeting was cancelled.
    $topic = (string) ($row['topic'] ?? 'Untitled meeting');
    $when  = '';
    if (!empty($row['scheduled_at'])) {
        try { $when = (new DateTime($row['scheduled_at']))->format('M j, Y · g:i a'); }
        catch (Throwable $_) { $when = (string) $row['scheduled_at']; }
    }
    $actorId = (int) $u['id'];
    foreach ($attendeeIds as $uid) {
        if ($uid === $actorId) { continue; }
        notify(
            (int) $uid, 'meeting',
            'Meeting cancelled: ' . mb_substr($topic, 0, 80),
            'The meeting' . ($when !== '' ? ' on ' . $when : '') . ' has been cancelled.',
            'index.php?p=meetings',
        );
    }

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
        // VTs have no standalone EOD nav — send them back to Productivity
        // (EOD tab), where the form + history live. Others go to the EOD list.
        redirect($isVt ? portal_url('productivity') . '#prod-eod' : portal_url('eod'));
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

function handle_audit_delete(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('audit')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) { redirect(portal_url('audit')); }
    db()->prepare('DELETE FROM audit_log WHERE id = :id')->execute([':id' => $id]);
    // Don't audit_log() the deletion — that creates noise immediately after.
    flash('success', 'Audit entry removed.');
    redirect(portal_url('audit'));
}

function handle_audit_clear(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('audit')); }
    csrf_verify();
    // Defensive: require a typed confirmation token on the POST.
    if (($_POST['confirm'] ?? '') !== 'DELETE ALL') {
        flash('error', 'Confirmation phrase did not match. Audit log untouched.');
        redirect(portal_url('audit'));
    }
    $n = (int) db()->query('SELECT COUNT(*) FROM audit_log')->fetchColumn();
    db()->exec('DELETE FROM audit_log');
    flash('success', "Cleared {$n} audit entries.");
    redirect(portal_url('audit'));
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

function handle_traffic_delete(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('traffic')); }
    csrf_verify();
    if (!traffic_table_exists()) { redirect(portal_url('traffic')); }
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) { redirect(portal_url('traffic')); }
    db()->prepare('DELETE FROM traffic WHERE id = :id')->execute([':id' => $id]);
    audit_log('traffic_delete', 'traffic', $id);
    flash('success', 'Traffic row removed.');
    redirect(portal_url('traffic'));
}

function handle_traffic_clear(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('traffic')); }
    csrf_verify();
    if (!traffic_table_exists()) { redirect(portal_url('traffic')); }
    if (($_POST['confirm'] ?? '') !== 'DELETE ALL') {
        flash('error', 'Confirmation phrase did not match. Traffic log untouched.');
        redirect(portal_url('traffic'));
    }
    $scope = (string) ($_POST['scope'] ?? 'all');
    $pdo = db();
    if ($scope === '30d') {
        $n = (int) $pdo->query("SELECT COUNT(*) FROM traffic WHERE created_at < datetime('now','-30 days')")->fetchColumn();
        $pdo->exec("DELETE FROM traffic WHERE created_at < datetime('now','-30 days')");
        audit_log('traffic_clear', 'traffic', 0, "older than 30 days ({$n} rows)");
        flash('success', "Removed {$n} rows older than 30 days.");
    } else {
        $n = (int) $pdo->query('SELECT COUNT(*) FROM traffic')->fetchColumn();
        $pdo->exec('DELETE FROM traffic');
        audit_log('traffic_clear', 'traffic', 0, "all ({$n} rows)");
        flash('success', "Cleared {$n} traffic rows.");
    }
    redirect(portal_url('traffic'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * SITE CACHE (super admin only)
 * Controls the marketing site's CSS/JS browser cache. State lives in two
 * app_settings keys (cache_enabled, cache_version) and is mirrored to
 * data/cache-state.php, which the public site reads via includes/cache.php to
 * decide each asset's ?v= query string (see vt_asset_ver()).
 * ═════════════════════════════════════════════════════════════════════════ */

/** Current cache state for the dashboard view. */
function cache_state(): array
{
    return [
        'enabled' => get_setting('cache_enabled', '1') !== '0',
        'version' => get_setting('cache_version', ''),
    ];
}

/**
 * Mirror the cache settings to data/cache-state.php so the public marketing
 * site (which never loads the portal/DB) can read them with a cheap include.
 */
function cache_state_sync(): bool
{
    $st  = cache_state();
    $php = "<?php\n"
         . "// Auto-generated by the portal Site Cache control. Do not edit by hand.\n"
         . "return " . var_export(['enabled' => $st['enabled'], 'version' => $st['version']], true) . ";\n";
    $file = __DIR__ . '/../data/cache-state.php';
    $dir  = dirname($file);
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
    return @file_put_contents($file, $php, LOCK_EX) !== false;
}

function handle_cache_toggle(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('dashboard')); }
    csrf_verify();
    $enabled = (($_POST['enabled'] ?? '') === '1');
    set_setting('cache_enabled', $enabled ? '1' : '0');
    $ok = cache_state_sync();
    audit_log('cache_toggle', 'cache', 0, 'enabled=' . ($enabled ? '1' : '0'));
    if (!$ok) {
        flash('error', 'Saved the setting, but could not write data/cache-state.php (check permissions) — the public site may not pick it up.');
    } else {
        flash('success', $enabled
            ? 'Site caching enabled. Browsers cache CSS/JS until the next deploy or flush.'
            : 'Site caching disabled. Every visit re-fetches CSS/JS (no browser caching).');
    }
    redirect(portal_url('dashboard'));
}

function handle_cache_flush(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('dashboard')); }
    csrf_verify();
    // Bump the version token → every ?v= changes once → all visitors re-fetch.
    $version = gmdate('YmdHis');
    set_setting('cache_version', $version);
    $ok = cache_state_sync();
    audit_log('cache_flush', 'cache', 0, 'version=' . $version);
    if (!$ok) {
        flash('error', 'Could not write data/cache-state.php (check permissions). Cache was not flushed.');
    } else {
        flash('success', 'Cache flushed — visitors will pull fresh CSS/JS on their next page load.');
    }
    redirect(portal_url('dashboard'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * EMAIL COMPOSER (super admin only)
 * Compose + send an email through the portal mailer, and a quick "send test"
 * to verify mail delivery on the host.
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_email_compose(): void
{
    $u = require_role('super_admin');
    render('email-compose', [
        'title'      => 'Email',
        'subtitle'   => 'Compose and send an email through the portal mailer, or send a quick test to verify delivery.',
        'user'       => $u,
        'result'     => $_SESSION['email_result'] ?? null,
        'draft'      => $_SESSION['email_draft']  ?? [],
        'lead_email' => get_setting('lead_notify_email', 'nricamora@virtualteammate.com'),
    ]);
    unset($_SESSION['email_result'], $_SESSION['email_draft']);
}

function handle_email_save_settings(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('email')); }
    csrf_verify();
    $addr = trim((string) ($_POST['lead_notify_email'] ?? ''));
    if (!filter_var($addr, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Enter a valid email address for lead notifications.');
        redirect(portal_url('email'));
    }
    set_setting('lead_notify_email', $addr);
    audit_log('lead_email_set', 'settings', null, $addr);
    flash('success', 'Lead notifications will now be sent to ' . $addr . '.');
    redirect(portal_url('email'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * LEADS (super admin) — website lead-form submissions, captured by /lead.php
 * ═════════════════════════════════════════════════════════════════════════ */

/** Shared DDL so the page works even before the first lead is captured. */
function leads_ensure_table(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL DEFAULT '', email TEXT NOT NULL DEFAULT '',
            phone TEXT NOT NULL DEFAULT '', company TEXT NOT NULL DEFAULT '',
            message TEXT NOT NULL DEFAULT '', source TEXT NOT NULL DEFAULT '',
            form TEXT NOT NULL DEFAULT '', vt_id INTEGER NOT NULL DEFAULT 0,
            vt_interest TEXT NOT NULL DEFAULT '', details TEXT NOT NULL DEFAULT '',
            ip TEXT NOT NULL DEFAULT '', created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )"
    );
}

function handle_leads_list(): void
{
    require_role('super_admin');
    $pdo = db();
    leads_ensure_table($pdo);
    // Capture the previous "seen" marker BEFORE updating it, so the view can
    // badge the rows that arrived since the admin last opened this page.
    $prevSeen = get_setting('leads_last_seen_at', '');
    $rows = $pdo->query('SELECT * FROM leads ORDER BY datetime(created_at) DESC, id DESC LIMIT 1000')->fetchAll();
    $newCount = 0;
    foreach ($rows as $r) {
        if ($prevSeen === '' || (string) $r['created_at'] > $prevSeen) { $newCount++; }
    }
    // Mark all current leads as seen so the nav badge clears (uses the newest
    // lead's own timestamp so the comparison basis matches stored values).
    $max = $pdo->query('SELECT MAX(created_at) FROM leads')->fetchColumn();
    if ($max) { set_setting('leads_last_seen_at', (string) $max); }
    render('leads-list', [
        'title'     => 'Leads',
        'subtitle'  => 'Lead-form submissions captured from the marketing website.',
        'rows'      => $rows,
        'last_seen' => $prevSeen,
        'new_count' => $newCount,
    ]);
}

function handle_leads_delete(): void
{
    require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('leads')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        db()->prepare('DELETE FROM leads WHERE id = :id')->execute([':id' => $id]);
        audit_log('lead_delete', 'lead', $id);
        flash('success', 'Lead deleted.');
    }
    redirect(portal_url('leads'));
}

function handle_email_send(): void
{
    $u = require_role('super_admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('email')); }
    csrf_verify();

    $to      = trim((string) ($_POST['to'] ?? ''));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));

    $errors = [];
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) { $errors[] = 'A valid recipient email is required.'; }
    if ($subject === '') { $errors[] = 'A subject is required.'; }
    if ($message === '') { $errors[] = 'A message is required.'; }

    if ($errors) {
        $_SESSION['email_result'] = ['ok' => false, 'msg' => implode(' ', $errors)];
        $_SESSION['email_draft']  = ['to' => $to, 'subject' => $subject, 'message' => $message];
        redirect(portal_url('email'));
    }

    $clean    = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    $bodyHtml = nl2br($clean($message));
    $footer   = 'Sent from the Virtual Teammate portal by ' . $clean((string) ($u['name'] ?: $u['email'])) . '.';
    $html     = portal_email_shell($subject, $bodyHtml, '', $footer, '');
    $ok       = portal_send_mail($to, $subject, $html, $message);

    audit_log('email_send', 'email', null, ($ok ? 'sent: ' : 'FAILED: ') . $to . ' — ' . mb_substr($subject, 0, 80));

    if ($ok) {
        $_SESSION['email_result'] = ['ok' => true, 'msg' => 'Email sent to ' . $to . '.'];
    } else {
        $_SESSION['email_result'] = ['ok' => false, 'msg' => smtp_config() !== null
            ? 'SMTP relay rejected the message — check the App Password / SMTP settings in portal/smtp.local.php (see the server error log for the failing step).'
            : 'Google SMTP isn\'t configured and native mail() could not hand off the message. On localhost this is expected; add portal/smtp.local.php to relay through Google Workspace.'];
        $_SESSION['email_draft']  = ['to' => $to, 'subject' => $subject, 'message' => $message];
    }
    redirect(portal_url('email'));
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
            'INSERT INTO users (email, password_hash, role, first_name, last_name, country, active, notify_by_email)
             VALUES (:e, :h, :r, :fn, :ln, "US", 1, 1)'
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
                "INSERT INTO clients (user_id, company_name, company_email, contract_status)
                 VALUES (:u, :n, :e, 'active')"
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
                "INSERT OR IGNORE INTO client_vts (client_id, vt_user_id, contract_status, started_at, workday_tracker_id)
                 VALUES (:c, :v, 'active', :s, :w)"
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

        // NOTE: deliberately do NOT reset the users AUTOINCREMENT counter. Letting
        // ids keep climbing avoids reusing ids of purged rows (which could collide
        // with stale references elsewhere). Removed per request.

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
    // Clean out BOTH media trees: the gated data/media/<entity> and the public
    // vtmedia/<entity> (photos). Spare per-id dirs that belong to demo users.
    $fileCount = 0;
    foreach (['data/media', 'vtmedia'] as $rel) {
        foreach (['vt', 'client', 'csm'] as $entity) {
            $base = __DIR__ . '/../' . $rel . '/' . $entity;
            if (!is_dir($base)) { continue; }
            foreach (glob($base . '/*', GLOB_ONLYDIR) ?: [] as $idDir) {
                if (isset($demoIds[(int) basename($idDir)])) { continue; } // spare demo media
                $fileCount += rrmdir($idDir);
            }
        }
    }
    // Public 150x150 thumbnails (vtmedia/vt_thumbs/<id>.<ext>), sparing demo ids.
    foreach (glob(__DIR__ . '/../vtmedia/vt_thumbs/*') ?: [] as $thumb) {
        if (is_dir($thumb) || isset($demoIds[(int) pathinfo($thumb, PATHINFO_FILENAME)])) { continue; }
        if (@unlink($thumb)) { $fileCount++; }
    }

    hs_control('reset');

    audit_log('hs_purge_all', 'hubspot', null, "clients=$cClients users=$cUsers media=$fileCount");
    flash('success', "Hard-purged: {$cClients} clients, {$cUsers} users (incl. CSMs), {$fileCount} media files. Demo users spared. Sync state reset.");
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
    $cClients = $cClientUsers = $cUsers = $cCsm = 0;
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

        // CSM users created from a company's HubSpot Owner have ONLY
        // hubspot_owner_id set (no contact id), so the clause above misses
        // them. Sweep those too (spare demo + any super_admin owner).
        $cCsm = $pdo->exec(
            "DELETE FROM users
              WHERE role = 'csm' AND hubspot_owner_id != ''
                AND email NOT LIKE 'demo-%'"
        );

        // NOTE: deliberately do NOT reset the users AUTOINCREMENT counter (removed
        // per request) — ids keep climbing so purged ids are never reused.

        $pdo->commit();
    } catch (Throwable $ex) {
        $pdo->rollBack();
        flash('error', 'Purge failed: ' . $ex->getMessage());
        redirect(portal_url('hubspot'));
    }

    // Wipe downloaded media from BOTH trees: gated data/media/vt and public
    // vtmedia/vt, plus the public 150x150 thumbnails in vtmedia/vt_thumbs.
    $fileCount = 0;
    foreach ([__DIR__ . '/../data/media/vt', __DIR__ . '/../vtmedia/vt'] as $mediaDir) {
        foreach (glob($mediaDir . '/*', GLOB_ONLYDIR) ?: [] as $idDir) {
            $fileCount += rrmdir($idDir);
        }
    }
    foreach (glob(__DIR__ . '/../vtmedia/vt_thumbs/*') ?: [] as $thumb) {
        if (!is_dir($thumb) && @unlink($thumb)) { $fileCount++; }
    }

    hs_control('reset');

    $totalUsers = $cUsers + $cClientUsers + $cCsm;
    audit_log('hs_purge', 'hubspot', null, "clients=$cClients users=$totalUsers csm=$cCsm media_files=$fileCount");
    flash('success', "Purged: {$cClients} HubSpot clients, {$totalUsers} synced users (incl. {$cCsm} owner-only CSMs), {$fileCount} media files. Sync state reset.");
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
        "SELECT p.*, u.email, u.first_name, u.last_name, u.phone, u.country, u.photo_url, u.cover_url, u.active, u.last_login_at, u.hubspot_contact_id, u.job_title
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
/* ═════════════════════════════════════════════════════════════════════════
 * TASK MANAGEMENT — full CRUD per role, calendar view, file attachments.
 *
 * Permissions matrix (enforced by tasks_can_*):
 *   super_admin : create / read / update / delete / toggle / attach / detach
 *                 — any task in the system, can assign to any VT (even
 *                   without a client context: client_id is nullable now).
 *   client      : create / read / update / delete / toggle / attach / detach
 *                 — tasks where task.client_id matches their company.
 *   csm         : read / update / delete / toggle / attach / detach
 *                 — tasks where task.client_id is one of their assigned
 *                   clients (csm_clients).
 *   vt_hired    : read / update / toggle / attach
 *                 — tasks where task.assignee_user_id is themselves. Cannot
 *                   delete or reassign; cannot remove other people's
 *                   attachments (only their own uploads).
 * ═════════════════════════════════════════════════════════════════════════ */

/** Returns the client_id a non-super-admin user is scoped to (or 0). */
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
        $cid = (int) ($_GET['client_id'] ?? $_POST['client_id'] ?? 0);
        if ($cid <= 0) { return 0; }
        $stmt = db()->prepare('SELECT 1 FROM csm_clients WHERE csm_user_id = :uid AND client_id = :cid LIMIT 1');
        $stmt->execute([':uid' => $u['id'], ':cid' => $cid]);
        return $stmt->fetchColumn() ? $cid : 0;
    }
    return 0;
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

/** True if $u may view this task at all. */
function tasks_can_view(array $u, array $task): bool
{
    if ($u['role'] === 'super_admin') { return true; }
    $cid = (int) ($task['client_id'] ?? 0);
    if ($cid > 0 && tasks_user_can_edit_client($u, $cid)) { return true; }
    return (int) ($task['assignee_user_id'] ?? 0) === (int) $u['id'];
}

/** True if $u may edit (title/desc/assignee/priority/due_date) this task. */
function tasks_can_edit(array $u, array $task): bool
{
    if ($u['role'] === 'super_admin') { return true; }
    $cid = (int) ($task['client_id'] ?? 0);
    if ($cid > 0 && tasks_user_can_edit_client($u, $cid)) { return true; }
    // VT can update their own task (description, status, due date) per spec.
    return $u['role'] === 'vt_hired'
        && (int) ($task['assignee_user_id'] ?? 0) === (int) $u['id'];
}

/** True if $u may delete this task entirely. VTs never can. */
function tasks_can_delete(array $u, array $task): bool
{
    if ($u['role'] === 'vt_hired') { return false; }
    return tasks_can_edit($u, $task);
}

/** True if $u can mark this task complete / re-open. */
function tasks_can_toggle(array $u, array $task): bool { return tasks_can_view($u, $task); }

/** True if $u can create a brand-new task in the given (optional) client scope. */
function tasks_can_create(array $u, int $cid): bool
{
    if ($u['role'] === 'super_admin') { return true; }
    if ($cid > 0) { return tasks_user_can_edit_client($u, $cid); }
    // client/csm require a client context to create.
    return false;
}

function tasks_notify_assignee(int $userId, int $taskId, string $title, int $cid): void
{
    if ($userId <= 0) { return; }
    notify(
        $userId,
        'task',
        'New task assigned: ' . mb_substr($title, 0, 80),
        'A new task has been assigned to you. Click "Open in portal" to view the details and start working.',
        'index.php?p=tasks.edit&id=' . $taskId,
    );
}

/** All VTs visible to $u for the "Assign to" picker. Scoped per role. */
function tasks_assignable_users(array $u, int $cid): array
{
    $pdo = db();
    if ($u['role'] === 'super_admin') {
        // Super admin can assign across the whole pool.
        return $pdo->query(
            "SELECT id AS user_id, first_name, last_name, email
             FROM users WHERE role = 'vt_hired' AND active = 1
             ORDER BY first_name, last_name"
        )->fetchAll();
    }
    if ($cid > 0) {
        return client_hired_vts($cid);
    }
    return [];
}

/**
 * Resolve the list scope + pull matching tasks for the calling user.
 * Returns ['scope', 'tasks', 'client_id'] — scope is one of:
 *   all   — super_admin viewing every task (optionally filtered by client)
 *   client — client/csm scoped to one client_id
 *   mine  — vt_hired viewing assigned tasks only
 */
function tasks_fetch_for_user(array $u): array
{
    $pdo = db();

    if ($u['role'] === 'vt_hired') {
        $stmt = $pdo->prepare(
            "SELECT t.*, c.company_name,
                    u.first_name AS a_fn, u.last_name AS a_ln, u.email AS a_email
             FROM tasks t
             LEFT JOIN clients c ON c.id = t.client_id
             LEFT JOIN users u   ON u.id = t.assignee_user_id
             WHERE t.assignee_user_id = :uid
             ORDER BY t.status = 'active' DESC, IFNULL(t.due_date,'9999-12-31'),
                      CASE t.priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END,
                      t.created_at DESC"
        );
        $stmt->execute([':uid' => $u['id']]);
        return ['scope' => 'mine', 'tasks' => $stmt->fetchAll(), 'client_id' => 0];
    }

    if ($u['role'] === 'super_admin') {
        // ?client_id=X is optional. With no filter we surface everything,
        // so the calendar / table cover every active task in the system.
        $cid = (int) ($_GET['client_id'] ?? 0);
        $sql = "SELECT t.*, c.company_name,
                       u.first_name AS a_fn, u.last_name AS a_ln, u.email AS a_email
                FROM tasks t
                LEFT JOIN clients c ON c.id = t.client_id
                LEFT JOIN users u   ON u.id = t.assignee_user_id";
        $params = [];
        if ($cid > 0) { $sql .= ' WHERE t.client_id = :cid'; $params[':cid'] = $cid; }
        $sql .= " ORDER BY t.status = 'active' DESC,
                  CASE t.priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END,
                  IFNULL(t.due_date,'9999-12-31'), t.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return ['scope' => 'all', 'tasks' => $stmt->fetchAll(), 'client_id' => $cid];
    }

    // client / csm
    if ($u['role'] === 'csm') {
        // CSM may want to see every task across their clients OR one client.
        $cid = (int) ($_GET['client_id'] ?? 0);
        if ($cid > 0 && !tasks_user_can_edit_client($u, $cid)) { $cid = 0; }
        $sql = "SELECT t.*, c.company_name,
                       u.first_name AS a_fn, u.last_name AS a_ln, u.email AS a_email
                FROM tasks t
                JOIN clients c ON c.id = t.client_id
                JOIN csm_clients cc ON cc.client_id = c.id AND cc.csm_user_id = :uid
                LEFT JOIN users u  ON u.id = t.assignee_user_id";
        $params = [':uid' => (int) $u['id']];
        if ($cid > 0) { $sql .= ' AND t.client_id = :cid'; $params[':cid'] = $cid; }
        $sql .= " ORDER BY t.status = 'active' DESC,
                  CASE t.priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END,
                  IFNULL(t.due_date,'9999-12-31'), t.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return ['scope' => 'client', 'tasks' => $stmt->fetchAll(), 'client_id' => $cid];
    }

    // client
    $cid = tasks_resolve_client_id($u);
    if ($cid <= 0) {
        return ['scope' => 'client', 'tasks' => [], 'client_id' => 0];
    }
    $stmt = $pdo->prepare(
        "SELECT t.*, c.company_name,
                u.first_name AS a_fn, u.last_name AS a_ln, u.email AS a_email
         FROM tasks t
         LEFT JOIN clients c ON c.id = t.client_id
         LEFT JOIN users u   ON u.id = t.assignee_user_id
         WHERE t.client_id = :cid
         ORDER BY t.status = 'active' DESC,
                  CASE t.priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END,
                  IFNULL(t.due_date,'9999-12-31'), t.created_at DESC"
    );
    $stmt->execute([':cid' => $cid]);
    return ['scope' => 'client', 'tasks' => $stmt->fetchAll(), 'client_id' => $cid];
}

function handle_tasks_list(): void
{
    $u = require_login();
    if (!in_array($u['role'], ['super_admin','client','csm','vt_hired'], true)) {
        render('error', ['title' => 'Forbidden', 'message' => 'No task list for your role.']);
        return;
    }
    $bundle = tasks_fetch_for_user($u);
    $tasks  = $bundle['tasks'];

    // Calendar inputs (the same dataset, just labeled for the calendar view).
    $view = (string) ($_GET['view'] ?? 'list');
    if (!in_array($view, ['list','calendar','year'], true)) { $view = 'list'; }
    $year  = (int) ($_GET['y'] ?? date('Y'));
    $month = (int) ($_GET['m'] ?? date('n'));
    if ($month < 1 || $month > 12) { $month = (int) date('n'); }
    if ($year  < 2000 || $year > 2100) { $year = (int) date('Y'); }

    // Bundle dated tasks into a YYYY-MM-DD map for fast calendar lookup.
    $tasksByDate = [];
    foreach ($tasks as $t) {
        $d = (string) ($t['due_date'] ?? '');
        if ($d === '') { continue; }
        $tasksByDate[substr($d, 0, 10)][] = $t;
    }

    // Assignees: super_admin sees the whole VT pool; other roles only see
    // their client's hired VTs.
    $assignees = tasks_assignable_users($u, (int) $bundle['client_id']);

    // Attachment counts per task (one query → keyed map).
    $attachCounts = [];
    if (!empty($tasks)) {
        $ids = array_map(fn($t) => (int) $t['id'], $tasks);
        $ph  = implode(',', array_fill(0, count($ids), '?'));
        $stmt = db()->prepare("SELECT task_id, COUNT(*) AS n FROM task_attachments WHERE task_id IN ($ph) GROUP BY task_id");
        $stmt->execute($ids);
        foreach ($stmt as $row) { $attachCounts[(int) $row['task_id']] = (int) $row['n']; }
    }

    render('tasks-list', [
        'user'           => $u,
        'tasks'          => $tasks,
        'scope'          => $bundle['scope'],
        'client_id'      => (int) $bundle['client_id'],
        'assignees'      => $assignees,
        // Name avoids collision with $view inside render() (the view filename).
        'tm_view'        => $view,
        'cal_year'       => $year,
        'cal_month'      => $month,
        'tasks_by_date'  => $tasksByDate,
        'attach_counts'  => $attachCounts,
    ]);
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
        if (!tasks_can_view($u, $task)) {
            render('error', ['title' => 'Forbidden', 'message' => 'You can not view this task.']);
            return;
        }
    }

    // For a NEW task, client_id may be supplied via ?client_id=… (super admin
    // / csm) or auto-resolved (client). super_admin can also create with no
    // client (cross-client). Anyone else must have a client context to create.
    $cid = $task ? (int) $task['client_id'] : (int) ($_REQUEST['client_id'] ?? 0);
    if (!$task) {
        if ($u['role'] !== 'super_admin') {
            $cid = tasks_resolve_client_id($u);
            if ($cid <= 0) {
                render('error', ['title' => 'No client', 'message' => 'No client context for creating a task.']);
                return;
            }
        }
        if (!tasks_can_create($u, $cid)) {
            render('error', ['title' => 'Forbidden', 'message' => 'You can not create tasks in this scope.']);
            return;
        }
    } else {
        if (!tasks_can_edit($u, $task)) {
            render('error', ['title' => 'Forbidden', 'message' => 'You can not edit this task.']);
            return;
        }
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

        // VTs can edit only their own task; they cannot change the assignee.
        if ($u['role'] === 'vt_hired') {
            $assignee = isset($task['assignee_user_id']) ? (int) $task['assignee_user_id'] : null;
            $cid      = (int) ($task['client_id'] ?? 0);
        } else {
            // super_admin / client / csm: when not super_admin the posted
            // client_id is ignored — they're scoped to whatever was loaded.
            if ($u['role'] === 'super_admin') {
                $cid = (int) ($_POST['client_id'] ?? $cid ?? 0);
            }
        }
        $cidParam = $cid > 0 ? $cid : null;

        if ($title === '') {
            flash('error', 'Title is required.');
            redirect(portal_url('tasks.edit', $id ? ['id' => $id] : ($cid ? ['client_id' => $cid] : [])));
        }

        if ($task) {
            $oldAssignee = (int) ($task['assignee_user_id'] ?? 0);
            $pdo->prepare(
                'UPDATE tasks SET title=:t, description=:d, assignee_user_id=:a, priority=:p,
                        due_date=:dd, client_id=:c, updated_at=CURRENT_TIMESTAMP
                 WHERE id=:id'
            )->execute([
                ':t'=>$title, ':d'=>$desc, ':a'=>$assignee, ':p'=>$priority,
                ':dd'=>$due, ':c'=>$cidParam, ':id'=>$id,
            ]);
            audit_log('task_update', 'task', $id);
            // Notify when the task is re-assigned to a new person.
            if ($assignee !== null && (int) $assignee !== $oldAssignee) {
                tasks_notify_assignee((int) $assignee, $id, $title, (int) ($cidParam ?? 0));
            } elseif ($assignee !== null && (int) $assignee === $oldAssignee && $assignee !== (int) $u['id']) {
                // Quiet "this task changed" ping to the current assignee
                // (skipped if they're the one editing).
                notify(
                    (int) $assignee, 'task',
                    'Task updated: ' . mb_substr($title, 0, 80),
                    'A task assigned to you was just updated. Check the latest details.',
                    'index.php?p=tasks.edit&id=' . $id,
                );
            }
            flash('success', 'Task updated.');
            redirect(portal_url('tasks.edit', ['id' => $id]));
        }

        $pdo->prepare(
            "INSERT INTO tasks (client_id, assignee_user_id, created_by, title, description, priority, due_date, status)
             VALUES (:c, :a, :cb, :t, :d, :p, :dd, 'active')"
        )->execute([
            ':c'=>$cidParam, ':a'=>$assignee, ':cb'=>$u['id'],
            ':t'=>$title, ':d'=>$desc, ':p'=>$priority, ':dd'=>$due,
        ]);
        $newId = (int) $pdo->lastInsertId();
        audit_log('task_create', 'task', $newId);
        if ($assignee) {
            tasks_notify_assignee($assignee, $newId, $title, (int) ($cidParam ?? 0));
        }
        flash('success', 'Task created.');
        redirect(portal_url('tasks.edit', ['id' => $newId]));
    }

    // Picker scope. For super_admin, also expose the client list so they
    // can attach (or detach) a task to a specific company.
    $assignees = tasks_assignable_users($u, $cid);
    $clients   = ($u['role'] === 'super_admin')
        ? db()->query("SELECT id, company_name FROM clients WHERE contract_status = 'active' ORDER BY company_name")->fetchAll()
        : [];

    // Existing attachments (only for saved tasks).
    $attachments = [];
    if ($task) {
        $stmt = db()->prepare(
            'SELECT a.*, u.first_name, u.last_name, u.email
             FROM task_attachments a
             LEFT JOIN users u ON u.id = a.uploaded_by
             WHERE a.task_id = :t ORDER BY a.created_at DESC'
        );
        $stmt->execute([':t' => (int) $task['id']]);
        $attachments = $stmt->fetchAll();
    }

    render('tasks-edit', [
        'user'        => $u,
        'task'        => $task,
        'client_id'   => $cid,
        'assignees'   => $assignees,
        'clients'     => $clients,
        'attachments' => $attachments,
    ]);
}

function handle_tasks_toggle(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('tasks')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) { redirect(portal_url('tasks')); }
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $task = $stmt->fetch();
    if (!$task) { flash('error', 'Task not found.'); redirect(portal_url('tasks')); }
    if (!tasks_can_toggle($u, $task)) { flash('error', 'Not allowed.'); redirect(portal_url('tasks')); }

    $newStatus = $task['status'] === 'active' ? 'completed' : 'active';
    $completed = $newStatus === 'completed' ? date('Y-m-d H:i:s') : null;
    $pdo->prepare('UPDATE tasks SET status=:s, completed_at=:c, updated_at=CURRENT_TIMESTAMP WHERE id=:id')
        ->execute([':s' => $newStatus, ':c' => $completed, ':id' => $id]);
    audit_log('task_' . ($newStatus === 'completed' ? 'complete' : 'reopen'), 'task', $id);

    // Notify the OTHER party (creator if assignee toggled, assignee if creator
    // toggled) so both sides see the status change in their inbox.
    $taskTitle = (string) ($task['title'] ?? 'Task');
    $creatorId = (int) ($task['created_by'] ?? 0);
    $asgneeId  = (int) ($task['assignee_user_id'] ?? 0);
    $actorId   = (int) $u['id'];
    $notifyIds = array_unique(array_filter([$creatorId, $asgneeId], static fn($v) => $v > 0 && $v !== $actorId));
    $actionLbl = $newStatus === 'completed' ? 'completed' : 're-opened';
    foreach ($notifyIds as $nid) {
        notify(
            (int) $nid, 'task',
            'Task ' . $actionLbl . ': ' . mb_substr($taskTitle, 0, 80),
            'The task "' . $taskTitle . '" was ' . $actionLbl . '.',
            'index.php?p=tasks.edit&id=' . $id,
        );
    }

    flash('success', $newStatus === 'completed' ? 'Marked complete.' : 'Re-opened.');
    redirect(portal_url('tasks'));
}

function handle_tasks_delete(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('tasks')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) { redirect(portal_url('tasks')); }
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $task = $stmt->fetch();
    if (!$task) { redirect(portal_url('tasks')); }
    if (!tasks_can_delete($u, $task)) { flash('error', 'Not allowed.'); redirect(portal_url('tasks')); }
    // Drop the attachment directory too.
    $dir = tasks_attachments_dir((int) $id);
    if (is_dir($dir)) {
        foreach (glob($dir . '/*') as $f) { @unlink($f); }
        @rmdir($dir);
    }
    $pdo->prepare('DELETE FROM tasks WHERE id = :id')->execute([':id' => $id]);
    audit_log('task_delete', 'task', $id);

    // Tell the assignee + creator (besides whoever clicked delete) the
    // task is gone. Link goes to the tasks list since the task is dead.
    $taskTitle = (string) ($task['title'] ?? 'Task');
    $actorId   = (int) $u['id'];
    $notifyIds = array_unique(array_filter(
        [(int) ($task['created_by'] ?? 0), (int) ($task['assignee_user_id'] ?? 0)],
        static fn($v) => $v > 0 && $v !== $actorId
    ));
    foreach ($notifyIds as $nid) {
        notify(
            (int) $nid, 'task',
            'Task deleted: ' . mb_substr($taskTitle, 0, 80),
            'The task "' . $taskTitle . '" was deleted from your workspace.',
            'index.php?p=tasks',
        );
    }

    flash('success', 'Task deleted.');
    redirect(portal_url('tasks'));
}

/* ── Task file attachments ─────────────────────────────────────────── */

function tasks_attachments_dir(int $taskId): string
{
    return __DIR__ . '/../data/task-attachments/' . $taskId;
}

/** Per-extension allowlist + MIME map for uploaded task files. */
function tasks_attachment_allowed(): array
{
    return [
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'csv'  => 'text/csv',
        'txt'  => 'text/plain',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'zip'  => 'application/zip',
    ];
}

function handle_tasks_attach(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('tasks')); }
    csrf_verify();
    $id = (int) ($_POST['task_id'] ?? 0);
    if ($id <= 0) { redirect(portal_url('tasks')); }
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $task = $stmt->fetch();
    if (!$task) { redirect(portal_url('tasks')); }
    if (!tasks_can_edit($u, $task)) { flash('error', 'Not allowed.'); redirect(portal_url('tasks.edit', ['id' => $id])); }

    if (empty($_FILES['file']) || ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        flash('error', 'No file uploaded (or upload failed).');
        redirect(portal_url('tasks.edit', ['id' => $id]));
    }
    $file = $_FILES['file'];
    if ((int) $file['size'] > 20 * 1024 * 1024) {
        flash('error', 'File too large (max 20 MB).');
        redirect(portal_url('tasks.edit', ['id' => $id]));
    }
    $orig = (string) $file['name'];
    $ext  = strtolower((string) pathinfo($orig, PATHINFO_EXTENSION));
    $allow = tasks_attachment_allowed();
    if (!isset($allow[$ext])) {
        flash('error', 'File type not allowed (' . e($ext) . ').');
        redirect(portal_url('tasks.edit', ['id' => $id]));
    }
    $mime = $allow[$ext];

    $pdo->prepare(
        'INSERT INTO task_attachments (task_id, uploaded_by, original_name, ext, mime, size_bytes)
         VALUES (:t, :u, :n, :e, :m, :s)'
    )->execute([
        ':t' => $id, ':u' => (int) $u['id'],
        ':n' => $orig, ':e' => $ext, ':m' => $mime, ':s' => (int) $file['size'],
    ]);
    $attId = (int) $pdo->lastInsertId();

    $dir  = tasks_attachments_dir($id);
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
    $dest = $dir . '/' . $attId . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        // Roll back the metadata row if the disk write failed.
        $pdo->prepare('DELETE FROM task_attachments WHERE id = :a')->execute([':a' => $attId]);
        flash('error', 'Could not save the uploaded file.');
        redirect(portal_url('tasks.edit', ['id' => $id]));
    }
    audit_log('task_attach', 'task', $id, $orig);
    flash('success', 'Attached: ' . $orig);
    redirect(portal_url('tasks.edit', ['id' => $id]));
}

function handle_tasks_attachment_serve(): void
{
    $u  = require_login();
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(404); echo 'Not found'; return; }
    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT a.*, t.client_id, t.assignee_user_id
         FROM task_attachments a
         JOIN tasks t ON t.id = a.task_id
         WHERE a.id = :a'
    );
    $stmt->execute([':a' => $id]);
    $row = $stmt->fetch();
    if (!$row) { http_response_code(404); echo 'Not found'; return; }
    if (!tasks_can_view($u, $row)) { http_response_code(403); echo 'Forbidden'; return; }

    $base = realpath(__DIR__ . '/../data/task-attachments');
    if ($base === false) { http_response_code(404); return; }
    $file = $base . DIRECTORY_SEPARATOR . (int) $row['task_id'] . DIRECTORY_SEPARATOR . $id . '.' . $row['ext'];
    $real = realpath($file);
    if ($real === false || !str_starts_with($real, $base)) { http_response_code(404); return; }

    header('Content-Type: ' . ($row['mime'] ?: 'application/octet-stream'));
    header('Content-Length: ' . filesize($file));
    header('Content-Disposition: attachment; filename="' . str_replace('"', '', $row['original_name']) . '"');
    header('X-Content-Type-Options: nosniff');
    readfile($file);
}

function handle_tasks_attachment_delete(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('tasks')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) { redirect(portal_url('tasks')); }
    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT a.*, t.client_id, t.assignee_user_id
         FROM task_attachments a JOIN tasks t ON t.id = a.task_id
         WHERE a.id = :a'
    );
    $stmt->execute([':a' => $id]);
    $row = $stmt->fetch();
    if (!$row) { redirect(portal_url('tasks')); }
    // VTs may only delete attachments they uploaded themselves.
    $isMine = ((int) $row['uploaded_by']) === (int) $u['id'];
    $canDel = ($u['role'] === 'super_admin')
           || (tasks_can_edit($u, $row) && ($u['role'] !== 'vt_hired' || $isMine));
    if (!$canDel) { flash('error', 'Not allowed.'); redirect(portal_url('tasks.edit', ['id' => (int) $row['task_id']])); }

    $file = tasks_attachments_dir((int) $row['task_id']) . '/' . $id . '.' . $row['ext'];
    if (is_file($file)) { @unlink($file); }
    $pdo->prepare('DELETE FROM task_attachments WHERE id = :a')->execute([':a' => $id]);
    audit_log('task_detach', 'task', (int) $row['task_id'], (string) $row['original_name']);
    flash('success', 'Attachment removed.');
    redirect(portal_url('tasks.edit', ['id' => (int) $row['task_id']]));
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
    // Refresh notify_by_email from the DB so the toggle reflects the latest
    // value (current_user() caches the row for the request).
    $stmt = db()->prepare('SELECT notify_by_email FROM users WHERE id = :u');
    $stmt->execute([':u' => $u['id']]);
    $u['notify_by_email'] = (int) ($stmt->fetchColumn() ?: 0);

    $stmt = db()->prepare('SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 200');
    $stmt->execute([':uid' => $u['id']]);
    render('notifications-list', ['user' => $u, 'notifications' => $stmt->fetchAll()]);
}

/** Detect AJAX-style POST: the JS-driven notifications page posts with
 *  `_ajax=1` so handlers can JSON-respond instead of redirecting. */
function notifications_is_ajax(): bool
{
    return !empty($_POST['_ajax'])
        || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
}

function notifications_json_reply(int $uid, array $extra = []): void
{
    $unread = (int) db()->query("SELECT COUNT(*) FROM notifications WHERE user_id = {$uid} AND read_at IS NULL")->fetchColumn();
    header('Content-Type: application/json');
    header('Cache-Control: no-store');
    echo json_encode(array_merge(['ok' => true, 'unread' => $unread], $extra), JSON_UNESCAPED_SLASHES);
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
    if (notifications_is_ajax()) { notifications_json_reply((int) $u['id']); return; }
    redirect(portal_url('notifications'));
}

function handle_notifications_delete(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('notifications')); }
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        db()->prepare('DELETE FROM notifications WHERE id = :id AND user_id = :uid')
            ->execute([':id' => $id, ':uid' => $u['id']]);
    }
    if (notifications_is_ajax()) { notifications_json_reply((int) $u['id']); return; }
    redirect(portal_url('notifications'));
}

function handle_notifications_delete_all(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('notifications')); }
    csrf_verify();
    db()->prepare('DELETE FROM notifications WHERE user_id = :uid')->execute([':uid' => $u['id']]);
    if (notifications_is_ajax()) { notifications_json_reply((int) $u['id']); return; }
    flash('success', 'All notifications cleared.');
    redirect(portal_url('notifications'));
}

function handle_notifications_toggle_email(): void
{
    $u = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('notifications')); }
    csrf_verify();
    $on = !empty($_POST['on']) ? 1 : 0;
    db()->prepare('UPDATE users SET notify_by_email = :on, updated_at = CURRENT_TIMESTAMP WHERE id = :uid')
        ->execute([':on' => $on, ':uid' => $u['id']]);
    if (notifications_is_ajax()) {
        notifications_json_reply((int) $u['id'], ['notify_by_email' => $on]);
        return;
    }
    flash('success', $on ? 'Email notifications turned ON.' : 'Email notifications turned OFF.');
    redirect(portal_url('notifications'));
}

/* ═════════════════════════════════════════════════════════════════════════
 * RESOURCES — Downloads/playbook tab, mirrors the staging
 * [client_resources] shortcode.
 * ═════════════════════════════════════════════════════════════════════════ */

function client_resources_definitions(): array
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
    $u    = require_login();
    $role = $u['role'];

    // Client downloads + playbook: shown to clients, CSMs, and super admins.
    // VTs do NOT see the client material — their Resources page is the
    // Knowledge Center launcher only.
    $showClient = in_array($role, ['client', 'csm', 'super_admin'], true);
    // Knowledge Center launcher: shown to VTs, CSMs, and super admins — never
    // to clients (the KC is internal team training).
    $showKc     = in_array($role, ['vt_hired', 'vt_onpool', 'csm', 'super_admin'], true);

    $client = null;
    if ($showClient) {
        $client = [
            'resources' => client_resources_definitions(),
            'playbook'  => [
                'title' => 'Virtual Teammate Client Playbook',
                'desc'  => 'Start here to align expectations, simplify communication, and establish a smooth workflow — so your Virtual Teammate can deliver faster.',
                'url'   => 'https://virtualteammate.com/client-playbook-3/',
            ],
        ];
    }

    $kc = null;
    if ($showKc) {
        // Hub presentation metadata per topic — a category tag and a filter
        // group (used by the "Operations / Admin / Support" chips), mirroring
        // the staging [vtm_resource_hub_consolidated] hub.
        $hubMeta = [
            'knowledge-center' => ['tag' => 'Guides & Articles', 'group' => 'ops admin',     'blurb' => 'Onboarding videos, how-tos, and self-paced lessons for the team.'],
            'human-resources'  => ['tag' => 'Policies & SOPs',    'group' => 'admin ops',     'blurb' => 'Attendance, Time Off, Health Benefits, and internal policies in one hub.'],
            'finance'          => ['tag' => 'Billing & Payments',  'group' => 'admin',         'blurb' => 'Billing references, payment info, and finance documentation.'],
            'it-concerns'      => ['tag' => 'Tech Support',        'group' => 'support',       'blurb' => 'Workstation setup, internet/power requirements, and troubleshooting.'],
            'marketing'        => ['tag' => 'Brand & Content',     'group' => 'ops admin',     'blurb' => 'Brand, audience, and social-content training for Virtual Teammate.'],
            'sales'            => ['tag' => 'Client Readiness',     'group' => 'ops support',   'blurb' => 'Communicate professionally, interpret client needs, and handle issues.'],
        ];
        $shortcuts = [];
        foreach (kc_topics() as $t) {
            $m = $hubMeta[$t['slug']] ?? ['tag' => 'Knowledge', 'group' => 'ops', 'blurb' => $t['intro'] ?? ''];
            $shortcuts[] = [
                'url'   => portal_url('knowledge-center', ['topic' => $t['slug']]),
                'label' => $t['label'],
                'icon'  => $t['icon'],
                'desc'  => $m['blurb'],
                'tag'   => $m['tag'],
                'group' => $m['group'],
                'ext'   => false,
            ];
        }
        // Wider community resource (external), as on the staging hub.
        $shortcuts[] = [
            'url'   => 'https://virtualteammate.com/group-teammate-community-about/',
            'label' => 'Group Teammate Community',
            'icon'  => 'fa-people-group',
            'desc'  => 'Connect with the wider Virtual Teammate community — events, discussions, and shared resources.',
            'tag'   => 'Community',
            'group' => 'ops admin support',
            'ext'   => true,
        ];
        $kc = [
            'shortcuts' => $shortcuts,
            'home'      => portal_url('knowledge-center'),
        ];
    }

    render('resources', ['client' => $client, 'kc' => $kc]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * KNOWLEDGE CENTER — internal learning hub for VTs and CSMs, recreated from
 * the staging WordPress Knowledge Center + its topic pages (Help Center, HR,
 * Finance, IT, Marketing, Sales). Content is data-driven via kc_topics();
 * the view (views/knowledge-center.php) renders a block model in our theme.
 * Gated to VTs (hired/on-pool), CSMs, and super admins — never clients.
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_knowledge_center(): void
{
    require_role('vt_hired', 'vt_onpool', 'csm', 'super_admin');
    $topics = kc_topics();
    $slug   = (string) ($_GET['topic'] ?? '');
    $active = $topics[$slug] ?? reset($topics);
    render('knowledge-center', [
        'topics' => $topics,
        'topic'  => $active,
        'slug'   => $active['slug'],
    ]);
}

/**
 * Full Knowledge Center content, keyed by topic slug. Each topic renders a
 * pill in the topic nav; its body is a set of tabs, each holding content
 * blocks. Block types handled by the view: lessons, summary, accordion,
 * cards (filterable), swatches, qa, captions, platforms, prose, heading,
 * note, image, ask.
 */
function kc_topics(): array
{
    return [
        /* ───────────────────────── Knowledge Center (landing) ───────────── */
        'knowledge-center' => [
            'slug'  => 'knowledge-center',
            'label' => 'Knowledge Center',
            'icon'  => 'fa-graduation-cap',
            'h1'    => ['KNOWLEDGE', 'CENTER'],
            'eyebrow' => 'Reliable answers. Quick solutions. All in one space.',
            'intro' => 'Onboarding videos and self-paced lessons to get you confident and client-ready from day one.',
            'tabs'  => [
                ['label' => 'Training 101', 'blocks' => [
                    ['type' => 'lessons', 'items' => [
                        ['title' => 'Meet the Team', 'badge' => 'Video', 'desc' => 'Get to know the amazing people behind Virtual Teammate. Our dedicated team works together to help businesses grow while providing meaningful opportunities for talented professionals worldwide.', 'media' => ['kind' => 'novideo', 'src' => '']],
                        ['title' => 'Message from our CEO', 'badge' => 'Video', 'desc' => 'A special message from our CEO sharing the vision, mission, and commitment of Virtual Teammate to building strong partnerships and creating a thriving remote work environment.', 'media' => ['kind' => 'novideo', 'src' => '']],
                        ['title' => 'Independent Contractor Agreement', 'badge' => 'Video', 'desc' => 'Learn more about the guidelines, expectations, and agreements that define your role as a valued Virtual Teammate. This section walks you through the essential terms to help you succeed in your journey with us.', 'media' => ['kind' => 'novideo', 'src' => '']],
                        ['title' => 'Application Roadmap', 'badge' => 'Video', 'desc' => 'Step-by-step guide on what to expect during your application process — from submitting your requirements, through the review and interview stages, to onboarding where you officially become a Virtual Teammate.', 'media' => ['kind' => 'novideo', 'src' => '']],
                        ['title' => 'Eisenhower Matrix', 'badge' => 'Self-paced training', 'desc' => 'A self-paced primer on prioritizing your work by urgency and importance so you spend time on what truly moves the needle.', 'media' => ['kind' => 'soon', 'src' => '']],
                        ['title' => 'Executive Assistant SOP', 'badge' => 'Lesson · PDF', 'desc' => 'A quick guide to the essential SOPs every Executive Assistant needs to follow for efficiency and consistency.', 'media' => ['kind' => 'pdf', 'src' => 'assets/kc/pdf/executive-assistant-sop.pdf']],
                        ['title' => 'How to ace your client interview', 'badge' => 'Lesson', 'desc' => 'Key tips to impress clients and build lasting professional relationships.', 'media' => ['kind' => 'novideo', 'src' => '']],
                        ['title' => 'Reasons why VT is unsuccessful in client interviews', 'badge' => 'Lesson', 'desc' => 'The key reasons Virtual Teammates fail to perform well in client interviews — and how to avoid them.', 'media' => ['kind' => 'novideo', 'src' => '']],
                    ]],
                ]],
                ['label' => 'Training 102', 'blocks' => [['type' => 'note', 'text' => 'Training 102 lessons are coming soon.']]],
                ['label' => 'Training 103', 'blocks' => [['type' => 'note', 'text' => 'Training 103 lessons are coming soon.']]],
                ['label' => 'Training 104', 'blocks' => [['type' => 'note', 'text' => 'Training 104 lessons are coming soon.']]],
            ],
        ],

        /* ───────────────────────── Human Resources ──────────────────────── */
        'human-resources' => [
            'slug'  => 'human-resources',
            'label' => 'Human Resources',
            'icon'  => 'fa-user-shield',
            'h1'    => ['HUMAN', 'RESOURCES'],
            'eyebrow' => 'People & policies',
            'intro' => 'Your reference hub for Attendance, Time Off, and Health Benefits. Expand any topic to read the details.',
            'search' => true,
            'downloads' => [
                ['label' => 'Attendance SOP', 'url' => 'assets/kc/pdf/attendance-sop.pdf'],
                ['label' => 'Time Off SOP', 'url' => 'assets/kc/pdf/time-off-sop.pdf'],
                ['label' => 'Health Benefits SOP', 'url' => 'assets/kc/pdf/health-benefits-sop.pdf'],
            ],
            'tabs'  => [
                ['label' => 'Attendance', 'blocks' => [
                    ['type' => 'summary', 'items' => [
                        ['title' => 'Working Hours & Coverage', 'bullets' => ['Company-wide hours are 10 PM–8 AM Manila Time / 7 AM–5 PM PST (includes a 1-hour lunch).', 'Two coverage shifts may apply: 10 PM–7 AM and 11 PM–8 AM (MNL).']],
                        ['title' => 'Tracking & Accountability', 'bullets' => ['Attendance is tracked daily via Work Day Tracker.', 'Excessive use of a manual tracker due to WDT issues may lead to a written warning.']],
                    ]],
                    ['type' => 'accordion', 'items' => [
                        ['title' => 'Reporting absences or lateness', 'sub' => 'Notify early + provide documentation when required', 'bullets' => ['Inform your immediate supervisor/HR at least 2 hours before your workday start if you will be late or absent.', 'Notification can be sent via email or SMS (to your department head or HR).', 'If it\'s a sudden illness/emergency, notify as soon as possible.', 'If sick leave exceeds two days, a medical certificate is required upon return. Missing documentation may trigger non-compliance actions.']],
                        ['title' => 'Tardiness rules', 'sub' => 'Definitions + escalation steps', 'bullets' => ['Tardiness is arriving more than 30 minutes after your scheduled start time without prior notice.', 'Time offsetting is not allowed for tardiness (even if approved) unless the reason is verified as an emergency.', 'Habitual tardiness: 3 instances in a month → verbal warning.', 'Frequent tardiness: 3 instances/month for 3 consecutive months → may lead to suspension or contract termination at Dept Head and Client discretion.']],
                        ['title' => 'Undertime rules', 'sub' => 'For special or emergency situations', 'bullets' => ['Undertime approval depends on your Department Head and/or HR, based on the request.', 'Use undertime strictly for special or emergency situations. Excessive patterns may trigger review and a verbal warning.', 'Request approval at least 2 hours in advance when possible, and include the reason + intended departure time.', 'No time offsetting for undertime (even if approved) unless the reason is verified as an emergency.']],
                        ['title' => 'Leave management & consequences', 'sub' => 'Where to file + what happens for non-compliance', 'bullets' => ['All leaves (sick/casual/annual) must be requested via DEEL and approved by the direct manager.', 'Unapproved or excessive absenteeism may be considered misconduct.', 'Non-compliance may progress: verbal warning → written warning → suspension/termination (Client discretion).', 'HR maintains attendance records; exceptions (emergencies, severe weather, etc.) require approval by HR/Dept Heads.', 'This SOP is reviewed annually and updated as needed.']],
                    ]],
                    ['type' => 'note', 'text' => 'Quick reminder: Attendance is expected during business hours unless leave is approved.'],
                ]],
                ['label' => 'Time Off', 'blocks' => [
                    ['type' => 'summary', 'items' => [
                        ['title' => 'Independent Contractor Context', 'bullets' => ['Time Off (TO) is time away from contracted services, coordinated with the client.', 'For VT Placed roles, there is no paid time off; TO decisions are determined by the client.']],
                        ['title' => 'Approval Timeline', 'bullets' => ['TO requests are reviewed and approved by the Client and/or AM/CSM within 1 business day (based on deadlines, capacity, and operational requirements).']],
                    ]],
                    ['type' => 'accordion', 'items' => [
                        ['title' => 'How to request Time Off', 'sub' => 'Email notification + required details', 'bullets' => ['Send an email notification to your CSM, including any proof of client approval.', 'Include: exact TO dates and any notes/context (if applicable).', 'Approval depends on: client agreement, project deadlines, team capacity, and operational requirements.', 'You\'ll receive confirmation via email or platform message. If denied, alternative suggestions may be provided.']],
                        ['title' => 'Before TO: handover checklist', 'sub' => 'Keep projects moving while you\'re away', 'bullets' => ['Update project statuses and key deliverables.', 'Notify relevant team members about your absence.', 'Ensure essential files and systems access are available for continuity.']],
                        ['title' => 'During TO & returning', 'sub' => 'Out-of-office + re-entry', 'bullets' => ['During approved TO, contractors are not expected to provide services.', 'Redirect urgent matters to a designated point of contact.', 'Activate out-of-office messages across your communication tools.', 'Upon return: review missed messages/updates, prioritize follow-ups, and notify your manager/client of your availability.']],
                        ['title' => 'Exceptions & review cycle', 'sub' => 'Deviations must be pre-approved', 'bullets' => ['Any deviations from this SOP must be pre-approved by the client or relevant manager/department head.', 'This SOP is reviewed annually and revised as needed.', 'Operational note: Client facing — CSM (no work, no pay).']],
                    ]],
                    ['type' => 'note', 'text' => 'Tip: Get client approval first, then send the request to your CSM with the confirmed dates.'],
                ]],
                ['label' => 'Health Benefits', 'blocks' => [
                    ['type' => 'summary', 'items' => [
                        ['title' => 'Program Overview', 'bullets' => ['Provider: Maxicare • Plan: PRIMA Consult • Cost: $16.85 / PHP 999 per person.', 'Coverage: outpatient consultations + basic diagnostics.']],
                        ['title' => 'Eligibility', 'bullets' => ['Available to teammates who have completed 90 days of active engagement with a client (enrollment subject to company approval and submission to Maxicare).']],
                    ]],
                    ['type' => 'accordion', 'items' => [
                        ['title' => 'What\'s covered', 'sub' => 'Unlimited consults + Annual Physical Exam (Basic 5)', 'bullets' => ['Unlimited doctor consultations at Maxicare-accredited PRIMA clinics.', 'Annual Physical Exam (Basic 5) includes: Physical exam, CBC, routine urinalysis, routine fecalysis, and Chest X-ray (PA view).']],
                        ['title' => 'Eligible specialists (PRIMA clinics)', 'sub' => 'Common specialties you can consult', 'bullets' => ['General Practitioner (GP), Internal Medicine (IM), Family Medicine (FM), Pediatrician, ENT, OB-GYN.', 'Dermatologist, Ophthalmologist, Cardiologist, Endocrinologist, Pulmonologist.', 'Gastroenterologist, Rheumatologist, Nephrologist, Infectious Disease Specialist.', 'General Surgeon, Urologist, Orthopedic Specialist, Rehabilitation Medicine Specialist.']],
                        ['title' => 'Key features (how it works)', 'sub' => 'Fast access and less paperwork', 'bullets' => ['Pre-existing conditions are accepted and covered.', 'No paperwork required during clinic visits.', 'No preliminary checkups/approvals needed.', 'Cashless and hassle-free consultations at PRIMA clinics.']],
                        ['title' => 'Enrollment & activation', 'sub' => 'What HR does + when coverage starts', 'bullets' => ['HR collects enrollment details, submits data to Maxicare, and coordinates updates/concerns.', 'Maxicare issues confirmation and activates PRIMA Consult access.', 'Coverage becomes active upon confirmation by Maxicare and remains valid for the coverage period in the enrollment agreement.']],
                        ['title' => 'Exclusions & limitations', 'sub' => 'Know what\'s not included', 'bullets' => ['Coverage is limited to outpatient consultations and Basic 5 APE only.', 'Hospitalization, emergency care, and advanced diagnostics are not included unless separately covered.', 'Services must be availed only at PRIMA clinics.']],
                    ]],
                    ['type' => 'note', 'text' => 'Responsibility: Teammates should use benefits responsibly and follow the program procedures.'],
                ]],
                ['label' => 'Helpful Notes', 'blocks' => [
                    ['type' => 'summary', 'items' => [
                        ['title' => 'When to contact HR/CSM', 'bullets' => ['Attendance issues (WDT, absences, documentation).', 'Time Off coordination and client approvals.', 'Benefits enrollment, activation, or clinic access.']],
                        ['title' => 'Best practice', 'bullets' => ['Notify early (2+ hours before shift when possible).', 'Keep proof of approvals (especially for TO).', 'Do a clean handover before leaving.']],
                    ]],
                ]],
            ],
        ],

        /* ───────────────────────── Finance ──────────────────────────────── */
        'finance' => [
            'slug'  => 'finance',
            'label' => 'Finance',
            'icon'  => 'fa-coins',
            'h1'    => ['FINANCE'],
            'eyebrow' => 'Finance Knowledge Base',
            'intro' => 'For Virtual Teammates — browse by category, then dive into subtopics.',
            'search' => true,
            'tabs'  => [
                ['label' => '', 'blocks' => [
                    ['type' => 'cards', 'filter' => true, 'items' => [
                        ['cat' => 'Foundations', 'title' => 'Finance Overview (Virtual Teammate Context)', 'summary' => 'How money flows and what "healthy margin" means inside Virtual Teammate.', 'bullets' => ['How money flows in Virtual Teammate', 'Client → Virtual Teammate → VT payment structure', 'Difference between company revenue and VT pay', 'Markup, margin, and profitability basics', 'Internal finance vs client finance responsibilities']],
                        ['cat' => 'Billing & AR', 'title' => 'Client Billing & Revenue Handling', 'summary' => 'Invoices, cycles, adjustments, and payment confirmation — done cleanly and consistently.', 'bullets' => ['Client billing models (monthly retainer, hourly, dedicated VT)', 'Invoice creation and components', 'Billing cycles and cut-off dates', 'Add-on charges and adjustments', 'Credits, refunds, and proration', 'Late payment handling and follow-ups', 'Payment confirmation and receipt tracking']],
                        ['cat' => 'Billing & AR', 'title' => 'Accounts Receivable (AR)', 'summary' => 'Track unpaid invoices and follow up with clear escalation rules.', 'bullets' => ['Tracking unpaid invoices', 'Aging reports (current, 30/60/90 days)', 'Client follow-up procedures', 'Escalation process for overdue accounts', 'Recording partial payments', 'Resolving billing disputes']],
                        ['cat' => 'AP & Expenses', 'title' => 'Accounts Payable (AP)', 'summary' => 'Pay vendors on time with proper approvals and clean documentation.', 'bullets' => ['Vendor payments', 'Software and subscription expenses', 'Recruitment-related expenses', 'Office/admin expenses', 'Payment scheduling and approvals', 'Expense documentation and receipts']],
                        ['cat' => 'Payroll', 'title' => 'Payroll & VT Compensation', 'summary' => 'Timesheets → cut-offs → payouts. Keep it accurate, predictable, and documented.', 'bullets' => ['VT payment structure (hourly vs fixed)', 'Timesheet tracking and validation', 'Overtime and overage hours', 'Payroll cut-offs and pay cycles', 'Payslip preparation', 'Payroll discrepancy handling', 'Contractor vs employee awareness (no legal advice)']],
                        ['cat' => 'Bookkeeping', 'title' => 'Bookkeeping Basics (Company-Level)', 'summary' => 'Clean categorization and matching = reliable reporting and fewer surprises.', 'bullets' => ['Chart of accounts (company-specific)', 'Revenue categorization', 'Payroll cost categorization', 'Operating expenses categorization', 'Bank feed transactions', 'Matching deposits and payments', 'Month-end closing support']],
                        ['cat' => 'Bookkeeping', 'title' => 'Bookkeeping Support for Clients', 'summary' => 'Support the client\'s finance hygiene without overstepping boundaries.', 'bullets' => ['Recording client income', 'Categorizing expenses', 'Expense reimbursement tracking', 'Vendor and supplier records', 'Basic transaction cleanup', 'Supporting accountant/bookkeeper (not replacing)']],
                        ['cat' => 'Reporting', 'title' => 'Financial Reporting Awareness', 'summary' => 'Know what the numbers mean so you can support accurately and communicate clearly.', 'bullets' => ['Profit & Loss statement understanding', 'Revenue vs cost vs profit', 'Gross margin per client/VT', 'Monthly financial summaries', 'Budget vs actual tracking', 'Internal finance dashboards (if applicable)']],
                        ['cat' => 'Metrics', 'title' => 'Financial Metrics', 'summary' => 'Key KPIs that help you spot risk early and understand business impact.', 'bullets' => ['Revenue per client', 'Cost per VT', 'Gross margin per VT', 'Client lifetime value (LTV)', 'Client churn impact on revenue', 'Utilization rate', 'Break-even awareness']],
                        ['cat' => 'Tools', 'title' => 'Payment Platforms & Tools', 'summary' => 'Where money moves — and what to verify before marking anything "paid."', 'bullets' => ['Stripe (subscriptions, invoices, payouts)', 'PayPal (client payments, VT payouts)', 'Wise (international transfers)', 'Payoneer', 'Bank transfers and confirmations', 'Payment reconciliation basics']],
                        ['cat' => 'Tools', 'title' => 'Accounting Software Exposure', 'summary' => 'Navigate the basics safely — especially permissions and role limitations.', 'bullets' => ['QuickBooks (basic navigation)', 'Xero (basic navigation)', 'Creating invoices', 'Recording payments', 'Running basic reports', 'User access limitations and roles']],
                        ['cat' => 'Bookkeeping', 'title' => 'Financial Data Management', 'summary' => 'Organize files so anyone can audit, trace, and understand quickly.', 'bullets' => ['File organization for finance records', 'Invoice and receipt storage', 'Naming conventions', 'Secure sharing of financial documents', 'Version control awareness']],
                        ['cat' => 'Compliance', 'title' => 'Compliance & Risk Awareness', 'summary' => 'Protect data, avoid overreach, and recognize red flags early.', 'bullets' => ['Confidentiality of financial data', 'Access control and permissions', 'Avoiding tax and legal advice', 'Contractor payment sensitivity', 'Client data separation', 'Fraud awareness and red flags']],
                        ['cat' => 'SOPs', 'title' => 'Internal Finance SOP Awareness', 'summary' => 'Know the cadence and the "who approves what" so nothing slips through.', 'bullets' => ['Daily finance tasks', 'Weekly finance tasks', 'Monthly finance tasks', 'Approval workflows', 'Escalation paths', 'Documentation standards']],
                        ['cat' => 'Scenarios', 'title' => 'Client Communication (Finance Context)', 'summary' => 'Say the right thing, with the right tone, and escalate when needed.', 'bullets' => ['Professional finance email etiquette', 'Payment reminders wording', 'Invoice explanations', 'Handling sensitive conversations', 'Escalating client concerns internally']],
                        ['cat' => 'Scenarios', 'title' => 'Common Finance Scenarios', 'summary' => 'What happens in real life — and the cleanest way to handle it.', 'bullets' => ['Late client payments', 'Client pauses or cancellations', 'VT replacement cost impact', 'Billing errors and corrections', 'Payroll mismatches', 'Currency differences and conversion issues']],
                        ['cat' => 'Scaling', 'title' => 'Scaling Finance Operations', 'summary' => 'Keep accuracy high as volume grows — without burning out the process.', 'bullets' => ['Handling more clients without errors', 'Automation awareness', 'Template usage', 'Batch processing', 'Reporting consistency']],
                        ['cat' => 'Guardrails', 'title' => 'What VTs Should NOT Do', 'summary' => 'Clear boundaries to protect clients, Virtual Teammate, and you.', 'bullets' => ['Give tax advice', 'Change billing without approval', 'Process refunds without authorization', 'Share financial access credentials', 'Access unrelated client financial data']],
                    ]],
                ]],
            ],
        ],

        /* ───────────────────────── IT Concerns ──────────────────────────── */
        'it-concerns' => [
            'slug'  => 'it-concerns',
            'label' => 'IT Concerns',
            'icon'  => 'fa-laptop-code',
            'h1'    => ['IT', 'CONCERNS'],
            'eyebrow' => 'IT Setup Requirements',
            'intro' => 'The minimum and recommended workstation setup for Virtual Assistants — computer specs, internet speed, backup internet, and backup power — so you can avoid downtime and meet client expectations from Day 1.',
            'search' => true,
            'tabs'  => [
                ['label' => '', 'blocks' => [
                    ['type' => 'summary', 'items' => [
                        ['title' => 'Computer (Minimum)', 'sub' => 'Windows 10/11 (64-bit) or macOS compatible', 'bullets' => ['CPU: Intel i5 / AMD Ryzen 3 (or newer)', 'RAM: 8 GB (12–16 GB recommended)', 'Storage: 128 GB SSD minimum']],
                        ['title' => 'Primary Internet', 'sub' => 'Wired DSL/Fiber strongly recommended', 'bullets' => ['Minimum: 25 Mbps download / 15 Mbps upload', 'Run a Speedtest before shift']],
                        ['title' => 'Backup Internet', 'sub' => 'Required to prevent downtime', 'bullets' => ['LTE backup: 10 Mbps (min)', 'LTE-A: 25 Mbps | 5G: 50 Mbps+']],
                        ['title' => 'Backup Power', 'sub' => 'UPS or generator required', 'bullets' => ['UPS for short outages (keeps PC/router online)', 'Generator for longer outages']],
                    ]],
                    ['type' => 'accordion', 'items' => [
                        ['title' => '1) Computer & Operating System (OS)', 'tags' => ['Workstation', 'Required'], 'sub' => 'Learning goal: Know what computer/OS is acceptable so you can pass onboarding checks and perform reliably.', 'bullets' => ['Windows: Windows 10/11 (64-bit). Windows 11 is commonly required; use a genuine license.', 'macOS: must be a supported modern version; Apple Silicon is commonly preferred for performance.', 'VA best practice: Use a dedicated work computer (avoid shared/family devices).'], 'note' => 'Trainer note: If your device does not meet minimum requirements, you must resolve this before client deployment to avoid productivity and compliance issues.'],
                        ['title' => '2) Processor (CPU) & RAM (Memory)', 'tags' => ['Performance', 'Required'], 'sub' => 'Why this matters: Video calls, EMR systems, multiple browser tabs, and VoIP tools will lag or crash on low specs.', 'bullets' => ['Minimum CPU: Intel i5 / AMD Ryzen 3 (or newer)', 'Recommended CPU: Intel i5/i7 / AMD Ryzen 5 (or better)', 'Minimum RAM: 8 GB · Recommended RAM: 12–16 GB', 'Check RAM (Windows): Task Manager → Performance → Memory', 'Check CPU: Settings → System → About (or Task Manager → Performance → CPU)']],
                        ['title' => '3) Storage (SSD) & Free Disk Space', 'tags' => ['Workstation', 'Required'], 'sub' => 'Minimum storage: 128 GB (SSD strongly preferred). More storage reduces crashes and improves speed.', 'bullets' => ['Recommended: 256 GB SSD if you frequently use large apps/files.', 'Rule of thumb: Keep at least 20% free space for smooth performance.', 'VA habit: Save work files to approved cloud storage (not random desktop folders).']],
                        ['title' => '4) Primary Internet Speed (Do Not Guess — Test It)', 'tags' => ['Connectivity', 'Required'], 'sub' => 'Minimum primary internet: 25 Mbps download / 15 Mbps upload (wired DSL/Fiber recommended).', 'bullets' => ['Why upload matters: VoIP calls, screen-sharing, and video meetings fail first when upload is weak.', 'Daily: Run a speed test before your shift and take a screenshot if results are low.', 'If your speed is below minimum, switch to backup internet immediately and notify the team.'], 'note' => 'Pro tip: If you have frequent call drops, move closer to the router or use an Ethernet cable.'],
                        ['title' => '5) Backup Internet (Non-Negotiable)', 'tags' => ['Continuity', 'Required'], 'sub' => 'Goal: If your primary internet fails, you must be able to continue working (especially during client hours).', 'bullets' => ['Minimum backup: 4G LTE at 10 Mbps.', 'Better: 4G LTE Advanced at 25 Mbps.', 'Best: 5G at 50 Mbps+.', 'VA habit: Keep your hotspot/pocket Wi-Fi charged and tested weekly.'], 'note' => 'Trainer standard: Practice switching to your backup once before you go live with clients.'],
                        ['title' => '6) Backup Power (UPS / Generator)', 'tags' => ['Continuity', 'Required'], 'sub' => 'Why clients care: Power interruptions cause missed calls, dropped sessions, and lost work.', 'bullets' => ['Minimum: UPS or generator (UPS is ideal for short outages; generator for longer outages).', 'What to plug into your UPS: computer + router/modem (so internet stays on during short power cuts).', 'During an outage, switch to backup power and message the appropriate channel/manager if client work is impacted.']],
                        ['title' => '7) Required Peripherals (Webcam + Noise-Cancelling Headset)', 'tags' => ['Audio/Video', 'Required'], 'sub' => 'Minimum requirements: 1080p webcam (or high-quality HD) and a USB noise-cancelling headset.', 'bullets' => ['Why this matters: Clear audio is a client trust issue (especially in healthcare/admin workflows).', 'VA habit: Test mic/camera weekly and before interviews/assessments.', 'Common mistakes: Using laptop mic in noisy rooms; Bluetooth devices with unstable pairing.']],
                        ['title' => '8) Home Workspace Standards (IT + Reliability)', 'tags' => ['Readiness', 'Required'], 'sub' => 'Your setup must support clear calls, stable connection, and professional video — without interruptions.', 'bullets' => ['Quiet environment (minimal background noise).', 'Stable desk/workstation (avoid working from bed/sofa).', 'Decent lighting and professional background for video calls.', 'Keep backup devices charged: hotspot/pocket Wi-Fi + power bank.']],
                        ['title' => '9) Pre-Shift Checklist & What To Do During an IT Issue', 'tags' => ['Best Practice', 'Must Know'], 'sub' => 'Use this routine to prevent "surprise" downtime.', 'bullets' => ['Before shift: Speed test, confirm headset/mic, check battery/UPS status, confirm backup internet is charged.', 'If internet drops: Switch to backup → re-test speed → notify your lead if client work is impacted.', 'If power drops: Move to UPS/generator → ensure router/modem stays on → message the team with ETA.', 'If device is slow: Restart → close heavy apps/tabs → check disk space → confirm RAM usage.', 'Always report: time, location, what failed, screenshots, what you tried, current status.'], 'note' => 'VA professionalism standard: Don\'t disappear during outages. Send a short, clear status update and next ETA.'],
                    ]],
                    ['type' => 'ask', 'email' => 'people@virtualteammate.com', 'title' => 'Questions about your IT setup?', 'desc' => 'Submit a setup question. Include your device specs, internet speed test results, and what you need help with.'],
                ]],
            ],
        ],

        /* ───────────────────────── Marketing ────────────────────────────── */
        'marketing' => [
            'slug'  => 'marketing',
            'label' => 'Marketing',
            'icon'  => 'fa-bullhorn',
            'h1'    => ['MARKETING'],
            'eyebrow' => 'Marketing training',
            'intro' => 'A multi-day primer on brand, audience, and social content for Virtual Teammate.',
            'tabs'  => [
                ['label' => 'Day 1', 'blocks' => [
                    ['type' => 'heading', 'text' => 'Lesson'],
                    ['type' => 'qa', 'items' => [
                        ['q' => 'What is marketing?', 'a' => 'Marketing is the process of promoting, selling, and distributing a product or service. It involves understanding the customer, building relationships, and delivering value.'],
                        ['q' => 'What makes effective brand communication?', 'a' => 'Clear messaging, consistency across platforms, strong visuals, and understanding your audience\'s needs.'],
                        ['q' => 'What is Virtual Teammate?', 'a' => 'A company that offers remote staffing solutions for businesses, helping them grow by providing virtual support teams.'],
                        ['q' => 'Who is our target audience?', 'a' => 'U.S.-based business owners (primarily in healthcare and service sectors) looking to outsource tasks and grow efficiently.'],
                    ]],
                    ['type' => 'heading', 'text' => 'Brand Colors'],
                    ['type' => 'swatches', 'groups' => [
                        ['label' => 'Primary', 'colors' => ['#5d5dbe', '#f7b945', '#323232', '#ebecf2']],
                        ['label' => 'Secondary (small elements & gradient pairs)', 'colors' => ['#3919ba', '#a2a2f4', '#d6d9fb', '#f9f9f9']],
                    ]],
                    ['type' => 'note', 'text' => 'Assignment: Create 3 Facebook posts (Hiring, Healthcare Promo, Business). Submit with complete captions and simple graphics.'],
                ]],
                ['label' => 'Day 2', 'blocks' => [
                    ['type' => 'heading', 'text' => 'Post Conceptualization'],
                    ['type' => 'prose', 'bullets' => ['What do you want to achieve? (e.g., drive traffic, increase engagement, promote a product)', 'Who are you trying to reach? (Consider their interests, needs, and pain points)', 'What would you like your audience to know?', 'What keywords will help people find your post?', 'What visual elements (images, videos, infographics) will best support your message?', 'Create a headline that grabs attention.']],
                    ['type' => 'heading', 'text' => 'Audience Types'],
                    ['type' => 'qa', 'items' => [
                        ['q' => 'The Skimmers (Quick Scrollers)', 'a' => 'What they like: headlines that pop; bold, clear graphics; short, punchy text (no fluff); quick "what\'s in it for me" content.'],
                        ['q' => 'The Conversationalists / Relationship Builders', 'a' => 'What they like: authentic posts (behind-the-scenes, team, culture); personal stories, vulnerable shares; real comments, questions, and replies.'],
                        ['q' => 'The Information-Seekers / Problem-Solvers', 'a' => 'What they like: educational content; stats, data, or how-tos; practical steps to a solution; proof of expertise.'],
                        ['q' => 'The Decision-Makers / Action Takers', 'a' => 'What they like: clear CTA (book now, free consult); testimonials, trust signals; results + next steps in plain language.'],
                        ['q' => 'The Aesthetics-First Audience', 'a' => 'What they like: clean, branded, pleasing visuals; harmonious color palettes and polished design; calm, confident vibes; trust built visually first.'],
                    ]],
                    ['type' => 'heading', 'text' => 'LinkedIn Caption Sample'],
                    ['type' => 'captions', 'items' => [
                        ['label' => '(Before)', 'text' => 'The shift to remote work isn\'t just a trend—it\'s becoming a fundamental business strategy. Studies have shown that 77% of remote workers report higher productivity, and 23% are willing to work longer hours when working remotely (source: Owl Labs). What does this mean for your business? By integrating remote virtual teammates into your operations, you can boost productivity, cut overhead costs, and improve employee satisfaction—all while keeping operations lean and agile. The impact doesn\'t stop there: lower turnover (95% of employers say remote work positively impacts retention) and access to global talent. Ready to explore how virtual staffing can supercharge your business? Download our whitepaper to learn more. [Link to Landing Page]'],
                        ['label' => '(After)', 'text' => 'The shift to remote work isn\'t just a trend – it\'s becoming a FUNDAMENTAL business strategy! 👈 So what does this mean for your business? Just by integrating remote virtual teammates into your operations… 📈 You can boost overall productivity, from sales to customer outreach. ⚙️ Have lower turnover and improve customer retention. 💡 Access incredible global talent who can specialize in anything you need. Ready to explore how virtual staffing can supercharge your business? Download our whitepaper and start transforming your operations today! 👉 [Link to Landing Page]'],
                    ]],
                ]],
                ['label' => 'Day 3', 'blocks' => [
                    ['type' => 'heading', 'text' => 'Social Media Basics & Behavior'],
                    ['type' => 'platforms', 'items' => [
                        ['name' => 'Facebook', 'note' => 'Post 2× a week · short or long text · express opinion, experience or feelings · 1080×1080'],
                        ['name' => 'Instagram', 'note' => 'Post 2× a week · short text with top hashtags · aesthetic lifestyle · 1080×1080'],
                        ['name' => 'LinkedIn', 'note' => 'Post once a week · short or long text · business/professional networking · 1200×1200 | 1080×1350 | 1200×627'],
                        ['name' => 'YouTube', 'note' => 'Post once or 2× a week · detailed description and CTA · educational, inspiration · 1920×1080'],
                        ['name' => 'X / Twitter', 'note' => 'Post everyday · short text · blog headline, quick thoughts, ideological'],
                        ['name' => 'TikTok', 'note' => 'Post everyday · detailed description and CTA · entertainment · 1080×1920'],
                        ['name' => 'Pinterest', 'note' => 'Hook to your main material · tips, educational, hacks · 1000×1500 | 1080×1080'],
                    ]],
                    ['type' => 'heading', 'text' => 'Topic: Coffee — caption examples'],
                    ['type' => 'captions', 'items' => [
                        ['label' => 'Instagram', 'text' => 'Dreamy latte from a hidden gem in the city — love the cup design and vibe! #CoffeeShopInspo #LatteArtIdeas'],
                        ['label' => 'Facebook', 'text' => 'Took a quick break today to recharge at a local café. Sometimes, a good cup of coffee and a change of scenery are all you need to spark new ideas. #WorkLifeBalance #RemoteWork'],
                        ['label' => 'X / Twitter', 'text' => 'Starting my morning right ✨☕ #LatteLove #CoffeeVibes #MorningMood'],
                        ['label' => 'LinkedIn', 'text' => 'Discovered this cute little coffee shop downtown today — the caramel latte was perfect and the vibe was so cozy! Highly recommend if you\'re looking for a peaceful spot to unwind. ☕💬'],
                        ['label' => 'YouTube', 'text' => 'Trying a New Coffee Spot in Cebu – Best Latte Ever? Today I visited Local Cafe for the first time and tried their famous vanilla oat milk latte!'],
                        ['label' => 'TikTok', 'text' => 'Just grabbed the best latte ever. ☕🔥 @LocalCafe is killing it today. #CoffeeFix #LatteArt'],
                    ]],
                    ['type' => 'heading', 'text' => 'Reference: one topic, every platform'],
                    ['type' => 'platforms', 'items' => [
                        ['name' => 'Facebook', 'note' => 'I like donuts'],
                        ['name' => 'Twitter', 'note' => 'I\'m eating a tasty #donut'],
                        ['name' => 'YouTube', 'note' => 'I\'m watching donut videos'],
                        ['name' => 'Snapchat', 'note' => 'Short video with my donut'],
                        ['name' => 'TikTok', 'note' => 'I dance & sing with my donut'],
                        ['name' => 'Instagram', 'note' => 'Here\'s a photo of my donut'],
                        ['name' => 'Pinterest', 'note' => 'Boards of my favorite donuts'],
                        ['name' => 'LinkedIn', 'note' => 'My skills include donut eating'],
                        ['name' => 'Reddit', 'note' => 'Discuss our love for donuts'],
                    ]],
                    ['type' => 'note', 'text' => 'Assignment: Create 1 sample post per platform with graphics or video.'],
                ]],
                ['label' => 'Day 4', 'blocks' => [['type' => 'note', 'text' => 'Day 4 content is coming soon.']]],
                ['label' => 'Day 5', 'blocks' => [['type' => 'note', 'text' => 'Day 5 content is coming soon.']]],
            ],
        ],

        /* ───────────────────────── Sales ────────────────────────────────── */
        'sales' => [
            'slug'  => 'sales',
            'label' => 'Sales',
            'icon'  => 'fa-handshake',
            'h1'    => ['SALES'],
            'eyebrow' => 'Onboarding Course: Client Readiness',
            'intro' => 'A practical, step-by-step reference to help Virtual Assistants communicate professionally, interpret client needs, respond correctly, and handle issues with confidence. Use this as your day-to-day playbook.',
            'search' => true,
            'tabs'  => [
                ['label' => '', 'blocks' => [
                    ['type' => 'summary', 'items' => [
                        ['title' => 'Service Standard', 'sub' => 'Helpful, clear, accountable — always', 'bullets' => ['Acknowledge → Action → Next step → ETA', 'Close the loop (confirm completion)']],
                        ['title' => 'Tone Rule', 'sub' => 'Warm + professional, concise', 'bullets' => ['No guessing on high-impact items', 'No blame, no defensiveness']],
                        ['title' => 'Escalate Fast', 'sub' => 'Security, finance, blocked access, high risk', 'bullets' => ['Issue → Impact → What you tried', 'Evidence → Recommendation → ETA']],
                        ['title' => 'Quality Check', 'sub' => 'Names, dates, links, attachments, scope', 'bullets' => ['Verify you\'re in the correct client workspace', 'Catch errors before the client does']],
                    ]],
                    ['type' => 'accordion', 'items' => [
                        ['title' => '1) The Client Service Mindset', 'tags' => ['Foundations', 'High'], 'sub' => 'Goal: Build trust through consistency, accuracy, and calm communication.', 'bullets' => ['Golden rule: Be helpful, clear, and accountable — never defensive.', 'Default behaviors: Confirm receipt, share next step, provide ETA, follow through.', 'What clients remember: Reliability, tone, and resolution — not excuses.']],
                        ['title' => '2) Tone, Writing Style, and Clarity', 'tags' => ['Communication', 'High'], 'sub' => 'Use a "warm + professional" tone: polite, confident, and concise.', 'bullets' => ['Structure: Acknowledge → Answer/Action → Next step → ETA.', 'Avoid: slang, over-apologizing, blaming, long paragraphs, guessing.', 'Preferred language: "I can help with that." "Here\'s what I\'ll do next." "I\'ll update you by ___."'], 'note' => 'Quick check: If your message sounds emotional, rewrite it once — shorter, calmer, and more specific.'],
                        ['title' => '3) Response Framework + Quick Templates', 'tags' => ['Playbook', 'High'], 'sub' => '', 'bullets' => ['Acknowledge: "Thanks for sharing — got it."', 'Clarify if needed: "To confirm, do you mean A or B?"', 'Commit: "I\'ll take care of X and update you by ___."', 'Close loop: "Completed. Here\'s what changed + what to check next."'], 'note' => 'Standard: If you cannot provide an ETA yet, give the next milestone ETA (e.g., "I\'ll confirm access by 2:00 PM").'],
                        ['title' => '4) Interpreting Client Requests Correctly', 'tags' => ['Skill', 'High'], 'sub' => 'Rule: Do not guess when accuracy matters.', 'bullets' => ['Identify intent: What outcome does the client want?', 'Confirm constraints: Deadline, format, approvals, tools, access.', 'Spot ambiguity: Missing dates, unclear scope, multiple interpretations.', 'Best practice: Offer two options: "Do you prefer A (faster) or B (more detailed)?"']],
                        ['title' => '5) Prioritization, SLAs, and Triage', 'tags' => ['Operations', 'High'], 'sub' => '', 'bullets' => ['Urgent: Revenue-impacting, client-facing outage, compliance, payment/shipping blockers.', 'Important: High value but not time-critical (reporting, cleanup, optimization).', 'When in doubt, ask: "What\'s the deadline and what happens if this waits?"', 'Always communicate ETA and update if the ETA changes.']],
                        ['title' => '6) Handling Upset Clients and De-escalation', 'tags' => ['Communication', 'High'], 'sub' => 'Objective: Reduce emotion, regain clarity, and move toward resolution.', 'bullets' => ['Start with empathy: "I understand this is frustrating."', 'Stay factual: What happened, what you\'re doing now, what\'s next.', 'Never: argue, blame the client, or match their tone.', 'Offer options: "I can do X now, or Y by end of day. Which do you prefer?"']],
                        ['title' => '7) Escalation Rules: When to Ask for Help', 'tags' => ['Process', 'High'], 'sub' => '', 'bullets' => ['Escalate immediately if: security/privacy risk, legal/compliance concern, financial changes, angry client requesting a manager, blocked access, unclear instruction with high impact.', 'Escalation format: Issue → Impact → What you tried → Evidence/links → Recommendation.', 'Client-facing message: "I\'m coordinating with the team and will update you by ___."'], 'note' => 'Rule: Escalation is a professionalism tool — use it early when risk is high.'],
                        ['title' => '8) Confidentiality, Security, and Professional Boundaries', 'tags' => ['Security', 'High'], 'sub' => '', 'bullets' => ['Never share: passwords, private client data, internal details, or screenshots without permission.', 'Use only: approved tools, approved accounts, approved file storage.', 'Access rule: least privilege — only what you need to do the task.', 'If unsure: pause and escalate before proceeding.']],
                        ['title' => '9) Documentation, Updates, and Handoffs', 'tags' => ['Operations', 'Medium'], 'sub' => '', 'bullets' => ['Daily expectation: clear status updates (what\'s done, what\'s next, blockers, ETA).', 'Handoff standard: context + links + current state + next action + owner.', 'Quality tip: write notes so someone else can continue without asking you questions.']],
                        ['title' => '10) Quality Control: Avoiding Common Mistakes', 'tags' => ['Quality', 'Medium'], 'sub' => '', 'bullets' => ['Before sending: Check names, dates, links, attachments, and accuracy.', 'Before acting: Confirm you\'re in the right client account/workspace.', 'When unsure: Ask one clear question instead of making assumptions.', 'Own outcomes: If you make an error, flag it early and propose a fix.']],
                    ]],
                    ['type' => 'ask', 'email' => 'people@virtualteammate.com', 'title' => 'Need help with a client situation?', 'desc' => 'Send your question. Include the client context, what happened, and the urgency.'],
                ]],
            ],
        ],
    ];
}

/* ═════════════════════════════════════════════════════════════════════════
 * PAYSLIPS — recreates the staging [my_payslip] shortcode. Pulls the published
 * payroll Google Sheet (CSV), matches rows to the signed-in VT by name or
 * email, and renders a pay-history ledger. vt_hired only (super_admin may view
 * for support); on-pool VTs never see it.
 * ═════════════════════════════════════════════════════════════════════════ */

/** Published payroll sheet CSV (same source as the staging shortcode). */
function payslip_sheet_url(): string
{
    return 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTE4PXuJwIQff08Wl16hpKxPIbqUB3QA5yCF8IAyoG5_lf7pYoQWEFpO842E6iskZ3ACZj7BurC8bwa/pub?output=csv&timestamp=' . time();
}

/** Fetch a URL with cURL (uses the bundled cacert), returns body or null. */
function payslip_http_get(string $url): ?string
{
    if (!function_exists('curl_init')) {
        $ctx = stream_context_create(['http' => ['timeout' => 20]]);
        $body = @file_get_contents($url, false, $ctx);
        return $body === false ? null : $body;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CAINFO         => __DIR__ . '/cacert.pem',
        CURLOPT_USERAGENT      => 'VT-Portal/1.0',
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($body === false || $code >= 400) { return null; }
    return (string) $body;
}

/** Split published-sheet CSV text into rows (skips blank + `sep=` lines). */
function payslip_parse_csv(string $csv): array
{
    $lines = preg_split("/\r\n|\n|\r/", $csv);
    $rows  = [];
    foreach ($lines as $line) {
        $t = trim((string) $line);
        if ($t === '' || stripos($t, 'sep=') === 0) { continue; }
        $rows[] = str_getcsv($line);
    }
    return $rows;
}

/** First non-empty value among the given candidate column names. */
function payslip_row_value(array $row, array $keys): string
{
    foreach ($keys as $k) {
        $v = trim((string) ($row[$k] ?? ''));
        if ($v !== '') { return $v; }
    }
    return '';
}

function payslip_ts(string $value): int
{
    $value = trim($value);
    if ($value === '') { return 0; }
    $ts = strtotime($value);
    return $ts === false ? 0 : (int) $ts;
}

function payslip_format_date(string $value, string $fallback = '--'): string
{
    $ts = payslip_ts($value);
    return $ts < 1 ? $fallback : date('M j, Y', $ts);
}

function payslip_parse_amount(string $value): ?float
{
    $value = trim($value);
    if ($value === '') { return null; }
    $negative = false;
    if (preg_match('/^\((.*)\)$/', $value, $m)) { $negative = true; $value = $m[1]; }
    $value = preg_replace('/[^0-9.\-]/', '', $value);
    if ($value === '' || !is_numeric($value)) { return null; }
    $amount = (float) $value;
    return $negative ? -$amount : $amount;
}

function payslip_format_amount(?float $amount): string
{
    return $amount === null ? '--' : '$' . number_format($amount, 2);
}

/** Normalized name "signatures" so "Dela Cruz, Maria" matches "Maria Dela Cruz". */
function payslip_name_signatures(string $value): array
{
    $n = strtolower(trim(strip_tags($value)));
    $n = preg_replace('/[^a-z0-9\s]/', ' ', $n);
    $n = trim(preg_replace('/\s+/', ' ', $n));
    if ($n === '') { return []; }
    $tokens = array_values(array_filter(explode(' ', $n)));
    sort($tokens, SORT_STRING);
    return array_values(array_unique(array_filter([$n, implode(' ', $tokens)])));
}

/** True if a sheet row belongs to the current VT (by name or email). */
function payslip_row_matches(array $row, array $vtNames, string $email): bool
{
    foreach (['VT Name', 'Virtual Teammate', 'Name', 'Employee Name', 'Full Name'] as $h) {
        $rowSigs = payslip_name_signatures($row[$h] ?? '');
        if (!$rowSigs) { continue; }
        foreach ($vtNames as $cand) {
            if (array_intersect($rowSigs, payslip_name_signatures($cand))) { return true; }
        }
    }
    $rowEmail = strtolower(trim((string) ($row['VT Email'] ?? $row['Email'] ?? '')));
    return $rowEmail !== '' && $email !== '' && $rowEmail === strtolower($email);
}

function handle_payslips(): void
{
    $u = require_role('vt_hired', 'super_admin');

    $email   = strtolower(trim((string) ($u['email'] ?? '')));
    $vtNames = array_values(array_unique(array_filter([
        trim(user_display_name($u)),
        trim(((string) ($u['first_name'] ?? '')) . ' ' . ((string) ($u['last_name'] ?? ''))),
    ])));
    if (!$vtNames) { $vtNames = [$email]; }

    $error    = '';
    $payslips = [];
    $header   = [];

    $body = payslip_http_get(payslip_sheet_url());
    if ($body === null) {
        $error = 'Unable to load payslip data right now. Please try again later.';
    } else {
        $rows = payslip_parse_csv($body);
        if (!$rows) {
            $error = 'No payslip data available yet.';
        } else {
            $header = array_map('trim', (array) array_shift($rows));
            foreach ($rows as $row) {
                $row = array_map('trim', (array) $row);
                if (!array_filter($row, 'strlen')) { continue; }
                if (count($row) < count($header)) { $row = array_pad($row, count($header), ''); }
                $assoc = @array_combine($header, array_slice($row, 0, count($header)));
                if (!is_array($assoc)) { continue; }
                if (payslip_row_matches($assoc, $vtNames, $email)) { $payslips[] = $assoc; }
            }
        }
    }

    $dateHeaders   = ['Pay Date', 'Date', 'Payroll Date', 'Issued Date', 'Payout Date'];
    $periodHeaders = ['Pay Period', 'Payroll Period', 'Period', 'Coverage', 'Month', 'Cutoff'];
    $amountFields  = ['Net Pay', 'Amount', 'Net Salary', 'Total Pay', 'Salary', 'Gross Pay', 'Payout', 'Net Amount'];

    usort($payslips, static function ($a, $b) use ($dateHeaders) {
        return payslip_ts(payslip_row_value($b, $dateHeaders)) <=> payslip_ts(payslip_row_value($a, $dateHeaders));
    });

    render('payslips', [
        'error'         => $error,
        'payslips'      => $payslips,
        'header'        => $header,
        'visibleCols'   => array_values(array_filter($header, 'strlen')),
        'dateHeaders'   => $dateHeaders,
        'periodHeaders' => $periodHeaders,
        'amountFields'  => $amountFields,
        'accountName'   => $vtNames[0] ?? $email,
        'vtNames'       => $vtNames,
    ]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * MY CSM & TEAMMATES — VT-facing people page: current client(s), the CSM(s)
 * on the account, and fellow VTs on the same engagement, with chat/email/call
 * actions. vt_hired only (super_admin may view).
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_my_team(): void
{
    $u   = require_role('vt_hired', 'super_admin');
    $pdo = db();
    $uid = (int) $u['id'];

    $person = 'u.id, u.first_name, u.last_name, u.email, u.phone, u.photo_url, u.role, u.job_title';

    $stmt = $pdo->prepare(
        "SELECT cv.client_id, c.company_name, c.company_email, c.company_domain,
                c.contract_status, c.user_id AS client_user_id
         FROM client_vts cv
         JOIN clients c ON c.id = cv.client_id
         WHERE cv.vt_user_id = :uid AND cv.contract_status = 'active'
         ORDER BY c.company_name"
    );
    $stmt->execute([':uid' => $uid]);
    $clients = $stmt->fetchAll();

    $engagements = [];
    foreach ($clients as $c) {
        $cid = (int) $c['client_id'];

        $contact = null;
        if (!empty($c['client_user_id'])) {
            $cs = $pdo->prepare("SELECT {$person} FROM users u WHERE u.id = :id");
            $cs->execute([':id' => (int) $c['client_user_id']]);
            $contact = $cs->fetch() ?: null;
        }

        $cs = $pdo->prepare(
            "SELECT {$person} FROM csm_clients cc JOIN users u ON u.id = cc.csm_user_id
             WHERE cc.client_id = :cid ORDER BY u.first_name"
        );
        $cs->execute([':cid' => $cid]);
        $csms = $cs->fetchAll();

        $ts = $pdo->prepare(
            "SELECT {$person} FROM client_vts cv JOIN users u ON u.id = cv.vt_user_id
             WHERE cv.client_id = :cid AND cv.contract_status = 'active' AND u.id != :uid
             ORDER BY u.first_name"
        );
        $ts->execute([':cid' => $cid, ':uid' => $uid]);
        $teammates = $ts->fetchAll();

        $engagements[] = [
            'client'    => $c,
            'contact'   => $contact,
            'csms'      => $csms,
            'teammates' => $teammates,
        ];
    }

    render('my-team', ['user' => $u, 'engagements' => $engagements]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * VTM APPS — download links for the tools VTs use day to day.
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_vtm_apps(): void
{
    require_role('vt_hired', 'vt_onpool', 'super_admin');
    $apps = [
        ['name' => 'Workday Tracker', 'icon' => 'fa-clock',           'accent' => '#3919BA', 'url' => 'https://workdaytracker.com/app/user/downloads',                                  'desc' => 'Track your work hours and submit productivity reports. Install the desktop tracker.', 'cta' => 'Download Workday Tracker'],
        ['name' => 'Wise',            'icon' => 'fa-money-bill-transfer','accent' => '#16a34a', 'url' => 'https://wise.com/invite/irtc/dominiquecuatonr',                                  'desc' => 'Receive your payouts internationally with low fees. Sign up with our invite link.',   'cta' => 'Get started with Wise'],
        ['name' => 'Slack',           'icon' => 'fa-slack',            'accent' => '#611f69', 'url' => 'https://slack.com/downloads/instructions/windows?ddl=1&build=win64_msix',        'desc' => 'Team chat for staying in sync with your client, CSM, and teammates. Install for Windows.', 'cta' => 'Download Slack for Windows'],
    ];
    render('vtm-apps', ['apps' => $apps]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * REFER A FRIEND — VTs refer a friend (name + email + optional note); the
 * referral is delivered to every active super admin as a notification (and an
 * email, for admins who opted in).
 * ═════════════════════════════════════════════════════════════════════════ */

function handle_refer(): void
{
    $u = require_role('vt_hired', 'vt_onpool', 'super_admin');

    $name = $email = $note = '';
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $name  = trim((string) ($_POST['friend_name'] ?? ''));
        $email = trim((string) ($_POST['friend_email'] ?? ''));
        $note  = trim((string) ($_POST['note'] ?? ''));

        if ($name === '')                                          { $errors[] = "Your friend's name is required."; }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'A valid email address is required.'; }

        if (!$errors) {
            $referrer = trim(user_display_name($u));
            $title = 'New VT referral from ' . ($referrer !== '' ? $referrer : (string) $u['email']);
            $body  = "Friend's name: {$name}\n"
                   . "Friend's email: {$email}\n"
                   . ($note !== '' ? "Note: {$note}\n" : '')
                   . "Referred by: " . ($referrer !== '' ? $referrer : '') . " ({$u['email']})";

            $admins = db()->query("SELECT id FROM users WHERE role = 'super_admin' AND active = 1")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($admins as $aid) {
                notify((int) $aid, 'referral', $title, $body);
            }
            audit_log('referral_submitted', 'user', (int) $u['id'], 'friend=' . $email);

            flash('success', 'Thanks! Your referral was sent to the Virtual Teammate team.');
            redirect(portal_url('refer'));
        }
    }

    render('refer', ['user' => $u, 'errors' => $errors, 'friend_name' => $name, 'friend_email' => $email, 'note' => $note]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * MY VTs — dedicated tab listing assigned Virtual Teammates, modeled on
 * the staging [selected_va_list] shortcode (large circular avatar cards).
 * ═════════════════════════════════════════════════════════════════════════ */

/**
 * VT profile modal data — returns one VT's full profile as JSON.
 * Authorization mirrors handle_media_serve: super_admin sees everyone;
 * client / csm see only VTs in their assigned engagements; vt_hired
 * can fetch themselves.
 */
function handle_vts_profile_json(): void
{
    $u   = require_login();
    $vid = (int) ($_GET['id'] ?? 0);
    if ($vid <= 0) { http_response_code(400); echo json_encode(['error' => 'bad id']); return; }
    $pdo = db();

    // Authorize the requester can see this VT.
    $allowed = false;
    if ($u['role'] === 'super_admin') {
        $allowed = true;
    } elseif ((int) $u['id'] === $vid) {
        $allowed = true;
    } elseif ($u['role'] === 'client') {
        $stmt = $pdo->prepare(
            "SELECT 1 FROM clients c JOIN client_vts cv ON cv.client_id = c.id
             WHERE c.user_id = :uid AND cv.vt_user_id = :vid
               AND cv.contract_status = 'active' LIMIT 1"
        );
        $stmt->execute([':uid' => $u['id'], ':vid' => $vid]);
        $allowed = (bool) $stmt->fetchColumn();
    } elseif ($u['role'] === 'csm') {
        $stmt = $pdo->prepare(
            "SELECT 1 FROM csm_clients cc
             JOIN client_vts cv ON cv.client_id = cc.client_id
             WHERE cc.csm_user_id = :uid AND cv.vt_user_id = :vid LIMIT 1"
        );
        $stmt->execute([':uid' => $u['id'], ':vid' => $vid]);
        $allowed = (bool) $stmt->fetchColumn();
    }
    if (!$allowed) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'forbidden']);
        return;
    }

    // Pull the full profile (vt_profiles + users join).
    $stmt = $pdo->prepare(
        "SELECT p.*, u.email, u.first_name, u.last_name, u.phone, u.country,
                u.photo_url, u.cover_url, u.active, u.last_login_at,
                u.hubspot_contact_id, u.job_title
         FROM vt_profiles p
         JOIN users u ON u.id = p.user_id
         WHERE p.user_id = :id"
    );
    $stmt->execute([':id' => $vid]);
    $vt = $stmt->fetch();
    if (!$vt) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'not found']);
        return;
    }

    // Engaged clients (active only).
    $cs = $pdo->prepare(
        "SELECT c.id, c.company_name, cv.started_at, cv.ended_at, cv.contract_status
         FROM client_vts cv JOIN clients c ON c.id = cv.client_id
         WHERE cv.vt_user_id = :id AND cv.contract_status = 'active'
         ORDER BY cv.started_at"
    );
    $cs->execute([':id' => $vid]);
    $clients = $cs->fetchAll();

    // CSMs covering those engagements.
    $cm = $pdo->prepare(
        "SELECT DISTINCT u.id, u.first_name, u.last_name, u.email
         FROM client_vts cv
         JOIN csm_clients cc ON cc.client_id = cv.client_id
         JOIN users u ON u.id = cc.csm_user_id
         WHERE cv.vt_user_id = :id"
    );
    $cm->execute([':id' => $vid]);
    $csms = $cm->fetchAll();

    // Resolve stored media URLs to portal-usable srcs (vtmedia photos need the
    // site base; gated resume/video endpoints pass through unchanged).
    foreach (['photo_url', 'cover_url', 'video_url', 'resume_url'] as $mk) {
        if (isset($vt[$mk])) { $vt[$mk] = media_src((string) $vt[$mk]); }
    }

    header('Content-Type: application/json');
    header('Cache-Control: private, no-store');
    echo json_encode([
        'vt'      => $vt,
        'clients' => $clients,
        'csms'    => $csms,
    ], JSON_UNESCAPED_SLASHES);
}

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

/** CSM: unified "My Clients & VTs" — each managed client with its active team. */
function handle_my_clients(): void
{
    $u = require_login();
    if (($u['role'] ?? '') !== 'csm') {
        render('error', ['title' => 'Forbidden', 'message' => 'My Clients is a CSM view.']);
        return;
    }
    $pdo = db();
    $stmt = $pdo->prepare(
        "SELECT c.id, c.company_name, c.company_email, c.company_domain, c.contract_status,
                u.id AS user_id, u.first_name, u.last_name, u.email AS contact_email
         FROM csm_clients cc
         JOIN clients c ON c.id = cc.client_id
         LEFT JOIN users u ON u.id = c.user_id
         WHERE cc.csm_user_id = :uid
         ORDER BY c.company_name COLLATE NOCASE"
    );
    $stmt->execute([':uid' => (int) $u['id']]);
    $clients = $stmt->fetchAll();

    // Attach each client's active VTs (one query, grouped in PHP).
    $byClient = [];
    $vstmt = $pdo->prepare(
        "SELECT cv.client_id, u.id AS vt_id, u.first_name, u.last_name, u.country, u.photo_url,
                p.role_title, p.department
         FROM csm_clients cc
         JOIN client_vts cv ON cv.client_id = cc.client_id AND cv.contract_status = 'active'
         JOIN users u ON u.id = cv.vt_user_id
         LEFT JOIN vt_profiles p ON p.user_id = u.id
         WHERE cc.csm_user_id = :uid
         ORDER BY u.first_name, u.last_name"
    );
    $vstmt->execute([':uid' => (int) $u['id']]);
    foreach ($vstmt->fetchAll() as $vt) {
        $byClient[(int) $vt['client_id']][] = $vt;
    }
    foreach ($clients as &$c) {
        $c['vts']      = $byClient[(int) $c['id']] ?? [];
        $c['vt_count'] = count($c['vts']);
    }
    unset($c);

    // Client VT requests across this CSM's accounts (pending first, newest first).
    vt_requests_ensure($pdo);
    $rstmt = $pdo->prepare(
        "SELECT r.*, c.company_name,
                vu.first_name AS vt_first, vu.last_name AS vt_last,
                vu.country AS vt_user_country,
                p.role_title, p.department,
                cu.first_name AS cl_first, cu.last_name AS cl_last, cu.email AS cl_email
           FROM vt_requests r
           JOIN csm_clients cc ON cc.client_id = r.client_id AND cc.csm_user_id = :uid
           JOIN clients c ON c.id = r.client_id
           JOIN users vu ON vu.id = r.vt_user_id
           LEFT JOIN vt_profiles p ON p.user_id = r.vt_user_id
           LEFT JOIN users cu ON cu.id = r.requested_by
          WHERE r.csm_deleted = 0
          ORDER BY (r.status = 'pending') DESC, r.id DESC
          LIMIT 60"
    );
    $rstmt->execute([':uid' => (int) $u['id']]);
    $requests = $rstmt->fetchAll();
    $pendingCount = 0; foreach ($requests as $rq) { if (($rq['status'] ?? '') === 'pending') { $pendingCount++; } }

    render('my-clients', [
        'user'          => $u,
        'clients'       => $clients,
        'requests'      => $requests,
        'pending_count' => $pendingCount,
    ]);
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
            "SELECT u.id AS user_id, u.first_name, u.last_name, u.email, u.photo_url,
                    p.workday_link, p.workday_tracker_id, p.role_title, p.department,
                    cv.client_id, cv.workday_link AS cv_workday_link, cv.workday_tracker_id AS cv_workday_tracker_id,
                    c.company_name
             FROM users u
             LEFT JOIN vt_profiles p ON p.user_id = u.id
             LEFT JOIN client_vts  cv ON cv.vt_user_id = u.id AND cv.contract_status = 'active'
             LEFT JOIN clients      c ON c.id = cv.client_id
             WHERE u.id = :uid"
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
            "SELECT u.id AS user_id, u.first_name, u.last_name, u.email, u.photo_url,
                    p.workday_link AS profile_workday_link, p.workday_tracker_id AS profile_tracker_id,
                    p.role_title, p.department,
                    cv.workday_link AS cv_workday_link, cv.workday_tracker_id AS cv_workday_tracker_id
             FROM client_vts cv
             JOIN users u ON u.id = cv.vt_user_id
             LEFT JOIN vt_profiles p ON p.user_id = u.id
             WHERE cv.client_id = :cid AND cv.contract_status = 'active'
             ORDER BY u.first_name"
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
    $pdo  = db();
    $uid  = (int) $u['id'];
    $cols = 'u.id, u.first_name, u.last_name, u.email, u.role, u.photo_url';

    if ($u['role'] === 'super_admin') {
        // Super admin can message everyone — including pool VTs (vt_onpool).
        return $pdo->query(
            "SELECT id, first_name, last_name, email, role, photo_url FROM users
             WHERE id != {$uid} AND active = 1
             ORDER BY last_name, first_name"
        )->fetchAll();
    }

    $rows = [];
    if ($u['role'] === 'client') {
        $stmt = $pdo->prepare(
            "SELECT {$cols}
             FROM clients c
             JOIN client_vts cv ON cv.client_id = c.id AND cv.contract_status = 'active'
             JOIN users u ON u.id = cv.vt_user_id
             WHERE c.user_id = :uid
             UNION
             SELECT {$cols}
             FROM clients c
             JOIN csm_clients cc ON cc.client_id = c.id
             JOIN users u ON u.id = cc.csm_user_id
             WHERE c.user_id = :uid"
        );
        $stmt->execute([':uid' => $uid]);
        $rows = $stmt->fetchAll();
    } elseif ($u['role'] === 'csm') {
        // Both the client contacts AND the VTs on the CSM's assigned accounts.
        // (A COALESCE here previously hid the client contact whenever the client
        // had active VTs, so CSMs couldn't message their client companies.)
        $stmt = $pdo->prepare(
            "SELECT {$cols}
               FROM csm_clients cc
               JOIN clients c ON c.id = cc.client_id
               JOIN users u ON u.id = c.user_id
              WHERE cc.csm_user_id = :uid
              UNION
             SELECT {$cols}
               FROM csm_clients cc
               JOIN client_vts cv ON cv.client_id = cc.client_id AND cv.contract_status = 'active'
               JOIN users u ON u.id = cv.vt_user_id
              WHERE cc.csm_user_id = :uid
              UNION
             SELECT {$cols}
               FROM users u
              WHERE u.role = 'vt_onpool' AND u.active = 1"
        );
        $stmt->execute([':uid' => $uid]);
        $rows = $stmt->fetchAll();
    } elseif ($u['role'] === 'vt_hired') {
        // A hired VT may message: the related client(s), the CSM(s) on those
        // accounts, and their teammates (other active VTs on the same client).
        $stmt = $pdo->prepare(
            "SELECT DISTINCT {$cols}
               FROM client_vts cv
               JOIN clients c ON c.id = cv.client_id
               JOIN users u ON u.id = c.user_id
              WHERE cv.vt_user_id = :uid AND cv.contract_status = 'active'
             UNION
             SELECT DISTINCT {$cols}
               FROM client_vts cv
               JOIN csm_clients cc ON cc.client_id = cv.client_id
               JOIN users u ON u.id = cc.csm_user_id
              WHERE cv.vt_user_id = :uid AND cv.contract_status = 'active'
             UNION
             SELECT DISTINCT {$cols}
               FROM client_vts cv
               JOIN client_vts cv2 ON cv2.client_id = cv.client_id AND cv2.contract_status = 'active'
               JOIN users u ON u.id = cv2.vt_user_id
              WHERE cv.vt_user_id = :uid AND cv.contract_status = 'active' AND u.id != :uid"
        );
        $stmt->execute([':uid' => $uid]);
        $rows = $stmt->fetchAll();
    } elseif ($u['role'] === 'vt_onpool') {
        // Pool VTs (not yet placed) can reach the team that manages the pool:
        // all CSMs and super admins.
        $rows = $pdo->query(
            "SELECT id, first_name, last_name, email, role, photo_url FROM users
              WHERE active = 1 AND role IN ('csm', 'super_admin') AND id != {$uid}
              ORDER BY role, last_name, first_name"
        )->fetchAll();
    }

    // Reachable beyond the role list: ANYONE the user already has a conversation
    // with — otherwise a thread the other party started could never be answered.
    // For non-VT roles we also always surface the admin team for support. Hired
    // VTs are intentionally limited to teammates / CSM / client (plus anyone who
    // already messaged them), so we do NOT force the whole admin team in for them.
    $partnerIds = [];
    try {
        $partnerIds = array_map('intval', chatdb()->query(
            "SELECT DISTINCT CASE WHEN sender_user_id = {$uid} THEN receiver_user_id ELSE sender_user_id END
               FROM messages WHERE sender_user_id = {$uid} OR receiver_user_id = {$uid}"
        )->fetchAll(PDO::FETCH_COLUMN));
    } catch (Throwable $_) {}
    $conds = [];
    if ($u['role'] !== 'vt_hired') { $conds[] = "role = 'super_admin'"; }
    if ($partnerIds) { $conds[] = 'id IN (' . implode(',', $partnerIds) . ')'; }
    $extra = [];
    if ($conds) {
        $extra = $pdo->query(
            "SELECT id, first_name, last_name, email, role, photo_url FROM users
              WHERE active = 1 AND id != {$uid} AND (" . implode(' OR ', $conds) . ")
              ORDER BY last_name, first_name"
        )->fetchAll();
    }

    // Merge + dedupe by id (role-based contacts first, then admins / existing chats).
    $byId = [];
    foreach (array_merge($rows, $extra) as $r) { $byId[(int) $r['id']] = $r; }
    return array_values($byId);
}

function handle_messages_list(): void
{
    $u = require_login();
    if (!in_array($u['role'], ['super_admin','client','csm','vt_hired','vt_onpool'], true)) {
        render('error', ['title' => 'Forbidden', 'message' => 'Messages not available for your role.']);
        return;
    }
    $pdo = db();
    $cdb = chatdb();
    $me  = (int) $u['id'];
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
                $key = messages_conversation_key($me, $with);
                $stmt = $cdb->prepare(
                    "SELECT * FROM messages WHERE conversation_key = :k ORDER BY id ASC LIMIT 500"
                );
                $stmt->execute([':k' => $key]);
                $messages = $stmt->fetchAll();
                // Mark partner-sent messages as read.
                $cdb->prepare("UPDATE messages SET read_at = CURRENT_TIMESTAMP WHERE conversation_key = :k AND sender_user_id = :p AND read_at IS NULL")
                    ->execute([':k' => $key, ':p' => $with]);
            }
        }
    }

    // Unread badge counts: ONE query, grouped by sender (chat DB).
    $unreadByContact = [];
    foreach ($contacts as $c) { $unreadByContact[(int) $c['id']] = 0; }
    foreach ($cdb->query("SELECT sender_user_id, COUNT(*) AS n FROM messages WHERE receiver_user_id = {$me} AND read_at IS NULL GROUP BY sender_user_id") as $row) {
        $sid = (int) $row['sender_user_id'];
        if (array_key_exists($sid, $unreadByContact)) { $unreadByContact[$sid] = (int) $row['n']; }
    }

    // Order contacts by most-recent conversation activity (newest first) so a
    // contact with a fresh incoming message floats to the top of the list.
    $lastAct = [];
    foreach ($cdb->query("SELECT conversation_key, MAX(id) AS mid FROM messages GROUP BY conversation_key") as $row) {
        $lastAct[$row['conversation_key']] = (int) $row['mid'];
    }
    usort($contacts, static function ($a, $b) use ($lastAct, $me) {
        $ka = messages_conversation_key($me, (int) $a['id']);
        $kb = messages_conversation_key($me, (int) $b['id']);
        return ($lastAct[$kb] ?? 0) <=> ($lastAct[$ka] ?? 0);
    });

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
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
           && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $jsonFail = static function (string $msg, int $code) {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => $msg]);
        exit;
    };

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('messages')); }
    csrf_verify();
    $with = (int) ($_POST['with'] ?? 0);
    $body = trim((string) ($_POST['body'] ?? ''));
    if ($with <= 0 || $body === '') {
        if ($isAjax) { $jsonFail('Empty message.', 400); }
        redirect(portal_url('messages', $with ? ['with' => $with] : []));
    }

    // Authorize the recipient is in our contacts.
    $contacts = messages_contacts($u);
    $ok = $u['role'] === 'super_admin';
    foreach ($contacts as $c) { if ((int) $c['id'] === $with) { $ok = true; break; } }
    if (!$ok) {
        if ($isAjax) { $jsonFail('You can not message that user.', 403); }
        flash('error', 'You can not message that user.');
        redirect(portal_url('messages'));
    }

    $key  = messages_conversation_key((int) $u['id'], $with);
    $body = mb_substr($body, 0, 4000);
    $cdb  = chatdb();
    $cdb->prepare(
        'INSERT INTO messages (conversation_key, sender_user_id, receiver_user_id, body)
         VALUES (:k, :s, :r, :b)'
    )->execute([':k' => $key, ':s' => $u['id'], ':r' => $with, ':b' => $body]);
    $newId = (int) $cdb->lastInsertId();
    $row   = $cdb->query("SELECT created_at FROM messages WHERE id = {$newId}")->fetch();

    // Surface to the recipient as a notification (+ email when opted-in).
    $senderName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: (string) ($u['email'] ?? 'A teammate');
    notify(
        $with,
        'message',
        'New message from ' . $senderName,
        mb_substr($body, 0, 220),
        'index.php?p=messages&with=' . (int) $u['id'],
    );

    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => true, 'message' => [
            'id'         => $newId,
            'mine'       => true,
            'body'       => $body,
            'created_at' => $row['created_at'] ?? '',
        ]]);
        exit;
    }
    redirect(portal_url('messages', ['with' => $with]));
}

/**
 * AJAX poll: returns new messages in the open conversation (id > after) and a
 * per-sender unread map (ordered newest-first) so the client can live-append
 * incoming messages and bump senders with new messages to the top of the list.
 */
function handle_messages_fetch(): void
{
    $u = require_login();
    header('Content-Type: application/json; charset=UTF-8');
    if (!in_array($u['role'], ['super_admin', 'client', 'csm', 'vt_hired', 'vt_onpool'], true)) {
        echo json_encode(['ok' => false]); return;
    }
    $me    = (int) $u['id'];
    $with  = (int) ($_GET['with'] ?? 0);
    $after = (int) ($_GET['after'] ?? 0);
    $cdb   = chatdb();

    // New messages in the OPEN conversation (if authorized), then mark read.
    $newMsgs = [];
    if ($with > 0) {
        $contacts = messages_contacts($u);
        $ok = $u['role'] === 'super_admin';
        foreach ($contacts as $c) { if ((int) $c['id'] === $with) { $ok = true; break; } }
        if ($ok) {
            $key = messages_conversation_key($me, $with);
            $st  = $cdb->prepare("SELECT id, sender_user_id, body, created_at FROM messages WHERE conversation_key = :k AND id > :a ORDER BY id ASC LIMIT 300");
            $st->execute([':k' => $key, ':a' => $after]);
            foreach ($st->fetchAll() as $m) {
                $newMsgs[] = ['id' => (int) $m['id'], 'mine' => ((int) $m['sender_user_id'] === $me), 'body' => $m['body'], 'created_at' => $m['created_at']];
            }
            $cdb->prepare("UPDATE messages SET read_at = CURRENT_TIMESTAMP WHERE conversation_key = :k AND sender_user_id = :p AND read_at IS NULL")
                ->execute([':k' => $key, ':p' => $with]);
        }
    }

    // Unread-by-sender, newest incoming first (drives sidebar badge + reorder).
    $unread = [];
    foreach ($cdb->query("SELECT sender_user_id, COUNT(*) AS n, MAX(id) AS mid FROM messages WHERE receiver_user_id = {$me} AND read_at IS NULL GROUP BY sender_user_id ORDER BY mid DESC") as $row) {
        $unread[] = ['id' => (int) $row['sender_user_id'], 'n' => (int) $row['n']];
    }
    echo json_encode(['ok' => true, 'messages' => $newMsgs, 'unread' => $unread]);
}

/** Super-admin: delete an entire conversation thread (both directions). */
function handle_messages_clear(): void
{
    $u = require_login();
    if (($u['role'] ?? '') !== 'super_admin') {
        http_response_code(403);
        render('error', ['title' => 'Forbidden', 'message' => 'Only an administrator can delete conversations.']);
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('messages')); }
    csrf_verify();
    $with = (int) ($_POST['with'] ?? 0);
    if ($with <= 0) { redirect(portal_url('messages')); }

    $key = messages_conversation_key((int) $u['id'], $with);
    $n   = chatdb()->prepare('DELETE FROM messages WHERE conversation_key = :k');
    $n->execute([':k' => $key]);
    flash('success', 'Conversation deleted (' . $n->rowCount() . ' message' . ($n->rowCount() === 1 ? '' : 's') . ').');
    redirect(portal_url('messages', ['with' => $with]));
}

/* ═════════════════════════════════════════════════════════════════════════
 * INVOICES — client billing records, pulled live from HubSpot (invoices object
 * associated to the client's company/contact). Mirrors the staging WP shortcode
 * [hubspot_invoice_viewer]: resolve contact ids by email, gather invoice ids
 * from company + contact associations, batch-read properties, group by status.
 * ═════════════════════════════════════════════════════════════════════════ */

/** Normalize a HubSpot date prop (ms-epoch or date string) to a unix timestamp. */
function invoices_ts($v): int
{
    $v = trim((string) $v);
    if ($v === '') { return 0; }
    if (is_numeric($v)) { $n = (int) $v; return $n > 9999999999 ? (int) round($n / 1000) : $n; }
    return (int) strtotime($v);
}

function handle_invoices(): void
{
    $u = require_login();
    if (($u['role'] ?? '') !== 'client') {
        render('error', ['title' => 'Not available', 'message' => 'Invoices are available to client accounts.']);
        return;
    }
    require_once __DIR__ . '/hubspot.php';

    $cstmt = db()->prepare('SELECT * FROM clients WHERE user_id = :uid');
    $cstmt->execute([':uid' => (int) $u['id']]);
    $client    = $cstmt->fetch() ?: [];
    $companyId = trim((string) ($client['hubspot_company_id'] ?? ''));
    $contactId = trim((string) ($u['hubspot_contact_id'] ?? ''));
    $emails    = array_values(array_unique(array_filter(array_map(
        static fn ($e) => strtolower(trim((string) $e)),
        [$u['email'] ?? '', $client['billing_contact_email'] ?? '', $client['company_email'] ?? '']
    ))));

    $token  = (string) (hs_settings()['hs_token'] ?? '');
    $rows   = [];
    $error  = '';
    $totals = ['billed' => 0.0, 'paid' => 0, 'open' => 0, 'outstanding' => 0.0];

    if ($token === '') {
        $error = 'Billing isn\'t connected yet — please check back soon.';
    } elseif ($companyId === '' && $contactId === '' && empty($emails)) {
        $error = 'We couldn\'t match your account to any billing records yet.';
    } else {
        try {
            $hs    = new HubSpotClient($token);
            $props = ['hs_invoice_number', 'hs_invoice_link', 'hs_title', 'hs_amount_billed', 'amount', 'hs_invoice_status', 'hs_due_date', 'hs_payment_date', 'name'];

            // 1) Resolve contact ids (stored id + any matching the account emails).
            $contactIds = [];
            if ($contactId !== '') { $contactIds[$contactId] = true; }
            foreach ($emails as $em) {
                $r = $hs->request('POST', '/crm/v3/objects/contacts/search', [
                    'filterGroups' => [['filters' => [['propertyName' => 'email', 'operator' => 'EQ', 'value' => $em]]]],
                    'properties'   => ['email'],
                    'limit'        => 5,
                ]);
                if ($r['ok']) {
                    foreach (($r['data']['results'] ?? []) as $row) {
                        $cid = trim((string) ($row['id'] ?? ''));
                        if ($cid !== '') { $contactIds[$cid] = true; }
                    }
                }
            }

            // 2) Gather invoice ids from company + contact associations.
            $invoiceIds = [];
            $assocFrom  = static function (string $objType, string $objId) use ($hs, &$invoiceIds): void {
                if ($objId === '') { return; }
                $r = $hs->request('GET', sprintf('/crm/v4/objects/%s/%s/associations/invoices?limit=100', rawurlencode($objType), rawurlencode($objId)));
                if ($r['ok']) {
                    foreach (($r['data']['results'] ?? []) as $a) {
                        $iid = trim((string) ($a['toObjectId'] ?? ''));
                        if ($iid !== '') { $invoiceIds[$iid] = true; }
                    }
                }
            };
            $assocFrom('companies', $companyId);
            foreach (array_keys($contactIds) as $cid) { $assocFrom('contacts', $cid); }

            // 3) Batch-read invoice properties.
            $invoices = [];
            foreach (array_chunk(array_keys($invoiceIds), 100) as $chunk) {
                $r = $hs->request('POST', '/crm/v3/objects/invoices/batch/read', [
                    'properties' => $props,
                    'inputs'     => array_map(static fn ($id) => ['id' => (string) $id], $chunk),
                ]);
                if ($r['ok']) {
                    foreach (($r['data']['results'] ?? []) as $row) { $invoices[] = $row; }
                }
            }

            // 4) Group + total.
            $paidSet = ['PAID', 'SETTLED', 'COMPLETED'];
            $lateSet = ['OVERDUE', 'PAST_DUE'];
            foreach ($invoices as $inv) {
                $p         = is_array($inv['properties'] ?? null) ? $inv['properties'] : [];
                $statusRaw = strtoupper(trim((string) ($p['hs_invoice_status'] ?? 'UNKNOWN')));
                $amount    = (float) ($p['hs_amount_billed'] ?? ($p['amount'] ?? 0));
                if (in_array($statusRaw, $paidSet, true)) {
                    $key = 'paid'; $totals['paid']++;
                } elseif (in_array($statusRaw, $lateSet, true)) {
                    $key = 'late'; $totals['open']++; $totals['outstanding'] += $amount;
                } else {
                    $key = 'pending'; $totals['open']++; $totals['outstanding'] += $amount;
                }
                $totals['billed'] += $amount;
                $rows[] = [
                    'number'       => (string) ($p['hs_invoice_number'] ?? ($p['name'] ?? ('INV-' . substr((string) ($inv['id'] ?? ''), -4)))),
                    'title'        => (string) ($p['hs_title'] ?? 'Billing record'),
                    'amount'       => $amount,
                    'status_key'   => $key,
                    'status_label' => ucwords(strtolower(str_replace('_', ' ', $statusRaw))),
                    'due_ts'       => invoices_ts($p['hs_due_date'] ?? ''),
                    'paid_ts'      => invoices_ts($p['hs_payment_date'] ?? ''),
                    'link'         => (string) ($p['hs_invoice_link'] ?? ''),
                ];
            }
            usort($rows, static fn ($a, $b) => $b['due_ts'] <=> $a['due_ts']);
            if (empty($rows)) { $error = 'No billing records were found for your account yet.'; }
        } catch (Throwable $e) {
            $error = 'We couldn\'t load your billing records right now — please try again later.';
        }
    }

    render('invoices', [
        'user'    => $u,
        'company' => (string) ($client['company_name'] ?? ''),
        'rows'    => $rows,
        'totals'  => $totals,
        'error'   => $error,
    ]);
}

/* ═════════════════════════════════════════════════════════════════════════
 * REQUEST AN ADDITIONAL VT — suggested available pool teammates for the client
 * (mirrors the staging WP [vt_request_pool]): show active on-pool VTs the client
 * doesn't already have, department-matched to their current team first. POSTing
 * a request notifies the client's CSM(s) + super admins.
 * ═════════════════════════════════════════════════════════════════════════ */
// vt_requests_ensure() now lives in bootstrap.php so the public talent
// directory's request endpoint can share it (clients request a pool VT, CSMs
// approve/reject with a note, both sides can dismiss from their own view).

function handle_request_vt(): void
{
    $u = require_login();
    if (($u['role'] ?? '') !== 'client') {
        render('error', ['title' => 'Not available', 'message' => 'This page is available to client accounts.']);
        return;
    }
    $pdo = db();
    vt_requests_ensure($pdo);
    $cstmt = $pdo->prepare('SELECT * FROM clients WHERE user_id = :uid');
    $cstmt->execute([':uid' => (int) $u['id']]);
    $client = $cstmt->fetch() ?: null;
    $cid    = (int) ($client['id'] ?? 0);

    // Handle a request submission.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();

        // Client dismisses a request from THEIR view only (CSM keeps their copy).
        if (($_POST['action'] ?? '') === 'delete') {
            $rid = (int) ($_POST['request_id'] ?? 0);
            if ($rid > 0 && $cid > 0) {
                $pdo->prepare('UPDATE vt_requests SET client_deleted = 1 WHERE id = :id AND client_id = :c')
                    ->execute([':id' => $rid, ':c' => $cid]);
                flash('success', 'Request removed from your list.');
            }
            redirect(portal_url('request-vt'));
        }

        $vtId = (int) ($_POST['vt_id'] ?? 0);
        $vtRow = null;
        if ($vtId > 0) {
            $st = $pdo->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = :id AND role = 'vt_onpool' AND active = 1");
            $st->execute([':id' => $vtId]);
            $vtRow = $st->fetch() ?: null;
        }
        if (!$vtRow) {
            flash('error', 'That teammate is no longer available.');
            redirect(portal_url('request-vt'));
        }
        $vtName   = trim(($vtRow['first_name'] ?? '') . ' ' . ($vtRow['last_name'] ?? '')) ?: ($vtRow['email'] ?? 'a teammate');
        $clientNm = $client['company_name'] ?? user_display_name($u);

        // Already an open request for this VT? Don't duplicate.
        $dup = $pdo->prepare("SELECT COUNT(*) FROM vt_requests WHERE client_id = :c AND vt_user_id = :v AND status = 'pending'");
        $dup->execute([':c' => $cid, ':v' => $vtId]);
        if ((int) $dup->fetchColumn() > 0) {
            flash('error', 'You already have a pending request for ' . $vtName . '.');
            redirect(portal_url('request-vt'));
        }

        $pdo->prepare('INSERT INTO vt_requests (client_id, vt_user_id, requested_by) VALUES (:c, :v, :b)')
            ->execute([':c' => $cid, ':v' => $vtId, ':b' => (int) $u['id']]);

        // Notify the client's CSM(s) + all active super admins.
        $recips = [];
        if ($cid > 0) {
            foreach ($pdo->query("SELECT DISTINCT csm_user_id FROM csm_clients WHERE client_id = " . $cid) as $r) {
                $recips[(int) $r['csm_user_id']] = true;
            }
        }
        foreach ($pdo->query("SELECT id FROM users WHERE role = 'super_admin' AND active = 1") as $r) {
            $recips[(int) $r['id']] = true;
        }
        foreach (array_keys($recips) as $rid) {
            if ($rid > 0) {
                notify($rid, 'vt_request', 'New VT request from ' . $clientNm,
                    $clientNm . ' requested an additional Virtual Teammate: ' . $vtName . '. Review it on My Clients & VTs.',
                    'index.php?p=my-clients');
            }
        }
        flash('success', 'Request sent! Your Client Success Manager will review it and follow up about ' . $vtName . '.');
        redirect(portal_url('request-vt'));
    }

    // This client's own requests (newest first) with VT name + decision note.
    $reqStmt = $pdo->prepare(
        "SELECT r.*, u.first_name, u.last_name, p.role_title, p.department
           FROM vt_requests r
           JOIN users u ON u.id = r.vt_user_id
           LEFT JOIN vt_profiles p ON p.user_id = r.vt_user_id
          WHERE r.client_id = :c AND r.client_deleted = 0
          ORDER BY r.id DESC"
    );
    $reqStmt->execute([':c' => $cid]);
    $myRequests = $reqStmt->fetchAll();

    // Current team (size + departments) to prioritise relevant suggestions.
    $teamCount = 0; $teamDepts = [];
    if ($cid > 0) {
        $teamCount = (int) $pdo->query("SELECT COUNT(*) FROM client_vts WHERE client_id = {$cid} AND contract_status = 'active'")->fetchColumn();
        foreach ($pdo->query("SELECT DISTINCT LOWER(TRIM(p.department)) d FROM client_vts cv JOIN vt_profiles p ON p.user_id = cv.vt_user_id WHERE cv.client_id = {$cid}") as $r) {
            if ($r['d'] !== '') { $teamDepts[$r['d']] = true; }
        }
    }

    // Available pool VTs the client doesn't already have.
    $excl = $cid > 0 ? "AND u.id NOT IN (SELECT vt_user_id FROM client_vts WHERE client_id = {$cid})" : '';
    $pool = $pdo->query(
        "SELECT u.id, u.first_name, u.last_name, u.country, u.photo_url,
                p.role_title, p.department, p.summary, p.experience_years,
                p.predictive_index, p.quiz_tier, p.engagement_score
         FROM users u JOIN vt_profiles p ON p.user_id = u.id
         WHERE u.role = 'vt_onpool' AND u.active = 1 {$excl}
         ORDER BY u.first_name, u.last_name"
    )->fetchAll();

    // Department-matched suggestions float to the top.
    usort($pool, static function ($a, $b) use ($teamDepts) {
        $ma = isset($teamDepts[strtolower(trim((string) $a['department']))]) ? 0 : 1;
        $mb = isset($teamDepts[strtolower(trim((string) $b['department']))]) ? 0 : 1;
        return $ma <=> $mb;
    });
    $pool = array_slice($pool, 0, 12);

    render('request-vt', [
        'user'       => $u,
        'pool'       => $pool,
        'team_count' => $teamCount,
        'requests'   => $myRequests,
    ]);
}

/** CSM/super-admin: approve or reject a client's VT request, with a note. */
function handle_request_decide(): void
{
    $u = require_login();
    $role = $u['role'] ?? '';
    if ($role !== 'csm' && $role !== 'super_admin') {
        http_response_code(403);
        render('error', ['title' => 'Forbidden', 'message' => 'Only a CSM or admin can decide requests.']);
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(portal_url('my-clients')); }
    csrf_verify();
    $pdo = db();
    vt_requests_ensure($pdo);

    $reqId  = (int) ($_POST['request_id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    $note   = trim((string) ($_POST['note'] ?? ''));
    $back   = $role === 'csm' ? portal_url('my-clients') : portal_url('leads');
    if ($reqId <= 0 || !in_array($action, ['approve', 'reject', 'delete'], true)) { redirect($back); }

    // Load the request + ensure this CSM manages the client (admins bypass).
    $st = $pdo->prepare(
        "SELECT r.*, c.company_name, c.id AS cid
           FROM vt_requests r JOIN clients c ON c.id = r.client_id
          WHERE r.id = :id"
    );
    $st->execute([':id' => $reqId]);
    $req = $st->fetch();
    if (!$req) { flash('error', 'Request not found.'); redirect($back); }

    if ($role === 'csm') {
        $own = $pdo->prepare('SELECT 1 FROM csm_clients WHERE csm_user_id = :u AND client_id = :c LIMIT 1');
        $own->execute([':u' => (int) $u['id'], ':c' => (int) $req['client_id']]);
        if (!$own->fetchColumn()) { flash('error', 'That client is not assigned to you.'); redirect($back); }
    }

    // Dismiss from the CSM/admin view only — the client keeps their own copy.
    // Allowed at any status (pending requests can also be cleared).
    if ($action === 'delete') {
        $pdo->prepare('UPDATE vt_requests SET csm_deleted = 1 WHERE id = :id')->execute([':id' => $reqId]);
        flash('success', 'Request removed from your list.');
        redirect($back);
    }

    if ($req['status'] !== 'pending') { flash('error', 'That request was already decided.'); redirect($back); }
    $status = $action === 'approve' ? 'approved' : 'rejected';

    $pdo->prepare('UPDATE vt_requests SET status = :s, csm_note = :n, decided_by = :d, decided_at = CURRENT_TIMESTAMP WHERE id = :id')
        ->execute([':s' => $status, ':n' => mb_substr($note, 0, 1000), ':d' => (int) $u['id'], ':id' => $reqId]);

    // Tell the client who requested it.
    $vt = $pdo->prepare('SELECT first_name, last_name, email FROM users WHERE id = :id');
    $vt->execute([':id' => (int) $req['vt_user_id']]);
    $vtr = $vt->fetch() ?: [];
    $vtName = trim(($vtr['first_name'] ?? '') . ' ' . ($vtr['last_name'] ?? '')) ?: ($vtr['email'] ?? 'the teammate');
    $verb   = $status === 'approved' ? 'approved' : 'declined';
    notify(
        (int) $req['requested_by'],
        'vt_request',
        'Your VT request was ' . $verb,
        'Your request for ' . $vtName . ' was ' . $verb . '.' . ($note !== '' ? ' Note: ' . $note : ''),
        'index.php?p=request-vt'
    );
    flash('success', 'Request ' . $verb . '. The client has been notified.');
    redirect($back);
}

/* ═════════════════════════════════════════════════════════════════════════
 * SPECIAL LINKS — CSM-generated, expiring, no-login links that open a single
 * VT profile (mirrors the staging WP [special_link_generator]). Tokens are
 * validated by the public vt-link.php viewer.
 * ═════════════════════════════════════════════════════════════════════════ */
function special_links_ensure(PDO $pdo): void
{
    static $done = false;
    if ($done) { return; }
    $done = true;
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS vt_special_links (
            token       TEXT    PRIMARY KEY,
            vt_user_id  INTEGER NOT NULL,
            created_by  INTEGER NOT NULL,
            created_at  TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at  INTEGER NOT NULL,
            revoked     INTEGER NOT NULL DEFAULT 0
        )"
    );
}

function handle_special_links(): void
{
    $u    = require_login();
    $role = $u['role'] ?? '';
    if ($role !== 'csm' && $role !== 'super_admin') {
        render('error', ['title' => 'Forbidden', 'message' => 'Special Links is a CSM tool.']);
        return;
    }
    $pdo = db();
    special_links_ensure($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'generate') {
            $vtId  = (int) ($_POST['vt_id'] ?? 0);
            $hours = (int) ($_POST['hours'] ?? 72);
            if (!in_array($hours, [24, 72, 168, 720], true)) { $hours = 72; }
            $chk = $pdo->prepare("SELECT 1 FROM users WHERE id = :id AND role = 'vt_onpool' AND active = 1");
            $chk->execute([':id' => $vtId]);
            if (!$chk->fetchColumn()) {
                flash('error', 'Pick an available teammate to generate a link.');
                redirect(portal_url('special-links'));
            }
            $token = bin2hex(random_bytes(16));
            $pdo->prepare('INSERT INTO vt_special_links (token, vt_user_id, created_by, expires_at) VALUES (:t,:v,:b,:e)')
                ->execute([':t' => $token, ':v' => $vtId, ':b' => (int) $u['id'], ':e' => time() + $hours * 3600]);
            flash('success', 'Special link generated — copy it below. It expires in ' . ($hours >= 168 ? ($hours / 168) . ' week(s)' : ($hours >= 24 ? ($hours / 24) . ' day(s)' : $hours . 'h')) . '.');
            redirect(portal_url('special-links'));
        }
        if ($action === 'revoke') {
            $token = (string) ($_POST['token'] ?? '');
            $sql   = 'UPDATE vt_special_links SET revoked = 1 WHERE token = :t';
            $params = [':t' => $token];
            if ($role !== 'super_admin') { $sql .= ' AND created_by = :b'; $params[':b'] = (int) $u['id']; }
            $pdo->prepare($sql)->execute($params);
            flash('success', 'Link revoked.');
            redirect(portal_url('special-links'));
        }
        redirect(portal_url('special-links'));
    }

    // Available pool VTs for the picker.
    $pool = $pdo->query(
        "SELECT u.id, u.first_name, u.last_name, u.photo_url, p.role_title, p.department
         FROM users u JOIN vt_profiles p ON p.user_id = u.id
         WHERE u.role = 'vt_onpool' AND u.active = 1
         ORDER BY u.first_name, u.last_name"
    )->fetchAll();

    // This CSM's links (admins see all), newest first.
    $sql = "SELECT l.*, u.first_name, u.last_name, p.role_title
            FROM vt_special_links l
            JOIN users u ON u.id = l.vt_user_id
            LEFT JOIN vt_profiles p ON p.user_id = l.vt_user_id";
    $params = [];
    if ($role !== 'super_admin') { $sql .= ' WHERE l.created_by = :b'; $params[':b'] = (int) $u['id']; }
    $sql .= ' ORDER BY l.created_at DESC LIMIT 100';
    $st = $pdo->prepare($sql);
    $st->execute($params);

    render('special-links', [
        'user'     => $u,
        'pool'     => $pool,
        'links'    => $st->fetchAll(),
        'base_url' => site_url('vt-link.php'),
        'now'      => time(),
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
