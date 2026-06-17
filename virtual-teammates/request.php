<?php
/**
 * Public talent-directory → "Request this Virtual Teammate" endpoint (AJAX).
 *
 * A logged-in CLIENT browsing /virtual-teammates/ can request a pool VT straight
 * from the profile modal. This creates the SAME vt_requests row the portal's
 * "Request an Additional VT" page does, and notifies the client's CSM(s) + super
 * admins — so it flows through the identical approve/reject workflow.
 *
 * Returns JSON. Requires an active client portal session + matching CSRF token.
 */
declare(strict_types=1);

require __DIR__ . '/../portal/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

$out = static function (array $payload, int $code = 200): never {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
};

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $out(['ok' => false, 'error' => 'Method not allowed.'], 405);
}

$u = current_user();
if (!$u || ($u['role'] ?? '') !== 'client') {
    $out(['ok' => false, 'error' => 'Please sign in to your client portal to request a teammate.'], 403);
}

// CSRF — token shares the vtportal session set on the directory page.
$supplied = $_POST['_csrf'] ?? '';
if (!is_string($supplied) || !hash_equals(csrf_token(), $supplied)) {
    $out(['ok' => false, 'error' => 'Your session expired, reload the page and try again.'], 400);
}

$vtId = (int) ($_POST['vt_id'] ?? 0);
if ($vtId < 1) {
    $out(['ok' => false, 'error' => 'No teammate specified.'], 422);
}

$pdo = db();
vt_requests_ensure($pdo);

// Resolve this client's account.
$cs = $pdo->prepare('SELECT id, company_name FROM clients WHERE user_id = :uid LIMIT 1');
$cs->execute([':uid' => (int) $u['id']]);
$client = $cs->fetch();
$cid    = (int) ($client['id'] ?? 0);
if ($cid < 1) {
    $out(['ok' => false, 'error' => 'No client account is linked to your login.'], 403);
}

// Validate the teammate is on the available pool.
$vs = $pdo->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = :id AND role = 'vt_onpool' AND active = 1");
$vs->execute([':id' => $vtId]);
$vt = $vs->fetch();
if (!$vt) {
    $out(['ok' => false, 'error' => 'That teammate is no longer available to request.'], 409);
}

$vtName   = trim(($vt['first_name'] ?? '') . ' ' . ($vt['last_name'] ?? '')) ?: ($vt['email'] ?? 'a teammate');
$clientNm = (string) ($client['company_name'] ?? '') ?: user_display_name($u);

// Already on this client's team (current or previous)? Then there's nothing to request.
$on = $pdo->prepare('SELECT 1 FROM client_vts WHERE client_id = :c AND vt_user_id = :v LIMIT 1');
$on->execute([':c' => $cid, ':v' => $vtId]);
if ($on->fetchColumn()) {
    $out(['ok' => false, 'error' => $vtName . ' is already on your team.'], 409);
}

// Don't duplicate an open request.
$dup = $pdo->prepare("SELECT COUNT(*) FROM vt_requests WHERE client_id = :c AND vt_user_id = :v AND status = 'pending'");
$dup->execute([':c' => $cid, ':v' => $vtId]);
if ((int) $dup->fetchColumn() > 0) {
    $out(['ok' => true, 'already' => true, 'message' => 'You already have a pending request for ' . $vtName . '.']);
}

$pdo->prepare('INSERT INTO vt_requests (client_id, vt_user_id, requested_by) VALUES (:c, :v, :b)')
    ->execute([':c' => $cid, ':v' => $vtId, ':b' => (int) $u['id']]);

// Notify the client's CSM(s) + all active super admins (same as the portal flow).
$recips = [];
foreach ($pdo->query('SELECT DISTINCT csm_user_id FROM csm_clients WHERE client_id = ' . $cid) as $r) {
    $recips[(int) $r['csm_user_id']] = true;
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

$out(['ok' => true, 'message' => 'Request sent! Your Client Success Manager will review it and follow up about ' . $vtName . '.']);
