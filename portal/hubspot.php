<?php
/**
 * HubSpot sync — state-machine version, modeled on the WP plugin's batched
 * sync (vt-hubspot-user-sync: `talent_sync_*` + `client_sync_*` step funcs).
 *
 * One combined run covers VTs + clients + CSMs. Each stage executes in small
 * batches so the request never times out. The browser polls `?p=hubspot.step`
 * until the state machine reports `done`. Pause / Resume / Reset are just
 * `status` flips persisted in `app_settings`.
 *
 * Algorithm parity with the WP plugin:
 *   - search: POST /crm/v3/objects/{contacts|companies}/search with a single
 *             EQ filter (e.g. hs_lead_status = "Virtual Teammate"), paginated
 *             via `paging.next.after`. (`search_virtual_teammate_contacts`,
 *             `search_client_companies`.)
 *   - upsert: by hubspot_contact_id / hubspot_company_id first, then by
 *             lowercased email. (`sync_contact_to_user`,
 *             `sync_company_to_client_user`.)
 *   - role map: vt_status → vt_hired / vt_onpool, "no longer eligible"
 *               triggers delete. (`map_role_from_vt_status`.)
 *   - media: download into a tagged directory, prefer originals.
 *            (`maybe_import_media`, `filter_upload_dir_to_vtmedia`.)
 */

declare(strict_types=1);

const HS_API_BASE        = 'https://api.hubapi.com';
const HS_STATE_KEY       = 'hs_sync_state';
const HS_MEDIA_ROOT      = __DIR__ . '/../data/media';   // web-DENIED (resumes/videos, gated)
const HS_VTMEDIA_ROOT    = __DIR__ . '/../vtmedia';      // web-ACCESSIBLE (photos, public)
const HS_SEARCH_PAGE_MAX = 100;   // HubSpot cap.
const HS_PROCESS_BATCH   = 20;    // Contacts per process step (raised from 5 per user request).
// Media downloads run concurrently (curl_multi) so a single tick pulls several
// files at once instead of one-at-a-time. Per-item state is checkpointed after
// each file, so a tick killed mid-batch resumes cleanly on the next item.
const HS_MEDIA_BATCH           = 8;     // items peeked + drained per tick
const HS_MEDIA_PARALLEL        = 4;     // concurrent downloads in flight
const HS_MEDIA_TIMEOUT         = 150;   // hard per-file ceiling (seconds)
const HS_MEDIA_CONNECT_TIMEOUT = 10;    // connect-phase ceiling (seconds)
const HS_MEDIA_LOW_SPEED_BYTES = 1024;  // abort a transfer slower than 1KB/s…
const HS_MEDIA_LOW_SPEED_SECS  = 30;    // …sustained this long (kills stalls)
const HS_MEDIA_MAX_ATTEMPTS    = 3;     // skip a file that keeps dying mid-import

/**
 * Resolve a CA bundle path so cURL HTTPS works on WAMP / shared hosts where
 * curl.cainfo isn't configured. Returns '' if cURL should use its built-in
 * defaults (Linux production usually has /etc/ssl/certs configured).
 */
function hs_ca_bundle(): string
{
    static $cached = null;
    if ($cached !== null) { return $cached; }
    foreach ([
        getenv('CURL_CA_BUNDLE') ?: '',
        (string) ini_get('curl.cainfo'),
        (string) ini_get('openssl.cafile'),
        __DIR__ . '/cacert.pem',                         // shipped with the portal
        'c:/wamp64/bin/php/php8.2.26/extras/ssl/cacert.pem',
        'c:/wamp64/bin/apache/apache2.4.58/bin/curl-ca-bundle.crt',
        '/etc/ssl/certs/ca-certificates.crt',
    ] as $candidate) {
        if ($candidate !== '' && is_file($candidate)) { return $cached = $candidate; }
    }
    return $cached = '';
}

function hs_apply_curl_ssl($ch): void
{
    $ca = hs_ca_bundle();
    if ($ca !== '') {
        curl_setopt($ch, CURLOPT_CAINFO, $ca);
    }
}

/**
 * Lift PHP runtime ceilings for the long-running, network-heavy sync so a large
 * media batch can't be killed mid-flight by max_execution_time, memory_limit,
 * or socket timeouts. Mirrors the staging WordPress bootstrap (wp-config.php)
 * values. Every call is @-suppressed and is a safe no-op on hosts that forbid
 * ini_set / set_time_limit.
 */
function hs_raise_runtime_limits(): void
{
    @set_time_limit(0);
    @ini_set('max_execution_time', '3360000');
    @ini_set('max_input_time', '0');
    @ini_set('memory_limit', '2048M');
    @ini_set('upload_max_filesize', '1024M');
    @ini_set('post_max_size', '1024M');
    @ini_set('max_input_vars', '50000');
    @ini_set('max_file_uploads', '2000');
    @ini_set('default_socket_timeout', '6000');
    @ignore_user_abort(true);
}

/* ═════════════════════════════════════════════════════════════════════════
 * Settings
 * ═════════════════════════════════════════════════════════════════════════ */

function hs_defaults(): array
{
    return [
        'hs_token'                    => '',
        'hs_vt_lead_status_field'     => 'hs_lead_status',
        'hs_vt_lead_status_value'     => 'Virtual Teammate',
        'hs_vt_status_field'          => 'vt_status',
        'hs_client_lead_status_field' => 'hs_lead_status',
        'hs_client_lead_status_value' => 'Client - Active',
        'hs_csm_lead_status_field'    => 'hs_lead_status',
        'hs_csm_lead_status_value'    => 'CSM',
        'hs_batch_size'               => '100',
        'hs_import_media'             => '1',
    ];
}

function hs_settings(): array
{
    $out = hs_defaults();
    foreach (array_keys($out) as $k) { $out[$k] = get_setting($k, $out[$k]); }
    return $out;
}

/* ═════════════════════════════════════════════════════════════════════════
 * HubSpotClient — minimal cURL wrapper. Parity with hubspot_request().
 * ═════════════════════════════════════════════════════════════════════════ */

final class HubSpotClient
{
    public function __construct(
        private readonly string $token,
        private readonly int    $timeoutSec = 30,
    ) {}

    /**
     * @return array{ok:bool,status:int,data:array,error:?string}
     */
    public function request(string $method, string $path, ?array $body = null): array
    {
        if ($this->token === '') {
            return ['ok'=>false, 'status'=>0, 'data'=>[], 'error'=>'No HubSpot token configured.'];
        }
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => HS_API_BASE . $path,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeoutSec,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        hs_apply_curl_ssl($ch);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_SLASHES));
        }
        $raw   = curl_exec($ch);
        $errno = curl_errno($ch);
        $err   = curl_error($ch);
        $code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0) {
            return ['ok'=>false, 'status'=>0, 'data'=>[], 'error'=>"cURL error: {$err}"];
        }
        $data = json_decode((string) $raw, true);
        if (!is_array($data)) { $data = []; }
        if ($code < 200 || $code >= 300) {
            $msg = $data['message'] ?? ('HTTP ' . $code);
            return ['ok'=>false, 'status'=>$code, 'data'=>$data, 'error'=>$msg];
        }
        return ['ok'=>true, 'status'=>$code, 'data'=>$data, 'error'=>null];
    }

    /** Single-page search; returns the raw HubSpot response. */
    public function searchPage(string $object, array $filter, array $properties, int $limit = HS_SEARCH_PAGE_MAX, ?string $after = null): array
    {
        $payload = [
            'filterGroups' => [[ 'filters' => [$filter] ]],
            'properties'   => array_values(array_unique($properties)),
            'limit'        => max(1, min(HS_SEARCH_PAGE_MAX, $limit)),
        ];
        if ($after !== null && $after !== '') {
            $payload['after'] = $after;
        }
        $resp = $this->request('POST', '/crm/v3/objects/' . rawurlencode($object) . '/search', $payload);
        if (!$resp['ok']) {
            throw new RuntimeException($resp['error'] ?: 'HubSpot search failed.');
        }
        return $resp['data'];
    }
}

/* ═════════════════════════════════════════════════════════════════════════
 * Property name lists (mirrors WP plugin's minimum_properties).
 * ═════════════════════════════════════════════════════════════════════════ */

function hs_vt_properties(): array
{
    return [
        'email','firstname','lastname','phone','mobilephone','jobtitle','country',
        'hs_lead_status','vt_status',
        'department','vt_department','primary_role','primary_roles','role','position',
        'english_proficiency','english_level',
        'years_of_experience','years_of_experience__vt_','experience_years',
        'summary','vt_skills_summary','vt_experience_summary','professional_summary',
        'vt_profile_picture_link','profile_picture','profile_picture_url','vt_profile_picture_url','headshot','headshot_url','photo','photo_url',
        'resume','resume_url','resume_link','cv','cv_url',
        'intro_video_link','intro_video','intro_video_url','introduction_video','video_url',
        'workday_tracker_id','workday_report_id','workdaytracker_report_id','workday_tracker_report_id',
        'iq_test_score__max_10_','iq_level','technical_proficiency__max_5_','technical_skills_level',
    ];
}

function hs_company_properties(): array
{
    return ['name','domain','website','hs_contact_email','company_email','billing_email','hs_lead_status','hubspot_owner_id'];
}

function hs_csm_properties(): array
{
    return ['email','firstname','lastname','phone','mobilephone','hs_lead_status','jobtitle','country'];
}

/** Pick the first non-empty value from a property bag. */
function hs_pick(array $props, array $candidates, string $default = ''): string
{
    foreach ($candidates as $k) {
        if (isset($props[$k]) && trim((string) $props[$k]) !== '') {
            return trim((string) $props[$k]);
        }
    }
    return $default;
}

function hs_map_vt_role(string $vtStatus, string $leadStatusFallback = ''): string
{
    $n = strtolower(trim($vtStatus));
    if (in_array($n, ['hired', 'contracted'], true))             return 'vt_hired';
    if (in_array($n, ['unmatched / eligible', 'matched'], true)) return 'vt_onpool';
    if ($leadStatusFallback !== '')                               return 'vt_onpool';
    return '';
}

/**
 * Default password assigned when the sync creates a new user. Matches the
 * convention the marketing-site admin asked for so freshly synced accounts
 * can log into the portal immediately. Only applied on INSERT — existing
 * users keep whatever password they already have.
 */
function hs_default_password(string $role): string
{
    return match ($role) {
        'client'    => 'client12345',
        'csm'       => 'csm12345',
        'vt_hired'  => 'vthired12345',
        'vt_onpool' => 'vtonpool12345',
        default     => bin2hex(random_bytes(16)),
    };
}

function hs_find_user_for_contact(string $contactId, string $email): ?array
{
    $pdo = db();
    if ($contactId !== '') {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE hubspot_contact_id = :c LIMIT 1');
        $stmt->execute([':c' => $contactId]);
        if ($u = $stmt->fetch()) { return $u; }
    }
    if ($email !== '') {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e' => $email]);
        if ($u = $stmt->fetch()) { return $u; }
    }
    return null;
}

/* ═════════════════════════════════════════════════════════════════════════
 * MEDIA IMPORT — high-quality, original-bytes downloads with tagging.
 *
 * - Strips Google Drive / googleusercontent size qualifiers (=sXXX, =w200-h200)
 *   so we always pull the original.
 * - Strips common CDN resize query params (width, height, size, resize).
 * - Recognizes hosted-video providers (YouTube, Vimeo, Loom, Wistia) and
 *   stores the URL as-is rather than trying to download an embed page.
 * - Saves raw bytes; never re-encodes.
 * ═════════════════════════════════════════════════════════════════════════ */

function hs_media_kind_map(): array
{
    return [
        'photo'  => ['jpg','jpeg','png','gif','webp'],
        'resume' => ['pdf','doc','docx'],
        'video'  => ['mp4','mov','m4v','webm'],
    ];
}

function hs_media_dir(string $entity, int $id, string $kind = ''): string
{
    // Photos live in the web-accessible /vtmedia/ tree (served directly, public).
    // Resumes + videos stay under data/media (web-denied, gated portal endpoint).
    $root = ($kind === 'photo') ? HS_VTMEDIA_ROOT : HS_MEDIA_ROOT;
    $dir  = $root . '/' . $entity . '/' . $id;
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
    return $dir;
}

/**
 * The URL stored in the DB for a downloaded media file.
 * - photo  → direct, root-relative web path under /vtmedia/ (public).
 * - resume/video → the auth-gated portal endpoint (data/ is web-denied).
 */
function hs_media_served_url(string $entity, int $id, string $kind, string $ext): string
{
    if ($kind === 'photo') {
        return 'vtmedia/' . $entity . '/' . $id . '/photo.' . $ext;
    }
    return 'index.php?p=media&e=' . urlencode($entity) . '&id=' . $id . '&k=' . urlencode($kind);
}

/** True if a stored media URL points at an already-downloaded local file. */
function hs_media_url_is_local(string $url): bool
{
    return $url !== '' && (str_starts_with($url, 'index.php?p=media') || str_starts_with($url, 'vtmedia/'));
}

/** Root-relative web path of a VT's square thumbnail (vtmedia/vt_thumbs/<id>.<ext>). */
function hs_thumb_rel(int $id, string $ext): string
{
    return 'vtmedia/vt_thumbs/' . $id . '.' . strtolower($ext);
}

/**
 * Generate a 150x150 square (center-cropped) thumbnail of a downloaded profile
 * photo into vtmedia/vt_thumbs/<id>.<ext>. The full-size original is left intact.
 * Returns the thumb's root-relative path, or '' if GD/format unsupported.
 */
function hs_make_thumb(string $srcFile, int $id): string
{
    if (!is_file($srcFile) || !function_exists('imagecreatetruecolor')) { return ''; }
    $ext = strtolower(pathinfo($srcFile, PATHINFO_EXTENSION));
    $loaders = [
        'jpg' => 'imagecreatefromjpeg', 'jpeg' => 'imagecreatefromjpeg',
        'png' => 'imagecreatefrompng',  'gif'  => 'imagecreatefromgif',
        'webp'=> 'imagecreatefromwebp',
    ];
    if (!isset($loaders[$ext]) || !function_exists($loaders[$ext])) { return ''; }
    $src = @$loaders[$ext]($srcFile);
    if (!$src) { return ''; }

    $sw = imagesx($src); $sh = imagesy($src);
    $side = max(1, min($sw, $sh));
    $sx = (int) (($sw - $side) / 2);
    $sy = (int) (($sh - $side) / 2);
    $T = 150;
    $dst = imagecreatetruecolor($T, $T);
    if (in_array($ext, ['png', 'gif', 'webp'], true)) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagefilledrectangle($dst, 0, 0, $T, $T, imagecolorallocatealpha($dst, 0, 0, 0, 127));
    }
    imagecopyresampled($dst, $src, 0, 0, $sx, $sy, $T, $T, $side, $side);

    $dir = HS_VTMEDIA_ROOT . '/vt_thumbs';
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
    $out = $dir . '/' . $id . '.' . $ext;
    // Drop any stale thumb for this id in a different extension.
    foreach (glob($dir . '/' . $id . '.*') ?: [] as $old) { if ($old !== $out) { @unlink($old); } }
    $ok = match ($ext) {
        'png'  => imagepng($dst, $out, 6),
        'gif'  => imagegif($dst, $out),
        'webp' => function_exists('imagewebp') ? imagewebp($dst, $out, 82) : false,
        default=> imagejpeg($dst, $out, 82),
    };
    imagedestroy($src);
    imagedestroy($dst);
    return $ok ? hs_thumb_rel($id, $ext) : '';
}

/** Returns true for embed-only video hosts we can't download as raw files. */
function hs_is_embedded_video_url(string $url): bool
{
    return (bool) preg_match('#(youtube\.com|youtu\.be|vimeo\.com|loom\.com|wistia\.com|wistia\.net|fast\.wistia)#i', $url);
}

/**
 * Normalize a media URL so the download fetches the highest-quality original.
 * No-op for URLs we don't recognize.
 */
function hs_normalize_media_url(string $url, string $kind): string
{
    $url = trim($url);
    if ($url === '') { return $url; }

    // 0a. Google Drive viewer URL → direct-download. Mirrors staging's logic
    //     (vt-hubspot-user-sync.php lines 6241-6248): if we can pull a Drive
    //     file ID out of any of the supported URL forms, swap to `uc?export=download`.
    //     Patterns: /file/d/{ID}/view, /open?id={ID}, ?id={ID}, &id={ID}.
    //     Folder URLs (/drive/.../folders/{ID}) are NOT fetchable — Drive serves
    //     a folder-listing HTML page, never a file body. Skip them so the caller
    //     reports a clean error instead of "not a valid PDF/image".
    if (preg_match('#drive\.google\.com#i', $url)) {
        if (preg_match('#drive\.google\.com/(?:drive/)?(?:[a-z0-9/]+/)?folders/#i', $url)) {
            return '';
        }
        $fileId = '';
        foreach ([
            '#drive\.google\.com/file/d/([A-Za-z0-9_\-]+)#i',
            '#drive\.google\.com/open\?id=([A-Za-z0-9_\-]+)#i',
            '#[?&]id=([A-Za-z0-9_\-]+)#i',
        ] as $rx) {
            if (preg_match($rx, $url, $m)) { $fileId = $m[1]; break; }
        }
        if ($fileId !== '') {
            // confirm=t bypasses Drive's "can't scan for viruses" interstitial
            // HTML page that it serves for larger files (a common resume failure).
            return 'https://drive.google.com/uc?export=download&confirm=t&id=' . rawurlencode($fileId);
        }
    }
    // 0b. Google Docs document → export as PDF for resume, plain link otherwise.
    if (preg_match('#docs\.google\.com/document/d/([A-Za-z0-9_\-]+)#i', $url, $m)) {
        $docId = $m[1];
        if ($kind === 'resume') {
            return 'https://docs.google.com/document/d/' . rawurlencode($docId) . '/export?format=pdf';
        }
    }

    // 1. Google user content (lh3.googleusercontent.com, photos.app.goog, drive)
    //    serves thumbnails when a "=sXXX" / "=w200-h200" qualifier follows the
    //    base URL. Strip everything from the first "=" in the *final* path segment.
    if (preg_match('#googleusercontent\.com|googleapis\.com/drive#i', $url)) {
        $url = preg_replace('#=([wh]\d+|s\d+|s\d+-c|w\d+-h\d+|w\d+|h\d+)(?:-[a-z0-9\-]+)?$#i', '', $url);
        // Replace the qualifier even when it sits mid-URL just before a query.
        $url = preg_replace('#=([wh]\d+|s\d+|s\d+-c|w\d+-h\d+|w\d+|h\d+)(?:-[a-z0-9\-]+)?(\?|#|$)#i', '$2', $url);
    }

    // 2. Drop generic "resize-style" query params that downscale on the CDN.
    $parts = parse_url($url);
    if (is_array($parts) && !empty($parts['query'])) {
        parse_str($parts['query'], $q);
        foreach (['resize','width','height','w','h','size','quality','format','fit','crop'] as $bad) {
            unset($q[$bad]);
        }
        $parts['query'] = http_build_query($q);
        $url = (isset($parts['scheme']) ? $parts['scheme'] . '://' : '')
             . ($parts['host'] ?? '')
             . (isset($parts['port']) ? ':' . $parts['port'] : '')
             . ($parts['path'] ?? '')
             . ($parts['query'] !== '' ? '?' . $parts['query'] : '')
             . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
    }

    return $url;
}

/**
 * Download $url into data/media/{entity}/{id}/{kind}.{ext}.
 *
 * - For embedded video hosts we don't download; we return the original URL so
 *   it can be embedded later (vt_profile.video_url just stores the link).
 * - Returns the portal-served URL (`index.php?p=media&...`) for downloaded
 *   files, the original URL for embed-only videos, or '' on any failure.
 */
function hs_import_media(string $url, string $entity, int $id, string $kind, string $currentSourceUrl = '', bool &$skipped = false): string
{
    $skipped = false;
    if (preg_match('#drive\.google\.com/(?:drive/)?(?:[a-z0-9/]+/)?folders/#i', trim($url))) {
        return ''; // Drive folder URLs are not fetchable — caller should treat as no-op.
    }
    $url = hs_normalize_media_url($url, $kind);
    if ($url === '' || !preg_match('#^https?://#i', $url)) { return ''; }
    if (!isset(hs_media_kind_map()[$kind])) { return ''; }

    if ($kind === 'video' && hs_is_embedded_video_url($url)) {
        // Keep the embed URL intact — we don't re-encode YouTube / Vimeo / Loom.
        return $url;
    }

    // Re-sync cache: if the source URL matches the one we previously downloaded
    // AND the local file is still on disk, skip the network round-trip.
    if ($currentSourceUrl !== '' && $currentSourceUrl === $url) {
        $dir   = hs_media_dir($entity, $id, $kind);
        $exist = glob($dir . '/' . $kind . '.*');
        if (!empty($exist) && is_file($exist[0])) {
            $skipped = true;
            return hs_media_served_url($entity, $id, $kind, strtolower(pathinfo($exist[0], PATHINFO_EXTENSION)));
        }
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 VT-Portal-Sync/1.0',
        CURLOPT_HEADER         => false,
    ]);
    hs_apply_curl_ssl($ch);
    $body  = curl_exec($ch);
    $code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ctype = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $errno = curl_errno($ch);
    curl_close($ch);

    if ($errno !== 0 || $code < 200 || $code >= 300 || !is_string($body) || $body === '') {
        return '';
    }

    // Prefer the URL path extension (preserves original format), fall back to Content-Type.
    $pathExt = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));
    $ctExt   = strtolower(preg_replace('#[^a-z0-9]#', '', explode('/', $ctype)[1] ?? ''));
    if ($ctExt === 'jpeg') { $ctExt = 'jpg'; }
    $ext = $pathExt !== '' ? $pathExt : $ctExt;

    $allowed = hs_media_kind_map()[$kind];
    if (!in_array($ext, $allowed, true)) {
        $ext = $allowed[0];
    }

    $dir  = hs_media_dir($entity, $id, $kind);
    $file = $dir . '/' . $kind . '.' . $ext;

    if (file_put_contents($file, $body) === false) { return ''; }

    // Remove stale artifacts in a different extension for the same kind.
    foreach (glob($dir . '/' . $kind . '.*') as $existing) {
        if ($existing !== $file) { @unlink($existing); }
    }
    // Keep the full-size original AND generate a 150x150 thumbnail for lists/cards.
    if ($kind === 'photo') { hs_make_thumb($file, $id); }
    return hs_media_served_url($entity, $id, $kind, $ext);
}

/* ═════════════════════════════════════════════════════════════════════════
 * State machine
 *
 * State lives in app_settings.hs_sync_state as JSON. The dispatcher
 * `hs_step()` picks the current stage, calls its processor, and persists
 * the mutated state. `hs_control()` flips status/resets.
 * ═════════════════════════════════════════════════════════════════════════ */

function hs_state_default(): array
{
    return [
        'version'      => 1,
        'status'       => 'idle',  // idle | running | paused | done | error
        'stage'        => 'init',
        'stages'       => [
            'init', 'fetch_vts', 'process_vts',
            'fetch_clients', 'process_clients',
            'fetch_csms', 'process_csms',
            'link_associations',
            'done',
        ],
        'after_cursor'      => null,
        'pending'           => ['vts' => [], 'clients' => [], 'csms' => [], 'links' => []],
        'link_initialized'  => false,
        'started_at'        => null,
        'updated_at'        => null,
        'finished_at'       => null,
        'last_error'        => null,
        'stats' => [
            'vts'             => ['created'=>0,'updated'=>0,'skipped'=>0,'deleted'=>0,'errors'=>0],
            'clients'         => ['created'=>0,'updated'=>0,'skipped'=>0,'errors'=>0],
            'csms'            => ['created'=>0,'updated'=>0,'skipped'=>0,'errors'=>0],
            'relationships'   => ['vt_links'=>0,'csm_links'=>0,'owners_resolved'=>0,'errors'=>0],
            'media_downloads' => 0,
            'media_skipped'   => 0,
            'fetched_total'   => ['vts' => 0, 'clients' => 0, 'csms' => 0],
        ],
        'messages' => [],
    ];
}

function hs_state_load(): array
{
    $raw = get_setting(HS_STATE_KEY, '');
    if ($raw === '') { return hs_state_default(); }
    $s = json_decode($raw, true);
    if (!is_array($s) || ($s['version'] ?? 0) !== 1) { return hs_state_default(); }
    return array_replace_recursive(hs_state_default(), $s);
}

function hs_state_save(array $state): void
{
    $state['updated_at'] = date('c');
    set_setting(HS_STATE_KEY, json_encode($state, JSON_UNESCAPED_SLASHES));
}

function hs_state_log(array &$state, string $message): void
{
    $state['messages'][] = ['t' => date('H:i:s'), 'm' => $message];
    // Keep only the last 80 messages to keep the JSON small.
    if (count($state['messages']) > 80) {
        $state['messages'] = array_slice($state['messages'], -80);
    }
}

/**
 * Notify every active super_admin that a sync pipeline just finished, with
 * the headline stats so they can decide whether to dig in. Funnels through
 * notify() so the per-user email opt-in is honored.
 */
function hs_notify_sync_complete(string $pipeline, array $state): void
{
    $pipelineLbl = $pipeline === 'talent' ? 'Talent' : ($pipeline === 'client' ? 'Client' : ucfirst($pipeline));
    $stats = is_array($state['stats'] ?? null) ? $state['stats'] : [];

    // Build a short, human-readable summary line from the stats bucket
    // most relevant to the pipeline.
    $parts = [];
    if ($pipeline === 'talent') {
        $v = $stats['vts']   ?? [];
        $m = $stats['media'] ?? [];
        if (isset($v['created']))   { $parts[] = (int) $v['created']  . ' created'; }
        if (isset($v['updated']))   { $parts[] = (int) $v['updated']  . ' updated'; }
        $skip = (int) (($v['skipped_role'] ?? 0) + ($v['skipped_no_email'] ?? 0));
        if ($skip > 0)              { $parts[] = $skip . ' skipped'; }
        if (isset($m['downloaded'])){ $parts[] = (int) $m['downloaded'] . ' media downloaded'; }
        if (!empty($m['errors']))   { $parts[] = (int) $m['errors']   . ' errors'; }
    } else {
        $c = $stats['clients']       ?? [];
        $r = $stats['relationships'] ?? [];
        if (isset($c['created']))       { $parts[] = (int) $c['created']        . ' clients created'; }
        if (isset($c['updated']))       { $parts[] = (int) $c['updated']        . ' updated'; }
        if (isset($r['vt_links']))      { $parts[] = (int) $r['vt_links']       . ' VT links'; }
        if (isset($r['csm_links']))     { $parts[] = (int) $r['csm_links']      . ' CSM links'; }
        if (!empty($c['errors']))       { $parts[] = (int) $c['errors']         . ' errors'; }
    }
    $summary = $parts ? implode(' · ', $parts) : 'No record-level changes recorded.';

    $duration = '';
    if (!empty($state['started_at']) && !empty($state['finished_at'])) {
        $sec = max(0, strtotime($state['finished_at']) - strtotime($state['started_at']));
        if ($sec > 60) { $duration = floor($sec / 60) . 'm ' . ($sec % 60) . 's'; }
        else { $duration = $sec . 's'; }
    }

    $title = $pipelineLbl . ' sync finished';
    $body  = $summary . ($duration !== '' ? ' (ran ' . $duration . ')' : '');
    $link  = 'index.php?p=hubspot';

    try {
        foreach (db()->query("SELECT id FROM users WHERE role = 'super_admin' AND active = 1") as $row) {
            notify((int) $row['id'], 'sync', $title, $body, $link);
        }
    } catch (Throwable $_) {}
}

function hs_state_advance_stage(array &$state): void
{
    $stages = $state['stages'];
    $i = array_search($state['stage'], $stages, true);
    if ($i === false || $i + 1 >= count($stages)) {
        $state['stage']  = 'done';
        $state['status'] = 'done';
        $state['finished_at'] = date('c');
        return;
    }
    $state['stage']        = $stages[$i + 1];
    $state['after_cursor'] = null;
}

/* ═════════════════════════════════════════════════════════════════════════
 * Step processors
 * ═════════════════════════════════════════════════════════════════════════ */

function hs_step_init(array &$state, HubSpotClient $hs, array $settings): void
{
    $state['started_at']  = date('c');
    $state['finished_at'] = null;
    $state['last_error']  = null;
    $state['after_cursor'] = null;
    $state['pending'] = ['vts' => [], 'clients' => [], 'csms' => []];
    $state['stats']   = hs_state_default()['stats'];
    $state['messages'] = [];
    hs_state_log($state, 'Sync started.');
    hs_state_advance_stage($state);
}

function hs_step_fetch_generic(array &$state, HubSpotClient $hs, string $object, array $filter, array $properties, string $bucket, string $label, int $batchSize): void
{
    try {
        $data = $hs->searchPage($object, $filter, $properties, $batchSize, $state['after_cursor']);
    } catch (Throwable $ex) {
        $state['status']     = 'error';
        $state['last_error'] = $ex->getMessage();
        hs_state_log($state, "Fetch {$label} failed: " . $ex->getMessage());
        return;
    }
    $results = $data['results'] ?? [];
    foreach ($results as $obj) {
        $state['pending'][$bucket][] = [
            'id'         => (string) ($obj['id'] ?? ''),
            'properties' => is_array($obj['properties'] ?? null) ? $obj['properties'] : [],
        ];
    }
    $state['stats']['fetched_total'][$bucket] += count($results);
    $state['after_cursor'] = $data['paging']['next']['after'] ?? null;

    if ($state['after_cursor'] === null || $state['after_cursor'] === '') {
        hs_state_log($state, "Fetched " . $state['stats']['fetched_total'][$bucket] . " {$label} from HubSpot.");
        hs_state_advance_stage($state);
    }
}

function hs_step_fetch_vts(array &$state, HubSpotClient $hs, array $settings): void
{
    hs_step_fetch_generic($state, $hs, 'contacts',
        ['propertyName' => $settings['hs_vt_lead_status_field'], 'operator' => 'EQ', 'value' => $settings['hs_vt_lead_status_value']],
        hs_vt_properties(), 'vts', 'VTs', (int) $settings['hs_batch_size']);
}

function hs_step_fetch_clients(array &$state, HubSpotClient $hs, array $settings): void
{
    hs_step_fetch_generic($state, $hs, 'companies',
        ['propertyName' => $settings['hs_client_lead_status_field'], 'operator' => 'EQ', 'value' => $settings['hs_client_lead_status_value']],
        hs_company_properties(), 'clients', 'client companies', (int) $settings['hs_batch_size']);
}

function hs_step_fetch_csms(array &$state, HubSpotClient $hs, array $settings): void
{
    hs_step_fetch_generic($state, $hs, 'contacts',
        ['propertyName' => $settings['hs_csm_lead_status_field'], 'operator' => 'EQ', 'value' => $settings['hs_csm_lead_status_value']],
        hs_csm_properties(), 'csms', 'CSMs', (int) $settings['hs_batch_size']);
}

function hs_process_vt_one(array $contact, array $settings, array &$state): void
{
    $pdo = db();
    $contactId = (string) ($contact['id'] ?? '');
    $props     = is_array($contact['properties'] ?? null) ? $contact['properties'] : [];
    $email     = strtolower(trim((string) ($props['email'] ?? '')));
    $vtStatus  = trim((string) ($props['vt_status'] ?? ''));
    $leadStatus = trim((string) ($props[$settings['hs_vt_lead_status_field']] ?? ''));

    if ($email === '') { $state['stats']['vts']['skipped']++; return; }

    $role = hs_map_vt_role($vtStatus, $leadStatus);
    if ($role === '') {
        $state['stats']['vts']['skipped']++;
        $who = hs_contact_label_from_props($contact);
        hs_state_log($state, "Skipped {$who}: unmapped vt_status \"{$vtStatus}\".");
        return;
    }

    if (in_array(strtolower($vtStatus), ['no longer eligible', 'no-longer-eligible'], true)) {
        if ($existing = hs_find_user_for_contact($contactId, $email)) {
            $pdo->prepare('DELETE FROM users WHERE id = :id')->execute([':id' => $existing['id']]);
            $state['stats']['vts']['deleted']++;
            audit_log('hs_sync_delete', 'user', (int) $existing['id'], 'email=' . $email);
        }
        return;
    }

    $first   = trim((string) ($props['firstname'] ?? ''));
    $last    = trim((string) ($props['lastname']  ?? ''));
    $phone   = hs_pick($props, ['phone','mobilephone']);
    $country = trim((string) ($props['country'] ?? ''));

    $remotePhoto  = hs_pick($props, ['vt_profile_picture_link','profile_picture_url','vt_profile_picture_url','headshot_url','photo_url','profile_picture','headshot','photo']);
    $remoteResume = hs_pick($props, ['resume_url','resume_link','cv_url','resume','cv']);
    $remoteVideo  = hs_pick($props, ['intro_video_link','intro_video_url','intro_video','introduction_video','video_url']);

    $existing = hs_find_user_for_contact($contactId, $email);
    if ($existing) {
        $pdo->prepare(
            'UPDATE users SET email=:e, role=:r, first_name=:fn, last_name=:ln, phone=:p, country=:c,
                              hubspot_contact_id=:hcid, updated_at=CURRENT_TIMESTAMP
             WHERE id=:id'
        )->execute([
            ':e'=>$email, ':r'=>$role, ':fn'=>$first, ':ln'=>$last, ':p'=>$phone, ':c'=>$country,
            ':hcid'=>$contactId, ':id'=>$existing['id'],
        ]);
        $userId = (int) $existing['id'];
        $state['stats']['vts']['updated']++;
    } else {
        $pdo->prepare(
            'INSERT INTO users (email, password_hash, role, first_name, last_name, phone, country, hubspot_contact_id, active, notify_by_email)
             VALUES (:e, :h, :r, :fn, :ln, :p, :c, :hcid, 1, 1)'
        )->execute([
            ':e'=>$email, ':h'=>password_hash(hs_default_password($role), PASSWORD_DEFAULT),
            ':r'=>$role, ':fn'=>$first, ':ln'=>$last, ':p'=>$phone, ':c'=>$country, ':hcid'=>$contactId,
        ]);
        $userId = (int) $pdo->lastInsertId();
        $state['stats']['vts']['created']++;
    }

    // Media import (high-quality, original bytes) with cache-by-source-URL.
    $localPhoto = $localResume = $localVideo = '';
    $newPhotoSrc = hs_normalize_media_url($remotePhoto, 'photo');
    $newResumeSrc = hs_normalize_media_url($remoteResume, 'resume');
    $newVideoSrc  = hs_normalize_media_url($remoteVideo, 'video');
    if (!empty($settings['hs_import_media'])) {
        // Pull existing source URLs so we can short-circuit downloads when nothing changed.
        $stmt = $pdo->prepare('SELECT photo_source_url FROM users WHERE id = :id');
        $stmt->execute([':id' => $userId]);
        $existPhotoSrc = (string) $stmt->fetchColumn();
        $stmt = $pdo->prepare('SELECT resume_source_url, video_source_url FROM vt_profiles WHERE user_id = :u LIMIT 1');
        $stmt->execute([':u' => $userId]);
        $rowSrc = $stmt->fetch() ?: ['resume_source_url' => '', 'video_source_url' => ''];

        $sk = false;
        $localPhoto = hs_import_media($remotePhoto, 'vt', $userId, 'photo', $existPhotoSrc, $sk);
        if ($sk) { $state['stats']['media_skipped']++; }
        elseif ($localPhoto !== '') { $state['stats']['media_downloads']++; }

        $sk = false;
        $localResume = hs_import_media($remoteResume, 'vt', $userId, 'resume', (string) $rowSrc['resume_source_url'], $sk);
        if ($sk) { $state['stats']['media_skipped']++; }
        elseif ($localResume !== '') { $state['stats']['media_downloads']++; }

        $sk = false;
        $localVideo = hs_import_media($remoteVideo, 'vt', $userId, 'video', (string) $rowSrc['video_source_url'], $sk);
        if ($sk) { $state['stats']['media_skipped']++; }
        elseif ($localVideo !== '') { $state['stats']['media_downloads']++; }
    }
    if ($localPhoto !== '') {
        $pdo->prepare('UPDATE users SET photo_url = :ph, photo_source_url = :ps WHERE id = :id')
            ->execute([':ph' => $localPhoto, ':ps' => $newPhotoSrc, ':id' => $userId]);
    }

    // Upsert the VT profile row.
    $profileFields = [
        'status'             => ($role === 'vt_hired') ? 'hired' : 'onpool',
        'department'         => hs_pick($props, ['vt_department','department']),
        'role_title'         => hs_pick($props, ['primary_role','role','position','jobtitle']),
        'experience_years'   => (int) (hs_pick($props, ['years_of_experience__vt_','years_of_experience','experience_years'], '0') ?: 0),
        'english_level'      => hs_pick($props, ['english_proficiency','english_level']),
        'iq_band'            => hs_pick($props, ['iq_test_score__max_10_','iq_level']),
        'technical_band'     => hs_pick($props, ['technical_proficiency__max_5_','technical_skills_level']),
        'summary'            => hs_pick($props, ['summary','vt_skills_summary','professional_summary']),
        'experience_text'    => hs_pick($props, ['vt_experience_summary','professional_summary']),
        'resume_url'         => $localResume !== '' ? $localResume : hs_pick($props, ['resume_url','resume_link','cv_url','resume','cv']),
        'resume_source_url'  => $newResumeSrc,
        'video_url'          => $localVideo  !== '' ? $localVideo  : hs_pick($props, ['intro_video_link','intro_video_url','intro_video','introduction_video','video_url']),
        'video_source_url'   => $newVideoSrc,
        'workday_tracker_id' => hs_pick($props, ['workday_tracker_id','workday_report_id','workdaytracker_report_id','workday_tracker_report_id']),
    ];
    $existsStmt = $pdo->prepare('SELECT id FROM vt_profiles WHERE user_id = :u LIMIT 1');
    $existsStmt->execute([':u' => $userId]);
    $existsP = (int) $existsStmt->fetchColumn();
    if ($existsP > 0) {
        $sets   = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($profileFields)));
        $params = array_combine(array_map(fn($k)=>":$k", array_keys($profileFields)), array_values($profileFields));
        $params[':id'] = $existsP;
        $pdo->prepare("UPDATE vt_profiles SET $sets, updated_at = CURRENT_TIMESTAMP WHERE id = :id")->execute($params);
    } else {
        $cols   = array_merge(['user_id'], array_keys($profileFields));
        $vals   = array_merge([':user_id'], array_map(fn($k)=>":$k", array_keys($profileFields)));
        $params = array_combine(array_map(fn($k)=>":$k", array_keys($profileFields)), array_values($profileFields));
        $params[':user_id'] = $userId;
        $pdo->prepare('INSERT INTO vt_profiles (' . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ')')->execute($params);
    }

    audit_log('hs_sync_upsert', 'user', $userId, 'role=' . $role . ' contact=' . $contactId);
}

function hs_process_client_one(array $company, array $settings, array &$state): void
{
    $pdo = db();
    $companyId = (string) ($company['id'] ?? '');
    $props     = is_array($company['properties'] ?? null) ? $company['properties'] : [];
    $name      = hs_pick($props, ['name','company_name','company']);
    $domain    = hs_pick($props, ['domain','website']);
    $email     = strtolower(trim(hs_pick($props, ['hs_contact_email','company_email','billing_email'])));

    if ($name === '' && $domain === '') { $state['stats']['clients']['skipped']++; return; }
    if ($name === '') { $name = $domain ?: ('Company ' . $companyId); }

    $row = null;
    if ($companyId !== '') {
        $stmt = $pdo->prepare('SELECT * FROM clients WHERE hubspot_company_id = :h LIMIT 1');
        $stmt->execute([':h' => $companyId]);
        $row = $stmt->fetch() ?: null;
    }
    if (!$row) {
        $stmt = $pdo->prepare('SELECT * FROM clients WHERE company_name = :n LIMIT 1');
        $stmt->execute([':n' => $name]);
        $row = $stmt->fetch() ?: null;
    }

    $userId = null;
    if ($email !== '') {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e' => $email]);
        $userId = (int) ($stmt->fetchColumn() ?: 0);
        if ($userId === 0) {
            $pdo->prepare(
                "INSERT INTO users (email, password_hash, role, first_name, last_name, active, notify_by_email)
                 VALUES (:e, :h, 'client', '', :ln, 1, 1)"
            )->execute([
                ':e' => $email, ':h' => password_hash(hs_default_password('client'), PASSWORD_DEFAULT), ':ln' => $name,
            ]);
            $userId = (int) $pdo->lastInsertId();
        }
    }

    $ownerId = trim((string) ($props['hubspot_owner_id'] ?? ''));

    if ($row) {
        $pdo->prepare(
            "UPDATE clients SET user_id=:u, company_name=:n, company_email=:e, company_domain=:d,
                                contract_status='active', hubspot_company_id=:h, hubspot_owner_id=:o,
                                updated_at=CURRENT_TIMESTAMP
             WHERE id=:id"
        )->execute([
            ':u'=>$userId ?: $row['user_id'], ':n'=>$name, ':e'=>$email, ':d'=>$domain,
            ':h'=>$companyId, ':o'=>$ownerId, ':id'=>$row['id'],
        ]);
        $state['stats']['clients']['updated']++;
        audit_log('hs_sync_upsert', 'client', (int) $row['id'], 'company=' . $companyId);
    } else {
        $pdo->prepare(
            "INSERT INTO clients (user_id, company_name, company_email, company_domain, contract_status, hubspot_company_id, hubspot_owner_id)
             VALUES (:u, :n, :e, :d, 'active', :h, :o)"
        )->execute([
            ':u'=>$userId, ':n'=>$name, ':e'=>$email, ':d'=>$domain, ':h'=>$companyId, ':o'=>$ownerId,
        ]);
        $state['stats']['clients']['created']++;
        audit_log('hs_sync_create', 'client', (int) $pdo->lastInsertId(), 'company=' . $companyId);
    }
}

function hs_process_csm_one(array $contact, array $settings, array &$state): void
{
    $pdo = db();
    $contactId = (string) ($contact['id'] ?? '');
    $props     = is_array($contact['properties'] ?? null) ? $contact['properties'] : [];
    $email     = strtolower(trim((string) ($props['email'] ?? '')));
    if ($email === '') { $state['stats']['csms']['skipped']++; return; }

    $first   = trim((string) ($props['firstname'] ?? ''));
    $last    = trim((string) ($props['lastname']  ?? ''));
    $phone   = hs_pick($props, ['phone','mobilephone']);
    $country = trim((string) ($props['country'] ?? ''));

    $existing = hs_find_user_for_contact($contactId, $email);
    if ($existing) {
        $pdo->prepare(
            "UPDATE users SET email=:e, role='csm', first_name=:fn, last_name=:ln, phone=:p, country=:c,
                              hubspot_contact_id=:h, updated_at=CURRENT_TIMESTAMP
             WHERE id=:id"
        )->execute([
            ':e'=>$email, ':fn'=>$first, ':ln'=>$last, ':p'=>$phone, ':c'=>$country,
            ':h'=>$contactId, ':id'=>$existing['id'],
        ]);
        $state['stats']['csms']['updated']++;
    } else {
        $pdo->prepare(
            "INSERT INTO users (email, password_hash, role, first_name, last_name, phone, country, hubspot_contact_id, active, notify_by_email)
             VALUES (:e, :h, 'csm', :fn, :ln, :p, :c, :hcid, 1, 1)"
        )->execute([
            ':e'=>$email, ':h'=>password_hash(hs_default_password('csm'), PASSWORD_DEFAULT),
            ':fn'=>$first, ':ln'=>$last, ':p'=>$phone, ':c'=>$country, ':hcid'=>$contactId,
        ]);
        $state['stats']['csms']['created']++;
    }
}

/** Generic process step: drain $batchSize items from $state['pending'][$bucket]. */
function hs_step_process_generic(array &$state, array $settings, string $bucket, callable $processor, string $label): void
{
    if (empty($state['pending'][$bucket])) {
        hs_state_log($state, "All {$label} processed.");
        hs_state_advance_stage($state);
        return;
    }
    $batch = array_splice($state['pending'][$bucket], 0, HS_PROCESS_BATCH);
    foreach ($batch as $item) {
        try {
            $processor($item, $settings, $state);
        } catch (Throwable $ex) {
            $state['stats'][$bucket]['errors']++;
            hs_state_log($state, "{$label} item failed: " . $ex->getMessage());
        }
    }
    if (empty($state['pending'][$bucket])) {
        hs_state_log($state, "All {$label} processed.");
        hs_state_advance_stage($state);
    }
}

function hs_step_process_vts(array &$state, HubSpotClient $hs, array $settings): void
{
    hs_step_process_generic($state, $settings, 'vts', 'hs_process_vt_one', 'VTs');
}
function hs_step_process_clients(array &$state, HubSpotClient $hs, array $settings): void
{
    hs_step_process_generic($state, $settings, 'clients', 'hs_process_client_one', 'client companies');
}
function hs_step_process_csms(array &$state, HubSpotClient $hs, array $settings): void
{
    hs_step_process_generic($state, $settings, 'csms', 'hs_process_csm_one', 'CSMs');
}

/* ═════════════════════════════════════════════════════════════════════════
 * Relationship linking — populates csm_clients + client_vts from HubSpot
 * company-to-contact associations + company owners. Runs after all entities
 * have been ingested so the user lookups by hubspot_contact_id / owner_id
 * succeed. Mirrors the WP plugin's `relationship_*` summary keys.
 * ═════════════════════════════════════════════════════════════════════════ */

/**
 * Resolve a HubSpot owner ID to a portal CSM user. If the owner is already
 * a user (by email or owner_id), return it; otherwise fetch /crm/v3/owners/{id}
 * and create a new CSM user. Returns 0 on failure.
 */
function hs_resolve_owner_as_csm(string $ownerId, HubSpotClient $hs, array &$state): int
{
    if ($ownerId === '') { return 0; }
    $pdo = db();

    // Already linked by owner ID?
    $stmt = $pdo->prepare('SELECT id FROM users WHERE hubspot_owner_id = :o LIMIT 1');
    $stmt->execute([':o' => $ownerId]);
    if ($uid = (int) ($stmt->fetchColumn() ?: 0)) { return $uid; }

    // Fetch owner details from HubSpot.
    $resp = $hs->request('GET', '/crm/v3/owners/' . rawurlencode($ownerId));
    if (!$resp['ok']) {
        $state['stats']['relationships']['errors']++;
        hs_state_log($state, "Owner {$ownerId} fetch failed: " . ($resp['error'] ?: 'unknown'));
        return 0;
    }
    $owner = is_array($resp['data']) ? $resp['data'] : [];
    $email = strtolower(trim((string) ($owner['email'] ?? '')));
    if ($email === '') { return 0; }

    $first = trim((string) ($owner['firstName'] ?? ''));
    $last  = trim((string) ($owner['lastName']  ?? ''));

    // Existing user by email? Upgrade to csm + stamp owner_id.
    $stmt = $pdo->prepare('SELECT id, role FROM users WHERE email = :e LIMIT 1');
    $stmt->execute([':e' => $email]);
    if ($u = $stmt->fetch()) {
        // Don't demote a super_admin; otherwise reclassify as csm.
        $newRole = $u['role'] === 'super_admin' ? 'super_admin' : 'csm';
        $pdo->prepare('UPDATE users SET role=:r, hubspot_owner_id=:o, updated_at=CURRENT_TIMESTAMP WHERE id=:id')
            ->execute([':r' => $newRole, ':o' => $ownerId, ':id' => $u['id']]);
        $state['stats']['relationships']['owners_resolved']++;
        return (int) $u['id'];
    }

    // Create new CSM user from owner details.
    $pdo->prepare(
        "INSERT INTO users (email, password_hash, role, first_name, last_name, hubspot_owner_id, active, notify_by_email)
         VALUES (:e, :h, 'csm', :fn, :ln, :o, 1, 1)"
    )->execute([
        ':e'  => $email,
        ':h'  => password_hash(hs_default_password('csm'), PASSWORD_DEFAULT),
        ':fn' => $first, ':ln' => $last, ':o' => $ownerId,
    ]);
    $state['stats']['relationships']['owners_resolved']++;
    $state['stats']['csms']['created']++;
    return (int) $pdo->lastInsertId();
}

/** Link a single client's relationships from HubSpot to local join tables. */
function hs_link_one_client(array $item, HubSpotClient $hs, array &$state): void
{
    $pdo       = db();
    $clientId  = (int) $item['client_id'];
    $companyId = (string) $item['company_id'];
    $ownerId   = (string) $item['owner_id'];

    // Wipe stale associations for this client so the sync is authoritative
    // (handles VT leaving the company, owner changing, etc.).
    $pdo->prepare('DELETE FROM client_vts  WHERE client_id = :c')->execute([':c' => $clientId]);
    $pdo->prepare('DELETE FROM csm_clients WHERE client_id = :c')->execute([':c' => $clientId]);

    // 1) Company -> contact associations -> client_vts
    if ($companyId !== '') {
        $resp = $hs->request('GET', '/crm/v3/objects/companies/' . rawurlencode($companyId) . '/associations/contacts');
        if ($resp['ok']) {
            $assocs = is_array($resp['data']['results'] ?? null) ? $resp['data']['results'] : [];
            foreach ($assocs as $assoc) {
                $contactId = (string) ($assoc['id'] ?? '');
                if ($contactId === '') { continue; }
                $stmt = $pdo->prepare('SELECT id, role FROM users WHERE hubspot_contact_id = :c LIMIT 1');
                $stmt->execute([':c' => $contactId]);
                $u = $stmt->fetch();
                if (!$u) { continue; }
                if (!in_array($u['role'], ['vt_hired', 'vt_onpool'], true)) { continue; }
                $contractStatus = $u['role'] === 'vt_hired' ? 'active' : 'pool';
                $pdo->prepare(
                    'INSERT OR IGNORE INTO client_vts (client_id, vt_user_id, contract_status)
                     VALUES (:c, :v, :s)'
                )->execute([':c' => $clientId, ':v' => (int) $u['id'], ':s' => $contractStatus]);
                $state['stats']['relationships']['vt_links']++;
            }
        } else {
            $state['stats']['relationships']['errors']++;
            hs_state_log($state, "Assoc fetch failed for company {$companyId}: " . ($resp['error'] ?: 'unknown'));
        }
    }

    // 2) Company owner -> CSM user -> csm_clients
    if ($ownerId !== '') {
        $csmUserId = hs_resolve_owner_as_csm($ownerId, $hs, $state);
        if ($csmUserId > 0) {
            $pdo->prepare(
                'INSERT OR IGNORE INTO csm_clients (csm_user_id, client_id)
                 VALUES (:csm, :c)'
            )->execute([':csm' => $csmUserId, ':c' => $clientId]);
            $state['stats']['relationships']['csm_links']++;
        }
    }
}

function hs_step_link_associations(array &$state, HubSpotClient $hs, array $settings): void
{
    $pdo = db();

    // Seed the queue from local DB the first time this stage runs.
    if (empty($state['link_initialized'])) {
        $state['pending']['links'] = [];
        $stmt = $pdo->query("SELECT id, hubspot_company_id, hubspot_owner_id FROM clients WHERE hubspot_company_id != ''");
        foreach ($stmt as $row) {
            $state['pending']['links'][] = [
                'client_id'  => (int) $row['id'],
                'company_id' => (string) $row['hubspot_company_id'],
                'owner_id'   => (string) $row['hubspot_owner_id'],
            ];
        }
        $state['link_initialized'] = true;
        hs_state_log($state, 'Queued ' . count($state['pending']['links']) . ' client companies for association linking.');
    }

    if (empty($state['pending']['links'])) {
        hs_state_log($state, 'All client relationships linked.');
        hs_state_advance_stage($state);
        return;
    }

    $batch = array_splice($state['pending']['links'], 0, HS_PROCESS_BATCH);
    foreach ($batch as $item) {
        try {
            hs_link_one_client($item, $hs, $state);
        } catch (Throwable $ex) {
            $state['stats']['relationships']['errors']++;
            hs_state_log($state, "Link client {$item['client_id']} failed: " . $ex->getMessage());
        }
    }

    if (empty($state['pending']['links'])) {
        hs_state_log($state, 'All client relationships linked.');
        hs_state_advance_stage($state);
    }
}

/* ═════════════════════════════════════════════════════════════════════════
 * Dispatcher
 * ═════════════════════════════════════════════════════════════════════════ */

/**
 * Run ONE step of the state machine. Returns the updated state.
 * Caller should re-call until state.status changes from 'running'.
 */
function hs_step(): array
{
    $state    = hs_state_load();
    $settings = hs_settings();

    if ($state['status'] !== 'running') { return $state; }
    if ($state['stage'] === 'done') {
        $state['status'] = 'done';
        $state['finished_at'] = $state['finished_at'] ?? date('c');
        hs_state_save($state);
        return $state;
    }

    $hs = new HubSpotClient((string) $settings['hs_token']);

    $stage = $state['stage'];
    try {
        switch ($stage) {
            case 'init':            hs_step_init($state, $hs, $settings);           break;
            case 'fetch_vts':       hs_step_fetch_vts($state, $hs, $settings);      break;
            case 'process_vts':     hs_step_process_vts($state, $hs, $settings);    break;
            case 'fetch_clients':   hs_step_fetch_clients($state, $hs, $settings);  break;
            case 'process_clients': hs_step_process_clients($state, $hs, $settings);break;
            case 'fetch_csms':         hs_step_fetch_csms($state, $hs, $settings);      break;
            case 'process_csms':       hs_step_process_csms($state, $hs, $settings);    break;
            case 'link_associations':  hs_step_link_associations($state, $hs, $settings); break;
            default:                   hs_state_advance_stage($state);                  break;
        }
    } catch (Throwable $ex) {
        $state['status']     = 'error';
        $state['last_error'] = $ex->getMessage();
        hs_state_log($state, 'Fatal: ' . $ex->getMessage());
    }

    if ($state['stage'] === 'done' && $state['status'] !== 'error') {
        $state['status']      = 'done';
        $state['finished_at'] = date('c');
        hs_state_log($state, 'Sync finished.');
    }

    hs_state_save($state);
    return $state;
}

/**
 * Apply a control action: start | pause | resume | reset.
 */
function hs_control(string $action): array
{
    $state = hs_state_load();
    switch ($action) {
        case 'start':
            if ($state['status'] === 'running') { break; }
            // Fresh run unless we're explicitly resuming.
            $state = hs_state_default();
            $state['status'] = 'running';
            $state['stage']  = 'init';
            break;
        case 'pause':
            if ($state['status'] === 'running') { $state['status'] = 'paused'; }
            break;
        case 'resume':
            if (in_array($state['status'], ['paused', 'error'], true)) {
                $state['status']     = 'running';
                $state['last_error'] = null;
            }
            break;
        case 'reset':
            $state = hs_state_default();
            break;
    }
    hs_state_save($state);
    return $state;
}

/* ═════════════════════════════════════════════════════════════════════════
 * PHASE 2 — Two-pipeline state machine.
 *
 * Talent and Client sync each get an independent state object so they can
 * be started / paused / resumed / reset on their own. The legacy combined
 * `hs_step()` / `hs_control()` above stays in place for the existing UI
 * until Phase 7 retires it. The new pipelines live under:
 *
 *   app_settings.hs_talent_state    — drives talent (VT) sync
 *   app_settings.hs_client_state    — drives client + CSM + associations sync
 *
 * Phase 2 (this commit) wires the plumbing only — the per-stage processors
 * are stubs that just advance the stage to `done`. Phase 3 fills in talent,
 * Phase 4 fills in client.
 * ═════════════════════════════════════════════════════════════════════════ */

const HS_TALENT_STATE_KEY = 'hs_talent_state';
const HS_CLIENT_STATE_KEY = 'hs_client_state';

function hs_talent_state_default(): array
{
    return [
        'version'      => 1,
        'pipeline'     => 'talent',
        'status'       => 'idle',                    // idle | running | paused | done | error
        'stage'        => 'init',
        'stages'       => [
            'init',
            'fetch_vts_eligible',                    // filterGroup 1: lead=VT + vt_status=Unmatched / Eligible
            'fetch_vts_matched',                     // filterGroup 2: lead=VT + vt_status=Matched
            'fetch_vts_contracted',                  // filterGroup 3: lead=VT + vt_status=Contracted + contract_hired_status=First Day Complete
            'process_vts',
            'download_media',                        // Phase 5 fills this in
            'done',
        ],
        'after_cursor' => null,
        'pending'      => ['vts' => [], 'media' => [], 'ci_roles' => []],
        'started_at'   => null,
        'updated_at'   => null,
        'finished_at'  => null,
        'last_error'   => null,
        // Per-stage retry counter; bumped on transient stage exceptions and
        // reset once the stage advances. Lets a transient network blip retry
        // instead of permanently parking the pipeline in 'error'.
        'stage_attempts' => [],
        'stats' => [
            'fetched_total'   => ['eligible' => 0, 'matched' => 0, 'contracted' => 0],
            'vts'             => ['created'=>0,'updated'=>0,'skipped_role'=>0,'skipped_no_email'=>0,'errors'=>0],
            'media'           => ['downloaded'=>0,'cache_hits'=>0,'fallbacks'=>0,'skipped'=>0,'errors'=>0,'failed_urls'=>[]],
            'ci_roles'        => ['fetched'=>0,'linked'=>0,'errors'=>0],
        ],
        'messages'     => [],
        // The latest summary report (rendered in the UI after a run completes).
        'last_report'  => null,
    ];
}

function hs_client_state_default(): array
{
    return [
        'version'      => 1,
        'pipeline'     => 'client',
        'status'       => 'idle',
        'stage'        => 'init',
        'stages'       => [
            'init',
            'fetch_companies',                       // companies w/ lead=Client - Active
            'fetch_primary_contacts',                // company -> contacts assoc, pick Primary + collect Teammate-labeled hired contacts
            'fetch_contracts',                       // company -> contracts (object 2-31153232)
            'filter_first_day_complete',             // keep contracts at stage "First Day Complete" (pipeline-label resolved)
            'fetch_contract_contacts',               // contracts -> contacts (assoc type 28 = hired)
            'fetch_owner_csms',                      // resolve hubspot_owner_id -> CSM users
            'upsert_hired_vts',                      // bulk-fetch + upsert hired contacts as vt_hired users
            'process_clients',                       // upsert clients + login user from Primary Contact
            'process_associations',                  // write csm_clients + client_vts
            'done',
        ],
        'after_cursor' => null,
        'pending'      => [
            'companies' => [],
            'primary_contacts' => [],
            'contracts' => [],
            'first_day_contracts' => [],
            'contract_contacts' => [],
            'owners' => [],
            'links' => [],
        ],
        'started_at'   => null,
        'updated_at'   => null,
        'finished_at'  => null,
        'last_error'   => null,
        'stage_attempts' => [],
        'stats' => [
            'fetched_total'   => ['companies'=>0,'contracts'=>0,'first_day_contracts'=>0,'hired_contacts'=>0],
            'clients'         => ['created'=>0,'updated'=>0,'skipped'=>0,'errors'=>0],
            'csms'            => ['created'=>0,'updated'=>0,'errors'=>0],
            'relationships'   => ['vt_links'=>0,'csm_links'=>0,'owners_resolved'=>0,'errors'=>0],
        ],
        'messages'     => [],
        'last_report'  => null,
    ];
}

/** Generic JSON load — used by both pipeline loaders. */
function hs_pipeline_state_load(string $key, callable $defaultFn): array
{
    $raw = get_setting($key, '');
    if ($raw === '') { return $defaultFn(); }
    $s = json_decode($raw, true);
    if (!is_array($s) || ($s['version'] ?? 0) !== 1) { return $defaultFn(); }
    return array_replace_recursive($defaultFn(), $s);
}

function hs_pipeline_state_save(string $key, array $state): void
{
    $state['updated_at'] = date('c');
    set_setting($key, json_encode($state, JSON_UNESCAPED_SLASHES));
}

function hs_talent_state_load(): array { return hs_pipeline_state_load(HS_TALENT_STATE_KEY, 'hs_talent_state_default'); }
function hs_talent_state_save(array $s): void { hs_pipeline_state_save(HS_TALENT_STATE_KEY, $s); }
function hs_client_state_load(): array { return hs_pipeline_state_load(HS_CLIENT_STATE_KEY, 'hs_client_state_default'); }
function hs_client_state_save(array $s): void { hs_pipeline_state_save(HS_CLIENT_STATE_KEY, $s); }

/* ─── Talent pipeline ─── */

/**
 * Build a log label from a raw HubSpot contact item (the shape returned by
 * the /crm/v3/objects/contacts search endpoint):
 *   "First Last <email> (HS#contactId)"
 */
function hs_contact_label_from_props(array $item): string
{
    $cid   = (string) ($item['id'] ?? '');
    $props = is_array($item['properties'] ?? null) ? $item['properties'] : [];
    $first = trim((string) ($props['firstname'] ?? ''));
    $last  = trim((string) ($props['lastname']  ?? ''));
    $name  = trim($first . ' ' . $last);
    $email = strtolower(trim((string) ($props['email'] ?? '')));
    $bits  = [];
    if ($name  !== '') { $bits[] = $name; }
    if ($email !== '') { $bits[] = "<{$email}>"; }
    if ($cid   !== '') { $bits[] = "(HS#{$cid})"; }
    return $bits ? implode(' ', $bits) : 'contact (unknown)';
}

/**
 * Build a log label from a raw HubSpot company item:
 *   "Acme Inc <acme.com> (HS#companyId)"
 */
function hs_company_label_from_props(array $item): string
{
    $cid     = (string) ($item['id'] ?? '');
    $props   = is_array($item['properties'] ?? null) ? $item['properties'] : [];
    $name    = trim((string) ($props['name']    ?? ''));
    $domain  = trim((string) ($props['domain']  ?? ''));
    $bits    = [];
    if ($name   !== '') { $bits[] = $name; }
    if ($domain !== '') { $bits[] = "<{$domain}>"; }
    if ($cid    !== '') { $bits[] = "(HS#{$cid})"; }
    return $bits ? implode(' ', $bits) : 'company (unknown)';
}

/**
 * Resolve a user row into a one-line label suitable for the activity log:
 * "First Last <email> (#id)". Falls back to "user #id" if the row is gone.
 *
 * @return array{tag:string,name:string,email:string}
 */
function hs_user_label_for_log(PDO $pdo, int $uid): array
{
    static $cache = [];
    if (isset($cache[$uid])) { return $cache[$uid]; }
    try {
        $stmt = $pdo->prepare('SELECT first_name, last_name, email FROM users WHERE id = :u');
        $stmt->execute([':u' => $uid]);
        $row = $stmt->fetch() ?: [];
    } catch (Throwable $ex) {
        $row = [];
    }
    $name  = trim(((string)($row['first_name'] ?? '')) . ' ' . ((string)($row['last_name'] ?? '')));
    $email = (string) ($row['email'] ?? '');
    if ($name !== '' && $email !== '') {
        $tag = "{$name} <{$email}> (#{$uid})";
    } elseif ($name !== '') {
        $tag = "{$name} (#{$uid})";
    } elseif ($email !== '') {
        $tag = "<{$email}> (#{$uid})";
    } else {
        $tag = "user #{$uid}";
    }
    return $cache[$uid] = ['tag' => $tag, 'name' => $name, 'email' => $email];
}

/**
 * Per-stage exception handler. Lets the pipeline push through transient
 * failures instead of permanently halting on the first throw.
 *
 *   - Retries the same stage on the next dispatcher tick up to MAX attempts.
 *   - Once the budget is exhausted, logs the failure, advances past the
 *     stage and resets the counter so subsequent stages still run.
 *   - The pipeline stays in 'running' status until a stage truly cannot
 *     recover (we never escalate to 'error' here — the caller can if it
 *     needs a hard stop).
 */
function hs_handle_stage_exception(array &$state, string $stage, Throwable $ex): void
{
    $max = 3;
    $attempts = (int) ($state['stage_attempts'][$stage] ?? 0) + 1;
    $state['stage_attempts'][$stage] = $attempts;
    $state['last_error'] = $ex->getMessage();
    if ($attempts < $max) {
        hs_state_log($state, "Stage '{$stage}' failed (attempt {$attempts}/{$max}): " . $ex->getMessage() . ' — will retry.');
        return;
    }
    hs_state_log($state, "Stage '{$stage}' giving up after {$max} attempts: " . $ex->getMessage() . ' — skipping to next stage.');
    unset($state['stage_attempts'][$stage]);
    try { hs_state_advance_stage($state); }
    catch (Throwable $ex2) {
        $state['status']     = 'error';
        $state['last_error'] = 'Fatal: ' . $ex2->getMessage();
        hs_state_log($state, 'Fatal: ' . $ex2->getMessage());
    }
}

function hs_talent_step(): array
{
    // A single step may download several large files (videos) before returning.
    // Lift PHP runtime ceilings so a big media batch can't be killed mid-flight.
    hs_raise_runtime_limits();

    $state = hs_talent_state_load();
    if ($state['status'] !== 'running') { return $state; }
    if ($state['stage'] === 'done') {
        $state['status'] = 'done';
        $state['finished_at'] = $state['finished_at'] ?? date('c');
        hs_talent_state_save($state);
        return $state;
    }

    $settings = hs_settings();
    $hs = new HubSpotClient((string) $settings['hs_token']);
    $stageBefore = $state['stage'];

    try {
        switch ($state['stage']) {
            case 'init':
                hs_talent_step_init($state);
                break;
            case 'fetch_vts_eligible':
            case 'fetch_vts_matched':
            case 'fetch_vts_contracted':
                hs_talent_step_fetch($state, $hs, $settings);
                break;
            case 'process_vts':
                hs_talent_step_process($state, $hs, $settings);
                break;
            case 'download_media':
                hs_talent_step_download_media($state, $hs, $settings);
                break;
            default:
                hs_state_advance_stage($state);
                break;
        }
        // Stage made progress (or advanced) — clear its retry counter.
        if (($state['stage'] ?? '') !== $stageBefore) {
            unset($state['stage_attempts'][$stageBefore]);
        }
    } catch (Throwable $ex) {
        hs_handle_stage_exception($state, $stageBefore, $ex);
    }

    if ($state['stage'] === 'done' && $state['status'] !== 'error') {
        $wasAlreadyDone = ($state['status'] === 'done');
        $state['status']      = 'done';
        $state['finished_at'] = date('c');
        $state['last_report'] = hs_build_report($state);
        hs_state_log($state, 'Talent sync finished.');
        if (!$wasAlreadyDone) {
            hs_notify_sync_complete('talent', $state);
        }
    }

    hs_talent_state_save($state);
    return $state;
}

function hs_talent_step_init(array &$state): void
{
    $state['started_at']   = date('c');
    $state['finished_at']  = null;
    $state['last_error']   = null;
    $state['after_cursor'] = null;
    $state['pending']      = hs_talent_state_default()['pending'];
    $state['stats']        = hs_talent_state_default()['stats'];
    $state['messages']     = [];
    $state['last_report']  = null;
    hs_state_log($state, 'Talent sync started.');
    hs_state_advance_stage($state);
}

/* ─── Phase 3: talent fetch + process implementations ─── */

/**
 * Full property list for the contact search — mirrors the staging mu-plugin's
 * `pick_property` fallback chains plus vtadmin's superset. Pulling them all
 * once means we can fall back to alternate property names per role / badge.
 */
function hs_vt_properties_full(): array
{
    return array_values(array_unique([
        // Identity
        'email','firstname','lastname','fullname','phone','mobilephone','country','jobtitle',
        // Status / lead
        'hs_lead_status','vt_status','hs_vt_status','contract_hired_status',
        // Role / department / skills
        'department','vt_department','primary_role','primary_roles','role','position',
        'primary_skillset','primary_skills',
        // Experience
        'years_of_experience','years_of_experience__vt_','experience_years',
        // Narrative
        'summary','vt_skills_summary','vt_experience_summary','professional_summary',
        // Media URLs (raw; Phase 5 downloads)
        'vt_profile_picture_link','profile_picture','profile_picture_url','vt_profile_picture_url',
        'headshot','headshot_url','photo','photo_url',
        'resume','resume_url','resume_link','cv','cv_url',
        'intro_video_link','intro_video','intro_video_url','introduction_video','video_url',
        // Badges — English
        'english_proficiency','english_level','english_badge','english','language_score','english_rate',
        // Badges — IQ / Cognitive (CI)
        'iq_level','iq_description','iq_test_score__max_10_','iq_score','cognitive_level','aptitude_level',
        'ci_role','ci','cognitive_index','ci_score','ci_level','ci_badge','cognitive_index_score','cognitive_score',
        // Badges — Technical
        'technical_proficiency__max_5_','technical_skills_level','technical_skills_badge','tech_level',
        'technical_level','technical_skills_rate','technical_proficiency',
        // Badges — DISC / Personality
        'disc_profile','disc_badge','culture_index','personality_profile','personality',
        // Badges — HIPAA
        'hipaa_certified','hipaa','hipaa_badge',
        // Engagement / predictive
        'engagement_score','hs_predictivecontactscore','predictive_index','quiz_tier',
        // Workday
        'workday_tracker_id','workday_report_id','workdaytracker_report_id','workday_tracker_report_id',
        'workday_tracker_link','vt_wdt_link','vt_workday_tracker_link','workday_report_url',
        // Company linkage
        'company','company_name','associatedcompanyid',
    ]));
}

/**
 * vtadmin-faithful role mapping. Returns null to mean SKIP entirely (the
 * "vt_onboarded" state — Contracted but not yet First-Day-Complete is
 * excluded from the portal per user requirements).
 *
 * @return null|array{role:string, active:int}
 */
function hs_map_vt_role_and_status(string $vtStatus, string $contractHiredStatus = ''): ?array
{
    $vt = strtolower(preg_replace('/\s+/', ' ', trim($vtStatus)));
    $ch = strtolower(preg_replace('/\s+/', ' ', trim($contractHiredStatus)));

    if ($vt === 'contracted') {
        return $ch === 'first day complete' ? ['role' => 'vt_hired', 'active' => 1] : null;
    }
    if ($vt === 'unmatched / eligible' || $vt === 'matched') {
        return ['role' => 'vt_onpool', 'active' => 1];
    }
    // Anything else (Pending, No longer eligible, blank) → SKIP.
    return null;
}

/** filterGroups for each of the 3 fetch stages. */
function hs_talent_filter_for_stage(string $stage, array $settings): array
{
    $leadField  = $settings['hs_vt_lead_status_field']  ?: 'hs_lead_status';
    $leadValue  = $settings['hs_vt_lead_status_value']  ?: 'Virtual Teammate';
    $statusField = $settings['hs_vt_status_field']      ?: 'hs_vt_status';

    return match ($stage) {
        'fetch_vts_eligible' => [
            ['propertyName' => $leadField,   'operator' => 'EQ', 'value' => $leadValue],
            ['propertyName' => $statusField, 'operator' => 'EQ', 'value' => 'Unmatched / Eligible'],
        ],
        'fetch_vts_matched' => [
            ['propertyName' => $leadField,   'operator' => 'EQ', 'value' => $leadValue],
            ['propertyName' => $statusField, 'operator' => 'EQ', 'value' => 'Matched'],
        ],
        'fetch_vts_contracted' => [
            ['propertyName' => $leadField,             'operator' => 'EQ', 'value' => $leadValue],
            ['propertyName' => $statusField,           'operator' => 'EQ', 'value' => 'Contracted'],
            ['propertyName' => 'contract_hired_status','operator' => 'EQ', 'value' => 'First Day Complete'],
        ],
        default => [],
    };
}

/** Single-page contact search; queues results into pending['vts']. */
function hs_talent_step_fetch(array &$state, HubSpotClient $hs, array $settings): void
{
    $stage = $state['stage'];
    $bucketKey = match ($stage) {
        'fetch_vts_eligible'   => 'eligible',
        'fetch_vts_matched'    => 'matched',
        'fetch_vts_contracted' => 'contracted',
        default                => 'unknown',
    };

    $payload = [
        'filterGroups' => [['filters' => hs_talent_filter_for_stage($stage, $settings)]],
        'properties'   => hs_vt_properties_full(),
        'limit'        => 100,
    ];
    if (!empty($state['after_cursor'])) {
        $payload['after'] = (string) $state['after_cursor'];
    }

    $resp = $hs->request('POST', '/crm/v3/objects/contacts/search', $payload);
    if (!$resp['ok']) {
        $state['status']     = 'error';
        $state['last_error'] = (string) ($resp['error'] ?: ('HTTP ' . $resp['status']));
        hs_state_log($state, "Fetch {$bucketKey} failed: " . $state['last_error']);
        return;
    }

    $results = is_array($resp['data']['results'] ?? null) ? $resp['data']['results'] : [];
    foreach ($results as $contact) {
        if (!is_array($contact)) { continue; }
        $state['pending']['vts'][] = [
            'id'           => (string) ($contact['id'] ?? ''),
            'properties'   => is_array($contact['properties'] ?? null) ? $contact['properties'] : [],
            'source_stage' => $bucketKey,
        ];
    }
    $state['stats']['fetched_total'][$bucketKey] += count($results);

    $after = $resp['data']['paging']['next']['after'] ?? null;
    if ($after !== null && $after !== '') {
        $state['after_cursor'] = (string) $after;
        // Stay on this stage; next step() will fetch the next page.
        return;
    }

    hs_state_log($state, "Fetched {$state['stats']['fetched_total'][$bucketKey]} '{$bucketKey}' contacts from HubSpot.");
    $state['after_cursor'] = null;
    hs_state_advance_stage($state);
}

/** Drain HS_PROCESS_BATCH from pending['vts'] and upsert each. */
function hs_talent_step_process(array &$state, HubSpotClient $hs, array $settings): void
{
    if (empty($state['pending']['vts'])) {
        hs_state_log($state, 'All VTs processed (' . array_sum($state['stats']['fetched_total']) . ' fetched).');
        hs_state_advance_stage($state);
        return;
    }
    $batch = array_splice($state['pending']['vts'], 0, HS_PROCESS_BATCH);
    foreach ($batch as $item) {
        try {
            hs_process_vt_contact($item, $state);
        } catch (Throwable $ex) {
            $state['stats']['vts']['errors']++;
            $who = hs_contact_label_from_props($item);
            hs_state_log($state, "Process VT failed for {$who}: " . $ex->getMessage());
        }
    }
    if (empty($state['pending']['vts'])) {
        hs_state_log($state, 'All VTs processed.');
        hs_state_advance_stage($state);
    }
}

/**
 * Upsert one HubSpot contact into users + vt_profiles + vt_profile_meta.
 * vt_onboarded (Contracted without First-Day-Complete) is skipped.
 */
function hs_process_vt_contact(array $item, array &$state): void
{
    $pdo       = db();
    $contactId = (string) ($item['id'] ?? '');
    $props     = is_array($item['properties'] ?? null) ? $item['properties'] : [];
    $email     = strtolower(trim((string) ($props['email'] ?? '')));

    if ($email === '') {
        $state['stats']['vts']['skipped_no_email']++;
        return;
    }

    $vtStatus      = hs_pick($props, ['hs_vt_status','vt_status','vtstatus']);
    $contractHired = hs_pick($props, ['contract_hired_status']);
    $leadStatus    = hs_pick($props, ['hs_lead_status']);

    $roleMap = hs_map_vt_role_and_status($vtStatus, $contractHired);
    if ($roleMap === null) {
        $state['stats']['vts']['skipped_role']++;
        $who = hs_contact_label_from_props($item);
        hs_state_log($state, "Skipped {$who}: vt_status=\"{$vtStatus}\" / contract_hired_status=\"{$contractHired}\" — not eligible for portal.");
        return;
    }
    $role   = $roleMap['role'];
    $active = $roleMap['active'];

    // Identity fields with vtadmin-style fallbacks
    $first = hs_pick($props, ['firstname']);
    $last  = hs_pick($props, ['lastname']);
    $full  = trim($first . ' ' . $last);
    if ($full === '') { $full = hs_pick($props, ['fullname']) ?: $email; }
    $phone    = hs_pick($props, ['phone','mobilephone']);
    $country  = hs_pick($props, ['country']);
    $jobTitle = hs_pick($props, ['jobtitle']);

    // Media — stored as raw source URLs (Phase 5 downloads).
    $photoUrl  = hs_pick($props, [
        'vt_profile_picture_link','profile_picture_url','vt_profile_picture_url',
        'headshot_url','photo','photo_url','profile_picture','headshot',
    ]);
    $resumeUrl = hs_pick($props, ['resume_url','resume_link','cv_url','resume','cv']);
    $videoUrl  = hs_pick($props, ['intro_video_link','intro_video_url','intro_video','introduction_video','video_url']);

    // VT profile extended fields
    $department       = hs_pick($props, ['vt_department','department']);
    $roleTitle        = hs_pick($props, ['primary_role','primary_roles','role','position','jobtitle']);
    $experienceYears  = (int) (hs_pick($props, ['years_of_experience__vt_','years_of_experience','experience_years'], '0') ?: 0);
    $englishLevel     = hs_pick($props, ['english_proficiency','english_level','english_badge','language_score','english_rate','english']);
    $iqBand           = hs_pick($props, ['iq_level','iq_description','cognitive_level','aptitude_level','iq_test_score__max_10_','iq_score']);
    $technicalBand    = hs_pick($props, ['technical_skills_level','technical_skills_badge','tech_level','technical_level','technical_proficiency__max_5_','technical_skills_rate','technical_proficiency']);
    $summary          = hs_pick($props, ['summary','vt_skills_summary','vt_experience_summary','professional_summary']);
    $experienceText   = hs_pick($props, ['vt_experience_summary','professional_summary','summary']);
    $primarySkills    = hs_pick($props, ['primary_skillset','primary_skills']);
    $predictiveIndex  = hs_pick($props, ['predictive_index']);
    $quizTier         = hs_pick($props, ['quiz_tier']);
    $engagementScore  = hs_pick($props, ['engagement_score']);
    $predictiveScore  = hs_pick($props, ['hs_predictivecontactscore']);
    $personalityRaw   = hs_pick($props, ['personality_profile','personality']);

    // Badges (mirrors staging mu-plugin fallback chains).
    $ciRole      = hs_pick($props, ['ci_role','ci','cognitive_index','ci_score','ci_level','ci_badge','cognitive_index_score','cognitive_score']);
    $discProfile = hs_pick($props, ['disc_profile','disc_badge','culture_index','personality_profile']);
    $hipaaCert   = hs_pick($props, ['hipaa_certified','hipaa','hipaa_badge']);

    $workdayTrackerId = hs_pick($props, ['workday_tracker_id','workday_report_id','workdaytracker_report_id','workday_tracker_report_id']);
    $workdayLink      = hs_pick($props, ['workday_tracker_link','vt_wdt_link','vt_workday_tracker_link','workday_report_url']);

    $pdo->beginTransaction();
    try {
        // Find existing user (by HubSpot contact id, then email).
        $existing = hs_find_user_for_contact($contactId, $email);

        if ($existing) {
            $userId = (int) $existing['id'];
            $pdo->prepare(
                'UPDATE users SET email = :e, role = :r, first_name = :fn, last_name = :ln, full_name = :full,
                                  phone = :p, country = :c, job_title = :jt, photo_url = :ph,
                                  hubspot_contact_id = :hcid, vt_status = :vs, hs_lead_status = :ls,
                                  is_hired = :ih, active = :act, updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id'
            )->execute([
                ':e'=>$email, ':r'=>$role, ':fn'=>$first, ':ln'=>$last, ':full'=>$full,
                ':p'=>$phone, ':c'=>$country, ':jt'=>$jobTitle, ':ph'=>$photoUrl,
                ':hcid'=>$contactId, ':vs'=>$vtStatus, ':ls'=>$leadStatus,
                ':ih'=> ($role === 'vt_hired' ? 1 : 0), ':act'=>$active, ':id'=>$userId,
            ]);
            $state['stats']['vts']['updated']++;
        } else {
            $pdo->prepare(
                'INSERT INTO users (email, password_hash, role, first_name, last_name, full_name,
                                    phone, country, job_title, photo_url,
                                    hubspot_contact_id, vt_status, hs_lead_status,
                                    is_hired, active, notify_by_email)
                 VALUES (:e, :pwh, :r, :fn, :ln, :full,
                         :p, :c, :jt, :ph,
                         :hcid, :vs, :ls,
                         :ih, :act, 1)'
            )->execute([
                ':e'=>$email,
                ':pwh'=>password_hash(hs_default_password($role), PASSWORD_DEFAULT),
                ':r'=>$role, ':fn'=>$first, ':ln'=>$last, ':full'=>$full,
                ':p'=>$phone, ':c'=>$country, ':jt'=>$jobTitle, ':ph'=>$photoUrl,
                ':hcid'=>$contactId, ':vs'=>$vtStatus, ':ls'=>$leadStatus,
                ':ih'=> ($role === 'vt_hired' ? 1 : 0), ':act'=>$active,
            ]);
            $userId = (int) $pdo->lastInsertId();
            $state['stats']['vts']['created']++;
        }

        // Upsert vt_profiles row (one per user).
        $profileStatus = $role === 'vt_hired' ? 'hired' : 'onpool';
        $profileFields = [
            'status'                   => $profileStatus,
            'department'               => $department,
            'role_title'               => $roleTitle,
            'experience_years'         => $experienceYears,
            'english_level'            => $englishLevel,
            'iq_band'                  => $iqBand,
            'technical_band'           => $technicalBand,
            'primary_skills'           => $primarySkills,
            'predictive_index'         => $predictiveIndex,
            'quiz_tier'                => $quizTier,
            'engagement_score'         => $engagementScore,
            'predictive_contact_score' => $predictiveScore,
            'personality_profile'      => $personalityRaw,
            'ci_role'                  => $ciRole,
            'disc_profile'             => $discProfile,
            'hipaa_certified'          => $hipaaCert,
            'summary'                  => $summary,
            'experience_text'          => $experienceText,
            'resume_url'               => $resumeUrl,
            'video_url'                => $videoUrl,
            'workday_tracker_id'       => $workdayTrackerId,
            'workday_link'             => $workdayLink,
        ];
        $stmt = $pdo->prepare('SELECT id FROM vt_profiles WHERE user_id = :u LIMIT 1');
        $stmt->execute([':u' => $userId]);
        $vpId = (int) ($stmt->fetchColumn() ?: 0);
        if ($vpId > 0) {
            $sets = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($profileFields)));
            $params = [];
            foreach ($profileFields as $k => $v) { $params[":$k"] = $v; }
            $params[':id'] = $vpId;
            $pdo->prepare("UPDATE vt_profiles SET $sets, updated_at = CURRENT_TIMESTAMP WHERE id = :id")
                ->execute($params);
        } else {
            $cols = array_merge(['user_id'], array_keys($profileFields));
            $vals = array_merge([':user_id'], array_map(fn($k) => ":$k", array_keys($profileFields)));
            $params = [':user_id' => $userId];
            foreach ($profileFields as $k => $v) { $params[":$k"] = $v; }
            $pdo->prepare('INSERT INTO vt_profiles (' . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ')')
                ->execute($params);
        }

        // Dump every HubSpot property to vt_profile_meta for audit + future use.
        foreach ($props as $k => $v) {
            if (!is_string($k) || trim($k) === '') { continue; }
            $val = trim((string) $v);
            hs_upsert_vt_profile_meta($userId, $k, $val === '' ? null : $val);
        }
        if ($contactId !== '') {
            hs_upsert_vt_profile_meta($userId, 'hubspot_contact_id', $contactId);
        }

        // Queue media for Phase 5 to download. Empty URLs are skipped.
        foreach ([['photo', $photoUrl], ['resume', $resumeUrl], ['video', $videoUrl]] as [$kind, $url]) {
            if ($url === '') { continue; }
            $state['pending']['media'][] = ['user_id' => $userId, 'kind' => $kind, 'source_url' => $url];
        }

        $pdo->commit();
        audit_log('hs_talent_upsert', 'user', $userId, 'role=' . $role . ' contact=' . $contactId);
    } catch (Throwable $ex) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        throw $ex;
    }
}

/* ─── Phase 5: authenticated media downloader ─── */

/** Returns true if the URL is hosted on HubSpot and warrants the Bearer token. */
function hs_url_is_hubspot_hosted(string $url): bool
{
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    return $host !== '' && (
        str_contains($host, 'hubspot.com') ||
        str_contains($host, 'hubspotusercontent') ||
        str_contains($host, 'hubspot.net') ||
        str_contains($host, 'hubspotfeedback.com')
    );
}

/**
 * Optimum cURL options shared by the single- and parallel-download paths.
 * Tuned to prevent hangs and respect limits: transparent gzip, TCP keep-alive,
 * a hard ceiling timeout, and a low-speed guard that aborts a stalled transfer
 * (slower than HS_MEDIA_LOW_SPEED_BYTES/s for HS_MEDIA_LOW_SPEED_SECS) instead
 * of blocking until the full timeout elapses.
 */
function hs_media_curl_opts(string $url, array $headers): array
{
    return [
        CURLOPT_URL             => $url,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_FOLLOWLOCATION  => true,
        CURLOPT_MAXREDIRS       => 8,
        CURLOPT_TIMEOUT         => HS_MEDIA_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT  => HS_MEDIA_CONNECT_TIMEOUT,
        CURLOPT_LOW_SPEED_LIMIT => HS_MEDIA_LOW_SPEED_BYTES,
        CURLOPT_LOW_SPEED_TIME  => HS_MEDIA_LOW_SPEED_SECS,
        CURLOPT_ENCODING        => '',        // accept gzip/deflate transparently
        CURLOPT_TCP_KEEPALIVE   => 1,
        CURLOPT_HTTPHEADER      => $headers,
        CURLOPT_HEADER          => false,
        CURLOPT_FAILONERROR     => false,
    ];
}

/**
 * Resolve a media URL into a fetch plan WITHOUT touching the network:
 *   ['mode'=>'error', 'error'=>…]            bad input — skip
 *   ['mode'=>'embed', 'url'=>…]              store URL as-is, no download
 *   ['mode'=>'fetch', 'url'=>…, 'headers'=>…] download with these headers
 * Centralizes Drive-folder rejection, normalization, kind check, embed
 * detection, and the HubSpot Bearer-auth decision.
 */
function hs_media_prepare(string $url, string $kind, string $token): array
{
    if (preg_match('#drive\.google\.com/(?:drive/)?(?:[a-z0-9/]+/)?folders/#i', trim($url))) {
        return ['mode'=>'error', 'error'=>'Google Drive folder URL — needs a direct file link, not a folder'];
    }
    $norm = hs_normalize_media_url($url, $kind);
    if ($norm === '' || !preg_match('#^https?://#i', $norm)) {
        return ['mode'=>'error', 'error'=>'invalid url'];
    }
    if (!isset(hs_media_kind_map()[$kind])) {
        return ['mode'=>'error', 'error'=>'unknown kind'];
    }
    if ($kind === 'video' && hs_is_embedded_video_url($norm)) {
        return ['mode'=>'embed', 'url'=>$norm];
    }
    $headers = ['User-Agent: VT-Portal-Sync/1.0', 'Accept: */*'];
    if ($token !== '' && hs_url_is_hubspot_hosted($norm)) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    return ['mode'=>'fetch', 'url'=>$norm, 'headers'=>$headers];
}

/** True for fetch results worth one automatic retry (transient network/server). */
function hs_media_is_transient(array $r): bool
{
    if ((int) ($r['errno'] ?? 0) !== 0) { return true; }         // curl-level (timeout, reset)
    return in_array((int) ($r['http'] ?? 0), [429, 500, 502, 503, 504], true);
}

/**
 * Download many media URLs concurrently via curl_multi, capped at
 * HS_MEDIA_PARALLEL handles in flight. $reqs is a list of
 * ['key'=>mixed, 'url'=>string, 'headers'=>array]. Returns a map
 * key => ['ok'=>bool, 'body'=>?string, 'http'=>int, 'ctype'=>string,
 *         'errno'=>int, 'error'=>?string]. Transient failures are retried once.
 */
function hs_fetch_media_multi(array $reqs, int $concurrency = HS_MEDIA_PARALLEL): array
{
    $run = static function (array $reqs) use ($concurrency): array {
        $results = [];
        if (empty($reqs)) { return $results; }
        $conc  = max(1, min($concurrency, count($reqs)));
        $mh    = curl_multi_init();
        $queue = array_values($reqs);
        $live  = []; // (int)handle => key

        $add = static function () use (&$queue, &$live, $mh): void {
            if (empty($queue)) { return; }
            $req = array_shift($queue);
            $ch  = curl_init();
            curl_setopt_array($ch, hs_media_curl_opts($req['url'], $req['headers']));
            hs_apply_curl_ssl($ch);
            curl_multi_add_handle($mh, $ch);
            $live[(int) $ch] = $req['key'];
        };
        for ($i = 0; $i < $conc; $i++) { $add(); }

        do {
            curl_multi_exec($mh, $running);
            if ($running > 0) { curl_multi_select($mh, 1.0); }
            while ($info = curl_multi_info_read($mh)) {
                $ch    = $info['handle'];
                $key   = $live[(int) $ch] ?? null;
                $body  = curl_multi_getcontent($ch);
                $http  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $ctype = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                $errno = curl_errno($ch);
                $err   = curl_error($ch);
                if ($key !== null) {
                    if ($errno !== 0) {
                        $results[$key] = ['ok'=>false, 'body'=>null, 'http'=>$http, 'ctype'=>$ctype, 'errno'=>$errno, 'error'=>'curl: ' . $err];
                    } elseif ($http < 200 || $http >= 300 || !is_string($body) || $body === '') {
                        $results[$key] = ['ok'=>false, 'body'=>null, 'http'=>$http, 'ctype'=>$ctype, 'errno'=>0, 'error'=>'HTTP ' . $http];
                    } else {
                        $results[$key] = ['ok'=>true, 'body'=>$body, 'http'=>$http, 'ctype'=>$ctype, 'errno'=>0, 'error'=>null];
                    }
                }
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
                unset($live[(int) $ch]);
                $add(); // keep the pipe full
            }
        } while ($running > 0 || !empty($queue) || !empty($live));

        curl_multi_close($mh);
        return $results;
    };

    $results = $run($reqs);
    $retry = [];
    foreach ($reqs as $req) {
        $k = $req['key'];
        if (isset($results[$k]) && !$results[$k]['ok'] && hs_media_is_transient($results[$k])) {
            $retry[] = $req;
        }
    }
    if (!empty($retry)) {
        foreach ($run($retry) as $k => $r) { $results[$k] = $r; }
    }
    return $results;
}

/**
 * Validate downloaded bytes and persist to data/media/{entity}/{id}/{kind}.{ext}.
 * Returns the result struct used by both download paths. On a content mismatch
 * (e.g. an HTML login/interstitial page instead of a PDF/image) returns
 * ok=false with a descriptive error so the caller can fall back gracefully.
 *
 * @return array{ok:bool, served_url:string, ext:string, size:int, http:int, error:?string}
 */
function hs_media_write(string $body, int $code, string $ctype, string $url, string $entity, int $id, string $kind): array
{
    // Resolve the file extension: prefer URL path, fall back to Content-Type.
    $pathExt = strtolower(pathinfo((string) (parse_url($url, PHP_URL_PATH) ?: ''), PATHINFO_EXTENSION));
    $ctMain  = strtolower(trim(explode(';', $ctype)[0] ?? ''));
    $ctExt   = strtolower(preg_replace('#[^a-z0-9]#', '', explode('/', $ctMain)[1] ?? ''));
    if ($ctExt === 'jpeg') { $ctExt = 'jpg'; }
    $ext = $pathExt !== '' ? $pathExt : $ctExt;
    $allowed = hs_media_kind_map()[$kind];
    if (!in_array($ext, $allowed, true)) { $ext = $allowed[0]; }

    // An HTML body where we expected a binary file means a login / permission /
    // "virus scan" wall — never a real asset.
    $head = substr($body, 0, 512);
    $looksHtml = stripos($ctMain, 'text/html') !== false
              || stripos($head, '<!doctype html') !== false
              || stripos($head, '<html') !== false;

    if ($kind === 'photo') {
        $tmp = tmpfile();
        fwrite($tmp, $body);
        fflush($tmp);
        $meta = stream_get_meta_data($tmp);
        $info = @getimagesize($meta['uri']);
        fclose($tmp);
        if (!$info) {
            return ['ok'=>false, 'served_url'=>'', 'ext'=>'', 'size'=>strlen($body), 'http'=>$code, 'error'=>'not a valid image'];
        }
    } elseif ($kind === 'resume') {
        if ($looksHtml) {
            return ['ok'=>false, 'served_url'=>'', 'ext'=>'', 'size'=>strlen($body), 'http'=>$code, 'error'=>'received an HTML page, not a file (login/permission wall)'];
        }
        // PDF starts with %PDF. DOC/DOCX has a different signature — accept if extension is doc/docx.
        if ($ext === 'pdf' && substr($body, 0, 4) !== '%PDF') {
            return ['ok'=>false, 'served_url'=>'', 'ext'=>'', 'size'=>strlen($body), 'http'=>$code, 'error'=>'not a valid PDF (probably an HTML login page)'];
        }
    } elseif ($kind === 'video' && $looksHtml) {
        return ['ok'=>false, 'served_url'=>'', 'ext'=>'', 'size'=>strlen($body), 'http'=>$code, 'error'=>'received an HTML page, not a video file'];
    }

    $dir  = hs_media_dir($entity, $id, $kind);
    $file = $dir . '/' . $kind . '.' . $ext;
    if (file_put_contents($file, $body) === false) {
        return ['ok'=>false, 'served_url'=>'', 'ext'=>'', 'size'=>strlen($body), 'http'=>$code, 'error'=>'write failed'];
    }
    // Remove stale files for the same kind with a different extension.
    foreach (glob($dir . '/' . $kind . '.*') ?: [] as $existing) {
        if ($existing !== $file) { @unlink($existing); }
    }

    if ($kind === 'photo') { hs_make_thumb($file, $id); }   // 150x150 thumb alongside the original
    $served = hs_media_served_url($entity, $id, $kind, $ext);
    return ['ok'=>true, 'served_url'=>$served, 'ext'=>$ext, 'size'=>strlen($body), 'http'=>$code, 'error'=>null];
}

/**
 * Record that a media file now exists, by writing the locally-served URL plus
 * the source URL into the DB. The presence of the `index.php?p=media…` URL IS
 * the "file exists" record — re-syncs trust it instead of stat-ing the disk.
 */
function hs_media_persist(PDO $pdo, int $uid, string $kind, string $servedUrl, string $sourceUrl): void
{
    if ($kind === 'photo') {
        $pdo->prepare('UPDATE users SET photo_url = :u, photo_source_url = :s, updated_at = CURRENT_TIMESTAMP WHERE id = :id')
            ->execute([':u'=>$servedUrl, ':s'=>$sourceUrl, ':id'=>$uid]);
    } else {
        $col  = $kind === 'resume' ? 'resume_url'        : 'video_url';
        $scol = $kind === 'resume' ? 'resume_source_url' : 'video_source_url';
        $pdo->prepare("UPDATE vt_profiles SET {$col} = :u, {$scol} = :s, updated_at = CURRENT_TIMESTAMP WHERE user_id = :uid")
            ->execute([':u'=>$servedUrl, ':s'=>$sourceUrl, ':uid'=>$uid]);
    }
}

/**
 * Fallback persist: store ONLY the external link (leave *_source_url untouched
 * so a later sync still retries the real byte download). Used when we can't
 * fetch the file itself but at least want it reachable from the portal — the
 * resume/video views render an "Open external link" button for non-local URLs.
 */
function hs_media_persist_link_only(PDO $pdo, int $uid, string $kind, string $linkUrl): void
{
    if ($kind === 'photo') {
        $pdo->prepare('UPDATE users SET photo_url = :u, updated_at = CURRENT_TIMESTAMP WHERE id = :id')
            ->execute([':u'=>$linkUrl, ':id'=>$uid]);
    } else {
        $col = $kind === 'resume' ? 'resume_url' : 'video_url';
        $pdo->prepare("UPDATE vt_profiles SET {$col} = :u, updated_at = CURRENT_TIMESTAMP WHERE user_id = :uid")
            ->execute([':u'=>$linkUrl, ':uid'=>$uid]);
    }
}

/** Append a media problem row to the report's failed_urls list (capped at 50). */
function hs_media_log_problem(array &$state, array $p, string $reason): void
{
    if (count($state['stats']['media']['failed_urls'] ?? []) < 50) {
        $state['stats']['media']['failed_urls'][] = [
            'user_id' => $p['uid']          ?? 0,
            'name'    => $p['who']['name']  ?? '',
            'email'   => $p['who']['email'] ?? '',
            'kind'    => $p['kind']         ?? '',
            'url'     => $p['src']          ?? '',
            'reason'  => $reason,
        ];
    }
}

/**
 * Single-item download (ad-hoc path). The batch sync uses hs_fetch_media_multi;
 * this shares the same prepare + write helpers for a one-off fetch.
 *
 * @return array{ok:bool, served_url:string, ext:string, size:int, http:int, error:?string}
 */
function hs_download_media_with_auth(string $url, string $entity, int $id, string $kind, string $token): array
{
    $prep = hs_media_prepare($url, $kind, $token);
    if ($prep['mode'] === 'error') {
        return ['ok'=>false, 'served_url'=>'', 'ext'=>'', 'size'=>0, 'http'=>0, 'error'=>$prep['error']];
    }
    if ($prep['mode'] === 'embed') {
        return ['ok'=>true, 'served_url'=>$prep['url'], 'ext'=>'embed', 'size'=>0, 'http'=>200, 'error'=>null];
    }
    $ch = curl_init();
    curl_setopt_array($ch, hs_media_curl_opts($prep['url'], $prep['headers']));
    hs_apply_curl_ssl($ch);
    $body  = curl_exec($ch);
    $code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ctype = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $errno = curl_errno($ch);
    $err   = curl_error($ch);
    curl_close($ch);
    if ($errno !== 0) {
        return ['ok'=>false, 'served_url'=>'', 'ext'=>'', 'size'=>0, 'http'=>$code, 'error'=>'curl: ' . $err];
    }
    if ($code < 200 || $code >= 300 || !is_string($body) || $body === '') {
        return ['ok'=>false, 'served_url'=>'', 'ext'=>'', 'size'=>(int) strlen((string) $body), 'http'=>$code, 'error'=>'HTTP ' . $code];
    }
    return hs_media_write($body, $code, $ctype, $prep['url'], $entity, $id, $kind);
}

/**
 * Drain HS_MEDIA_BATCH items from state.pending.media in three phases:
 *   A) resolve identity + cache (DB only, no network, no disk stat) and build
 *      a per-item plan;
 *   B) download everything that needs fetching CONCURRENTLY (curl_multi);
 *   C) apply outcomes in order, with EXACTLY one array_shift + checkpoint per
 *      item so pending always drains.
 *
 * Resilience guarantees:
 *   - One bad file never stops the run: each item is isolated and, on failure,
 *     resumes/videos fall back to keeping the external link while photos are
 *     recorded as skipped. All problem items land in the summary report.
 *   - A file that keeps dying mid-import (attempts > HS_MEDIA_MAX_ATTEMPTS) is
 *     skipped permanently instead of blocking the queue.
 *   - The cache check is DB-only: a stored `index.php?p=media…` URL is the
 *     "file exists" record, so re-syncs never stat the filesystem.
 */
function hs_talent_step_download_media(array &$state, HubSpotClient $hs, array $settings): void
{
    if (empty($state['pending']['media'])) {
        hs_media_log_stage_summary($state, 'Media');
        hs_state_advance_stage($state);
        return;
    }
    // Are we even allowed to download? `hs_import_media=0` skips this stage.
    if (empty($settings['hs_import_media']) || (string) $settings['hs_import_media'] === '0') {
        hs_state_log($state, 'Media import disabled in settings — skipping.');
        $state['pending']['media'] = [];
        hs_state_advance_stage($state);
        return;
    }

    hs_raise_runtime_limits();
    $token = (string) ($settings['hs_token'] ?? '');
    $pdo   = db();
    // PEEK only — every item is array_shift'd + checkpointed individually in
    // Phase C, so a tick killed mid-batch resumes cleanly on the next item.
    $batch = array_slice($state['pending']['media'], 0, HS_MEDIA_BATCH);

    // Bulk-prefetch the stored source + served URL per kind in ONE query each
    // (instead of a SELECT per item) — the fast path for re-syncs full of cache
    // hits. Maps: uid => ['src'=>source_url, 'url'=>served_url].
    $photoIds = $vtIds = [];
    foreach ($batch as $item) {
        $uid = (int) ($item['user_id'] ?? 0);
        if ($uid < 1) { continue; }
        if (($item['kind'] ?? '') === 'photo') { $photoIds[$uid] = true; }
        else { $vtIds[$uid] = true; }
    }
    $photoCache = hs_media_prefetch_photo($pdo, array_keys($photoIds));
    $vtCache    = hs_media_prefetch_vt($pdo, array_keys($vtIds));

    // ── Phase A — plan each item (no network). Bump the per-item attempt
    //    counter on the REAL pending entry first and checkpoint, so a file that
    //    crashes the process still has its attempt recorded for next tick.
    $plan = [];
    foreach ($batch as $i => $item) {
        $state['pending']['media'][$i]['attempts'] = (int) ($state['pending']['media'][$i]['attempts'] ?? 0) + 1;
    }
    hs_talent_state_save($state);

    $toFetch = [];
    foreach ($batch as $i => $item) {
        try {
            $uid      = (int)    ($item['user_id'] ?? 0);
            $kind     = (string) ($item['kind'] ?? '');
            $src      = (string) ($item['source_url'] ?? '');
            $attempts = (int)    ($state['pending']['media'][$i]['attempts'] ?? 1);
            if ($uid < 1 || $src === '' || !in_array($kind, ['photo','resume','video'], true)) {
                $plan[$i] = ['action'=>'skip', 'uid'=>$uid, 'kind'=>$kind, 'src'=>$src,
                             'who'=>['name'=>'','email'=>'','tag'=>'user #'.$uid],
                             'reason'=>'invalid media item (missing id/url/kind)'];
                continue;
            }
            $who        = hs_user_label_for_log($pdo, $uid);
            $normalized = hs_normalize_media_url($src, $kind);
            $base = ['uid'=>$uid, 'kind'=>$kind, 'src'=>$src, 'who'=>$who, 'normalized'=>$normalized];

            // Give up on a file that keeps killing the tick mid-import.
            if ($attempts > HS_MEDIA_MAX_ATTEMPTS) {
                $plan[$i] = ['action'=>'skip'] + $base
                          + ['reason'=>"skipped after {$attempts} attempts (file keeps failing/stalling mid-import)"];
                continue;
            }

            // Cache (DB only): same source URL + a locally-served URL on record
            // means the file already exists — no network, no disk stat.
            $rec = $kind === 'photo' ? ($photoCache[$uid] ?? null) : ($vtCache[$uid] ?? null);
            $existingSource = $kind === 'photo' ? (string) ($rec['src'] ?? '')
                            : (string) ($rec[$kind . '_src'] ?? '');
            $existingUrl    = $kind === 'photo' ? (string) ($rec['url'] ?? '')
                            : (string) ($rec[$kind . '_url'] ?? '');
            if ($existingSource !== '' && $existingSource === $normalized
                && hs_media_url_is_local($existingUrl)) {
                $plan[$i] = ['action'=>'cache'] + $base;
                continue;
            }

            $prep = hs_media_prepare($src, $kind, $token);
            if ($prep['mode'] === 'error') {
                $plan[$i] = ['action'=>'fail', 'error'=>$prep['error'], 'http'=>0] + $base;
                continue;
            }
            if ($prep['mode'] === 'embed') {
                $plan[$i] = ['action'=>'embed', 'served'=>$prep['url']] + $base;
                continue;
            }
            $plan[$i]  = ['action'=>'fetch', 'prepUrl'=>$prep['url']] + $base;
            $toFetch[] = ['key'=>$i, 'url'=>$prep['url'], 'headers'=>$prep['headers']];
        } catch (Throwable $ex) {
            $plan[$i] = ['action'=>'skip', 'uid'=>(int)($item['user_id'] ?? 0), 'kind'=>(string)($item['kind'] ?? ''),
                         'src'=>(string)($item['source_url'] ?? ''),
                         'who'=>['name'=>'','email'=>'','tag'=>'user #'.(int)($item['user_id'] ?? 0)],
                         'reason'=>'planning error: ' . $ex->getMessage()];
        }
    }

    // ── Phase B — parallel download (bounded concurrency + one retry).
    $fetched = hs_fetch_media_multi($toFetch);

    // ── Phase C — apply outcomes in order; ALWAYS shift + checkpoint per item.
    foreach ($batch as $i => $item) {
        try {
            $p = $plan[$i] ?? ['action'=>'skip', 'uid'=>0, 'kind'=>'', 'src'=>'',
                               'who'=>['name'=>'','email'=>'','tag'=>'user #0'], 'reason'=>'no plan'];
            switch ($p['action']) {
                case 'cache':
                    $state['stats']['media']['cache_hits']++;
                    break;

                case 'skip':
                    $state['stats']['media']['skipped']++;
                    hs_media_log_problem($state, $p, 'SKIPPED — ' . ($p['reason'] ?? 'unknown'));
                    hs_state_log($state, "Media {$p['kind']} SKIPPED for {$p['who']['tag']}: " . ($p['reason'] ?? 'unknown'));
                    break;

                case 'embed':
                    hs_media_persist($pdo, $p['uid'], $p['kind'], $p['served'], $p['normalized']);
                    $state['stats']['media']['downloaded']++;
                    break;

                case 'fail':
                    hs_media_handle_failure($state, $pdo, $p, ($p['error'] ?: 'unknown') . ' (http=' . ($p['http'] ?? 0) . ')');
                    break;

                case 'fetch':
                    $fr = $fetched[$i] ?? ['ok'=>false, 'error'=>'no fetch result', 'http'=>0, 'body'=>null, 'ctype'=>''];
                    $w  = $fr['ok']
                        ? hs_media_write((string) $fr['body'], (int) $fr['http'], (string) $fr['ctype'], $p['prepUrl'], 'vt', $p['uid'], $p['kind'])
                        : ['ok'=>false, 'error'=>($fr['error'] ?: 'download failed'), 'http'=>(int) $fr['http']];
                    if (!$w['ok']) {
                        hs_media_handle_failure($state, $pdo, $p, ($w['error'] ?: 'unknown') . ' (http=' . ($w['http'] ?? 0) . ')');
                        break;
                    }
                    try {
                        hs_media_persist($pdo, $p['uid'], $p['kind'], $w['served_url'], $p['normalized']);
                        $state['stats']['media']['downloaded']++;
                    } catch (Throwable $ex) {
                        $state['stats']['media']['errors']++;
                        hs_media_log_problem($state, $p, 'DB write failed: ' . $ex->getMessage());
                        hs_state_log($state, "Media {$p['kind']} DB write failed for {$p['who']['tag']}: " . $ex->getMessage());
                    }
                    break;
            }
        } catch (Throwable $ex) {
            // Truly unexpected — never let it abort the batch.
            $state['stats']['media']['errors']++;
            $uid = (int) ($item['user_id'] ?? 0);
            hs_state_log($state, "Media item for user #{$uid} crashed: " . $ex->getMessage() . ' — continuing.');
        }
        // EXACTLY one drain + checkpoint per item, regardless of outcome above.
        // (The previous code skipped this on cache hits/failures via `continue`,
        // so a batch of all-cache-hits never drained → the run hung forever.)
        array_shift($state['pending']['media']);
        hs_talent_state_save($state);
    }

    if (empty($state['pending']['media'])) {
        hs_media_log_stage_summary($state, 'Media stage finished');
        hs_state_advance_stage($state);
    }
}

/** One-query prefetch of photo source + served URL for a set of user ids. */
function hs_media_prefetch_photo(PDO $pdo, array $ids): array
{
    $out = [];
    if (empty($ids)) { return $out; }
    try {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $st = $pdo->prepare("SELECT id, photo_source_url, photo_url FROM users WHERE id IN ($in)");
        $st->execute(array_values($ids));
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $out[(int) $row['id']] = ['src'=>(string) ($row['photo_source_url'] ?? ''), 'url'=>(string) ($row['photo_url'] ?? '')];
        }
    } catch (Throwable $ex) { /* fall back to per-item miss → re-download */ }
    return $out;
}

/** One-query prefetch of resume/video source + served URLs for user ids. */
function hs_media_prefetch_vt(PDO $pdo, array $ids): array
{
    $out = [];
    if (empty($ids)) { return $out; }
    try {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $st = $pdo->prepare("SELECT user_id, resume_source_url, resume_url, video_source_url, video_url FROM vt_profiles WHERE user_id IN ($in)");
        $st->execute(array_values($ids));
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $out[(int) $row['user_id']] = [
                'resume_src'=>(string) ($row['resume_source_url'] ?? ''), 'resume_url'=>(string) ($row['resume_url'] ?? ''),
                'video_src' =>(string) ($row['video_source_url'] ?? ''),  'video_url' =>(string) ($row['video_url'] ?? ''),
            ];
        }
    } catch (Throwable $ex) { /* fall back to per-item miss → re-download */ }
    return $out;
}

/**
 * Apply a failed download. "Find a way around" the error for resumes/videos by
 * keeping the external source URL so the file stays reachable from the portal
 * (the views render an "Open external link" button for non-local URLs), and
 * leave *_source_url untouched so a later sync retries the real byte download.
 * Photos have no useful external fallback → recorded as a hard error. Every
 * case is added to the summary report.
 */
function hs_media_handle_failure(array &$state, PDO $pdo, array $p, string $msg): void
{
    $kind = (string) ($p['kind'] ?? '');
    $src  = (string) ($p['src'] ?? '');
    if (($kind === 'resume' || $kind === 'video') && preg_match('#^https?://#i', $src)) {
        try {
            hs_media_persist_link_only($pdo, (int) $p['uid'], $kind, $src);
            $state['stats']['media']['fallbacks']++;
            hs_media_log_problem($state, $p, 'kept external link (could not fetch file): ' . $msg);
            hs_state_log($state, "Media {$kind} for {$p['who']['tag']} could not be fetched ({$msg}) — kept external link as fallback.");
            return;
        } catch (Throwable $ex) {
            // fall through to a hard failure
        }
    }
    $state['stats']['media']['errors']++;
    hs_media_log_problem($state, $p, $msg);
    hs_state_log($state, "Media {$kind} FAILED for {$p['who']['tag']}: {$msg}");
}

/** Emit the media tallies line used at stage start/finish. */
function hs_media_log_stage_summary(array &$state, string $label): void
{
    $m = $state['stats']['media'] ?? [];
    $d = (int) ($m['downloaded'] ?? 0);
    $h = (int) ($m['cache_hits'] ?? 0);
    $f = (int) ($m['fallbacks']  ?? 0);
    $s = (int) ($m['skipped']    ?? 0);
    $e = (int) ($m['errors']     ?? 0);
    hs_state_log($state, "{$label}: downloaded={$d}, cache_hits={$h}, fallback_links={$f}, skipped={$s}, errors={$e}.");
}

/* ─── Phase 6: single-fetch endpoints (talent + client) ─── */

/**
 * Search HubSpot contacts matching a query (email exact, then name LIKE).
 * Returns at most $limit lightweight matches so the UI can render a picker.
 */
function hs_talent_search(string $q, int $limit = 10): array
{
    $q = trim($q);
    if ($q === '') { return ['ok' => false, 'matches' => [], 'error' => 'Query is empty.']; }
    $settings = hs_settings();
    $token = (string) $settings['hs_token'];
    if ($token === '') { return ['ok' => false, 'matches' => [], 'error' => 'HubSpot token not configured.']; }
    $hs = new HubSpotClient($token);

    // Email LIKE — HubSpot CONTAINS_TOKEN matches the value as a token in the property.
    $isEmail = (bool) filter_var($q, FILTER_VALIDATE_EMAIL);
    $filterGroups = $isEmail
        ? [['filters' => [['propertyName'=>'email','operator'=>'EQ','value'=>strtolower($q)]]]]
        : [
            // Either firstname or lastname contains the token.
            ['filters' => [['propertyName'=>'firstname','operator'=>'CONTAINS_TOKEN','value'=>$q]]],
            ['filters' => [['propertyName'=>'lastname','operator'=>'CONTAINS_TOKEN','value'=>$q]]],
            ['filters' => [['propertyName'=>'email','operator'=>'CONTAINS_TOKEN','value'=>$q]]],
        ];
    $resp = $hs->request('POST', '/crm/v3/objects/contacts/search', [
        'filterGroups' => $filterGroups,
        'properties' => ['email','firstname','lastname','jobtitle','country','hs_lead_status','vt_status','hs_vt_status','contract_hired_status'],
        'limit' => $limit,
    ]);
    if (!$resp['ok']) {
        return ['ok' => false, 'matches' => [], 'error' => (string) ($resp['error'] ?: ('HTTP ' . $resp['status']))];
    }
    $matches = [];
    foreach ((array) ($resp['data']['results'] ?? []) as $c) {
        $p = is_array($c['properties'] ?? null) ? $c['properties'] : [];
        $matches[] = [
            'id'            => (string) ($c['id'] ?? ''),
            'email'         => (string) ($p['email'] ?? ''),
            'firstname'     => (string) ($p['firstname'] ?? ''),
            'lastname'      => (string) ($p['lastname'] ?? ''),
            'full_name'     => trim((string) ($p['firstname'] ?? '') . ' ' . (string) ($p['lastname'] ?? '')) ?: (string) ($p['email'] ?? ''),
            'jobtitle'      => (string) ($p['jobtitle'] ?? ''),
            'country'       => (string) ($p['country'] ?? ''),
            'hs_lead_status'=> (string) ($p['hs_lead_status'] ?? ''),
            'vt_status'     => (string) ($p['hs_vt_status'] ?? ($p['vt_status'] ?? '')),
            'contract_hired_status' => (string) ($p['contract_hired_status'] ?? ''),
        ];
    }
    return ['ok' => true, 'matches' => $matches, 'error' => null];
}

/**
 * Re-sync ONE HubSpot contact by ID through the same processor as the batched
 * talent sync, then download its media inline. Returns a per-record result.
 */
function hs_talent_sync_one(string $contactId): array
{
    $contactId = trim($contactId);
    if ($contactId === '') { return ['ok' => false, 'error' => 'contact_id is required.']; }
    $settings = hs_settings();
    $token = (string) $settings['hs_token'];
    if ($token === '') { return ['ok' => false, 'error' => 'HubSpot token not configured.']; }
    $hs = new HubSpotClient($token);

    // Pull the contact's full property bag so the role-mapper has everything.
    $objs = hs_batch_read_objects($hs, 'contacts', [$contactId], hs_vt_properties_full());
    $contact = $objs[$contactId] ?? null;
    if (!is_array($contact)) {
        return ['ok' => false, 'error' => 'Contact not found in HubSpot.'];
    }

    // Use a transient state struct so the per-record processor records stats locally.
    $state = hs_talent_state_default();
    $item  = [
        'id' => (string) ($contact['id'] ?? ''),
        'properties' => is_array($contact['properties'] ?? null) ? $contact['properties'] : [],
        'source_stage' => 'single_fetch',
    ];
    $action = 'unknown';
    try {
        hs_process_vt_contact($item, $state);
        if ($state['stats']['vts']['created'] > 0) { $action = 'created'; }
        elseif ($state['stats']['vts']['updated'] > 0) { $action = 'updated'; }
        elseif ($state['stats']['vts']['skipped_role'] > 0) { $action = 'skipped_role'; }
        elseif ($state['stats']['vts']['skipped_no_email'] > 0) { $action = 'skipped_no_email'; }
        elseif ($state['stats']['vts']['errors'] > 0) { $action = 'error'; }
    } catch (Throwable $ex) {
        return ['ok' => false, 'error' => $ex->getMessage(), 'matches' => []];
    }

    // Drain media queue synchronously — there are at most 3 per VT.
    $mediaSummary = ['downloaded' => 0, 'cache_hits' => 0, 'errors' => 0, 'failed_urls' => []];
    while (!empty($state['pending']['media'])) {
        $batchBefore = $state['stats']['media'];
        hs_talent_step_download_media($state, $hs, $settings);
        // Safety: detect no-progress loop.
        if ($batchBefore === $state['stats']['media'] && !empty($state['pending']['media'])) { break; }
    }
    $mediaSummary = $state['stats']['media'];

    // Resolve the user we just upserted so the UI can deep-link to them.
    $userId = 0;
    $email  = strtolower(trim((string) (($item['properties']['email'] ?? ''))));
    $found  = hs_find_user_for_contact($contactId, $email);
    if ($found) { $userId = (int) $found['id']; }

    return [
        'ok' => true, 'action' => $action, 'user_id' => $userId,
        'stats' => [
            'vts'   => $state['stats']['vts'],
            'media' => $mediaSummary,
        ],
        'log' => $state['messages'],
        'error' => null,
    ];
}

/** Search HubSpot companies by name token or domain. */
function hs_client_search(string $q, int $limit = 10): array
{
    $q = trim($q);
    if ($q === '') { return ['ok' => false, 'matches' => [], 'error' => 'Query is empty.']; }
    $settings = hs_settings();
    $token = (string) $settings['hs_token'];
    if ($token === '') { return ['ok' => false, 'matches' => [], 'error' => 'HubSpot token not configured.']; }
    $hs = new HubSpotClient($token);

    $filterGroups = [
        ['filters' => [['propertyName'=>'name','operator'=>'CONTAINS_TOKEN','value'=>$q]]],
        ['filters' => [['propertyName'=>'domain','operator'=>'CONTAINS_TOKEN','value'=>$q]]],
    ];
    $resp = $hs->request('POST', '/crm/v3/objects/companies/search', [
        'filterGroups' => $filterGroups,
        'properties' => ['name','domain','website','hs_lead_status','industry','hubspot_owner_id','csm'],
        'limit' => $limit,
    ]);
    if (!$resp['ok']) {
        return ['ok' => false, 'matches' => [], 'error' => (string) ($resp['error'] ?: ('HTTP ' . $resp['status']))];
    }
    $matches = [];
    foreach ((array) ($resp['data']['results'] ?? []) as $c) {
        $p = is_array($c['properties'] ?? null) ? $c['properties'] : [];
        $matches[] = [
            'id'             => (string) ($c['id'] ?? ''),
            'name'           => (string) ($p['name'] ?? ''),
            'domain'         => (string) ($p['domain'] ?? ''),
            'website'        => (string) ($p['website'] ?? ''),
            'industry'       => (string) ($p['industry'] ?? ''),
            'hs_lead_status' => (string) ($p['hs_lead_status'] ?? ''),
            'hubspot_owner_id'=>(string) ($p['hubspot_owner_id'] ?? ''),
            'csm'            => (string) ($p['csm'] ?? ''),
        ];
    }
    return ['ok' => true, 'matches' => $matches, 'error' => null];
}

/**
 * Re-sync ONE HubSpot company by ID. Runs the same per-record logic as the
 * batched client sync — fetch primary contact, resolve CSM, fetch Teammate-
 * labeled hired contacts, upsert clients + login user + company_profiles +
 * csm_clients + client_vts (authoritative).
 */
function hs_client_sync_one(string $companyId): array
{
    $companyId = trim($companyId);
    if ($companyId === '') { return ['ok' => false, 'error' => 'company_id is required.']; }
    $settings = hs_settings();
    $token = (string) $settings['hs_token'];
    if ($token === '') { return ['ok' => false, 'error' => 'HubSpot token not configured.']; }
    $hs = new HubSpotClient($token);

    // 1. Read the company.
    $objs = hs_batch_read_objects($hs, 'companies', [$companyId], hs_company_properties_full());
    $company = $objs[$companyId] ?? null;
    if (!is_array($company)) { return ['ok' => false, 'error' => 'Company not found in HubSpot.']; }
    $props = is_array($company['properties'] ?? null) ? $company['properties'] : [];

    // 2. Fetch companies→contacts assocs to pick Primary + collect hired Teammates.
    $assocResp = hs_batch_read_associations($hs, 'companies', 'contacts', [$companyId]);
    $assocRows = is_array($assocResp['map'][$companyId] ?? null) ? $assocResp['map'][$companyId] : [];
    $primaryCid = hs_pick_primary_contact_id($assocRows);

    $hiredContactIds = [];
    foreach ($assocRows as $row) {
        if (hs_assoc_is_hired_teammate(is_array($row['association_types'] ?? null) ? $row['association_types'] : [])) {
            $tid = (string) ($row['to_id'] ?? '');
            if ($tid !== '') { $hiredContactIds[$tid] = true; }
        }
    }

    // 3. Fetch Primary Contact full record (drives the client login user).
    $primary = null;
    if ($primaryCid !== '') {
        $pcObjs = hs_batch_read_objects($hs, 'contacts', [$primaryCid], [
            'email','firstname','lastname','phone','mobilephone','country','jobtitle',
        ]);
        $primary = $pcObjs[$primaryCid] ?? null;
    }

    // 4. Resolve CSM (owner-first, contact-fallback, then hubspot_owner_id).
    $state = hs_client_state_default(); // transient state for stats
    $csmUserId = 0;
    $csmRef = trim((string) ($props['csm'] ?? ''));
    $csmId  = '';
    if ($csmRef !== '' && preg_match('/\b(\d{3,})\b/', $csmRef, $m)) { $csmId = (string) $m[1]; }
    if ($csmId !== '') {
        $csmUserId = hs_resolve_owner_as_csm($csmId, $hs, $state);
        if ($csmUserId === 0) {
            $contactObjs = hs_batch_read_objects($hs, 'contacts', [$csmId], ['email','firstname','lastname','phone','country']);
            if (is_array($contactObjs[$csmId] ?? null)) {
                $csmUserId = hs_upsert_csm_from_contact_props(
                    is_array($contactObjs[$csmId]['properties'] ?? null) ? $contactObjs[$csmId]['properties'] : [],
                    $csmId, $state
                );
            }
        }
    }
    if ($csmUserId === 0) {
        $ownerId = trim((string) ($props['hubspot_owner_id'] ?? ''));
        if ($ownerId !== '') { $csmUserId = hs_resolve_owner_as_csm($ownerId, $hs, $state); }
    }

    // 5. Bulk-fetch hired contacts → upsert as vt_hired users so client_vts has rows to link.
    if ($hiredContactIds) {
        $hiredObjs = hs_batch_read_objects($hs, 'contacts', array_keys($hiredContactIds), [
            'email','firstname','lastname','phone','mobilephone','country','jobtitle',
            'hs_lead_status','vt_status','hs_vt_status','contract_hired_status',
        ]);
        foreach ($hiredObjs as $cid => $row) {
            // Push into the state.maps.hired_contact_ids list and call the talent
            // upsert helper indirectly by injecting a single-item state for it.
            $tmp = hs_client_state_default();
            $tmp['maps']['hired_contact_ids'] = [$cid => true];
            $tmp['maps']['hired_vt_seen'] = [];
            // Reuse the existing stage handler by giving it one batch of size 1.
            // It reads from maps.hired_contact_ids on its own.
            try {
                // The stage function loads from $tmp; do a single drain pass.
                hs_client_step_upsert_hired_vts($tmp, $hs);
            } catch (Throwable $_) {}
        }
    }

    // 6. Upsert the client row + login user + company_profiles.
    $state['maps'] = $state['maps'] ?? [];
    $state['maps']['client_id_by_company'] = [];
    try {
        hs_process_one_client($company, $primary, $state);
    } catch (Throwable $ex) {
        return ['ok' => false, 'error' => 'Client upsert failed: ' . $ex->getMessage()];
    }
    $clientId = (int) ($state['maps']['client_id_by_company'][$companyId] ?? 0);
    if ($clientId === 0) {
        return ['ok' => false, 'error' => 'Client row not created.', 'stats' => $state['stats']];
    }

    // 7. Authoritative association write for THIS company.
    $state['maps']['csm_user_by_company']      = [$companyId => $csmUserId];
    $state['maps']['hired_contacts_by_company']= [$companyId => $hiredContactIds];
    // Drain process_associations once (it processes HS_PROCESS_BATCH companies).
    hs_client_step_process_associations($state, $hs);

    return [
        'ok' => true,
        'client_id' => $clientId,
        'company_id' => $companyId,
        'primary_contact_id' => $primaryCid,
        'csm_user_id' => $csmUserId,
        'hired_contact_count' => count($hiredContactIds),
        'stats' => [
            'clients'       => $state['stats']['clients'] ?? null,
            'csms'          => $state['stats']['csms'] ?? null,
            'relationships' => $state['stats']['relationships'] ?? null,
        ],
        'error' => null,
    ];
}

/** Idempotent upsert for vt_profile_meta (key/value HubSpot property dump). */
function hs_upsert_vt_profile_meta(int $userId, string $key, ?string $value): void
{
    db()->prepare(
        "INSERT INTO vt_profile_meta (user_id, meta_key, meta_value, record_state, updated_at)
         VALUES (:u, :k, :v, 'active', CURRENT_TIMESTAMP)
         ON CONFLICT (user_id, meta_key, record_state)
         DO UPDATE SET meta_value = :v, updated_at = CURRENT_TIMESTAMP"
    )->execute([':u' => $userId, ':k' => $key, ':v' => $value]);
}

function hs_talent_control(string $action): array
{
    $state = hs_talent_state_load();
    switch ($action) {
        case 'start':
            if ($state['status'] === 'running') { break; }
            $state = hs_talent_state_default();
            $state['status'] = 'running';
            $state['stage']  = 'init';
            break;
        case 'pause':
            if ($state['status'] === 'running') { $state['status'] = 'paused'; }
            break;
        case 'resume':
            if (in_array($state['status'], ['paused', 'error'], true)) {
                $state['status']     = 'running';
                $state['last_error'] = null;
            }
            break;
        case 'reset':
            $state = hs_talent_state_default();
            break;
    }
    hs_talent_state_save($state);
    return $state;
}

/* ─── Client pipeline ─── */

function hs_client_step(): array
{
    hs_raise_runtime_limits();

    $state = hs_client_state_load();
    if ($state['status'] !== 'running') { return $state; }
    if ($state['stage'] === 'done') {
        $state['status'] = 'done';
        $state['finished_at'] = $state['finished_at'] ?? date('c');
        hs_client_state_save($state);
        return $state;
    }

    $settings = hs_settings();
    $hs = new HubSpotClient((string) $settings['hs_token']);
    $stageBefore = $state['stage'];

    try {
        switch ($state['stage']) {
            case 'init':                     hs_client_step_init($state); break;
            case 'fetch_companies':          hs_client_step_fetch_companies($state, $hs, $settings); break;
            case 'fetch_primary_contacts':   hs_client_step_fetch_primary_contacts($state, $hs); break;
            case 'fetch_contracts':          hs_client_step_fetch_contracts($state, $hs); break;
            case 'filter_first_day_complete':hs_client_step_filter_first_day_complete($state, $hs); break;
            case 'fetch_contract_contacts':  hs_client_step_fetch_contract_contacts($state, $hs); break;
            case 'fetch_owner_csms':         hs_client_step_fetch_owner_csms($state, $hs); break;
            case 'upsert_hired_vts':         hs_client_step_upsert_hired_vts($state, $hs); break;
            case 'process_clients':          hs_client_step_process_clients($state, $hs); break;
            case 'process_associations':     hs_client_step_process_associations($state, $hs); break;
            default:                         hs_state_advance_stage($state); break;
        }
        if (($state['stage'] ?? '') !== $stageBefore) {
            unset($state['stage_attempts'][$stageBefore]);
        }
    } catch (Throwable $ex) {
        hs_handle_stage_exception($state, $stageBefore, $ex);
    }

    if ($state['stage'] === 'done' && $state['status'] !== 'error') {
        $wasAlreadyDone = ($state['status'] === 'done');
        $state['status']      = 'done';
        $state['finished_at'] = date('c');
        $state['last_report'] = hs_build_report($state);
        hs_state_log($state, 'Client sync finished.');
        if (!$wasAlreadyDone) {
            hs_notify_sync_complete('client', $state);
        }
    }

    hs_client_state_save($state);
    return $state;
}

function hs_client_step_init(array &$state): void
{
    $state['started_at']   = date('c');
    $state['finished_at']  = null;
    $state['last_error']   = null;
    $state['after_cursor'] = null;
    $state['pending']      = hs_client_state_default()['pending'];
    $state['stats']        = hs_client_state_default()['stats'];
    $state['messages']     = [];
    $state['last_report']  = null;
    hs_state_log($state, 'Client sync started.');
    hs_state_advance_stage($state);
}

/* ─── Phase 4: client sync implementations
 *
 *   Algorithm (vtadmin parity + staging mu-plugin Primary Contact resolution):
 *     fetch_companies          → search companies w/ hs_lead_status = Client - Active
 *     fetch_primary_contacts   → batch companies->contacts associations; pick the
 *                                association whose label = "Primary"
 *     fetch_contracts          → batch companies->contracts (object 2-31153232)
 *     filter_first_day_complete→ batch-read contracts; keep only ones whose
 *                                hs_pipeline_stage == "First Day Complete"
 *     fetch_contract_contacts  → batch contracts->contacts; filter assoc typeId=28
 *                                (the "hired" association type per vtadmin)
 *     fetch_owner_csms         → resolve company.hubspot_owner_id or .csm to a CSM
 *                                user (upsert into users with role=csm)
 *     process_clients          → batch-read primary contact records; upsert
 *                                clients + login user + company_profiles
 *     process_associations     → write csm_clients + client_vts rows from the
 *                                resolved maps
 *
 *   All API calls use the HubSpot Bearer token via HubSpotClient.
 * ─── */

const HS_CONTRACT_OBJECT_TYPE       = '2-31153232';
const HS_HIRED_ASSOCIATION_TYPE_ID  = 28;
const HS_PRIMARY_CONTACT_LABEL_RX   = '/^(primary|primary contact|contact with primary company)$/i';

/** Settings fallback resolver — keeps the same defaults vtadmin uses. */
function hs_client_settings(array $settings): array
{
    return [
        'lead_field'  => $settings['hs_client_lead_status_field']  ?: 'hs_lead_status',
        'lead_value'  => $settings['hs_client_lead_status_value']  ?: 'Client - Active',
    ];
}

/** Properties to pull on each company (staging + vtadmin superset). */
function hs_company_properties_full(): array
{
    return array_values(array_unique([
        'name','domain','website','phone',
        'country','city','state','address','address_line_1','address_line_2',
        'industry','description','numberofemployees',
        'hs_lead_status','lifecyclestage',
        'hs_contact_email','company_email','billing_email','billing_contact_email',
        'hubspot_owner_id','csm',
    ]));
}

/** Primary-contact pick logic mirrors staging's pick_oldest_contact_id_by_label:
 *  pick the LOWEST-numbered contact id (= oldest) among associations whose
 *  label looks like "Primary". */
function hs_pick_primary_contact_id(array $assocRows): string
{
    $candidates = [];
    foreach ($assocRows as $row) {
        $assocTypes = is_array($row['association_types'] ?? null) ? $row['association_types'] : [];
        $matched = false;
        foreach ($assocTypes as $at) {
            $label = trim((string) ($at['label'] ?? ''));
            if ($label !== '' && preg_match(HS_PRIMARY_CONTACT_LABEL_RX, $label)) {
                $matched = true; break;
            }
        }
        if (!$matched) { continue; }
        $tid = trim((string) ($row['to_id'] ?? ''));
        if ($tid !== '') { $candidates[] = $tid; }
    }
    if (!$candidates) { return ''; }
    // Sort numerically — older IDs are smaller.
    usort($candidates, static fn($a, $b) => (int)$a <=> (int)$b);
    return (string) $candidates[0];
}

/** Stage 1: companies search, paginated 100 per page. */
function hs_client_step_fetch_companies(array &$state, HubSpotClient $hs, array $settings): void
{
    $cs = hs_client_settings($settings);
    $payload = [
        'filterGroups' => [['filters' => [[
            'propertyName' => $cs['lead_field'],
            'operator'     => 'EQ',
            'value'        => $cs['lead_value'],
        ]]]],
        'properties' => hs_company_properties_full(),
        'limit'      => 100,
    ];
    if (!empty($state['after_cursor'])) { $payload['after'] = (string) $state['after_cursor']; }

    $resp = $hs->request('POST', '/crm/v3/objects/companies/search', $payload);
    if (!$resp['ok']) {
        $state['status']     = 'error';
        $state['last_error'] = (string) ($resp['error'] ?: ('HTTP ' . $resp['status']));
        hs_state_log($state, 'Company fetch failed: ' . $state['last_error']);
        return;
    }
    $results = is_array($resp['data']['results'] ?? null) ? $resp['data']['results'] : [];
    foreach ($results as $co) {
        if (!is_array($co)) { continue; }
        $state['pending']['companies'][] = $co;
    }
    $state['stats']['fetched_total']['companies'] += count($results);

    $after = $resp['data']['paging']['next']['after'] ?? null;
    if ($after !== null && $after !== '') {
        $state['after_cursor'] = (string) $after;
        return;
    }
    hs_state_log($state, 'Fetched ' . $state['stats']['fetched_total']['companies'] . ' client companies.');
    $state['after_cursor'] = null;
    hs_state_advance_stage($state);
}

/** Helper: fetch the pipeline stage labels for a HubSpot object type so we
 *  can translate numeric stage IDs (e.g. 1016146705) to human-readable labels
 *  (e.g. "First Day Complete"). Mirrors staging's `fetch_hubspot_pipeline_stage_labels`.
 *
 *  Returns ['stage_id' => 'lowercased label', ...]. Empty array on failure. */
function hs_fetch_pipeline_stage_labels(HubSpotClient $hs, string $objectType): array
{
    $labels = [];
    $resp = $hs->request('GET', '/crm/v3/pipelines/' . rawurlencode($objectType), null);
    if (!$resp['ok']) { return $labels; }
    foreach ((array) ($resp['data']['results'] ?? []) as $pipeline) {
        foreach ((array) ($pipeline['stages'] ?? []) as $stage) {
            $id  = (string) ($stage['id'] ?? '');
            $lbl = strtolower(preg_replace('/\s+/', ' ', trim((string) ($stage['label'] ?? ''))));
            if ($id !== '' && $lbl !== '') { $labels[$id] = $lbl; }
        }
    }
    return $labels;
}

/** Returns true if an association_types array contains a "Teammate" / hired
 *  contact association (either label match or vtadmin's typeId 28). */
function hs_assoc_is_hired_teammate(array $assocTypes): bool
{
    foreach ($assocTypes as $at) {
        if (!is_array($at)) { continue; }
        $tid = (int) ($at['typeId'] ?? 0);
        if ($tid === HS_HIRED_ASSOCIATION_TYPE_ID || $tid === 108) { return true; }
        $label = strtolower(trim((string) ($at['label'] ?? '')));
        if ($label !== '' && (str_contains($label, 'teammate') || str_contains($label, 'hired'))) { return true; }
    }
    return false;
}

/** Helper: HubSpot v4 batch association read. */
function hs_batch_read_associations(HubSpotClient $hs, string $fromType, string $toType, array $fromIds): array
{
    if (!$fromIds) { return ['map' => [], 'ok' => true]; }
    $inputs = array_values(array_map(static fn($id) => ['id' => (string) $id], $fromIds));
    $path = '/crm/v4/associations/' . rawurlencode($fromType) . '/' . rawurlencode($toType) . '/batch/read';
    $resp = $hs->request('POST', $path, ['inputs' => $inputs]);
    if (!$resp['ok']) { return ['map' => [], 'ok' => false, 'error' => $resp['error']]; }
    $map = [];
    $results = is_array($resp['data']['results'] ?? null) ? $resp['data']['results'] : [];
    foreach ($results as $r) {
        $from = (string) ($r['from']['id'] ?? '');
        if ($from === '') { continue; }
        $toResults = is_array($r['to'] ?? null) ? $r['to'] : [];
        $rows = [];
        foreach ($toResults as $tr) {
            $rows[] = [
                'to_id'             => (string) ($tr['toObjectId'] ?? ''),
                'association_types' => is_array($tr['associationTypes'] ?? null) ? $tr['associationTypes'] : [],
            ];
        }
        $map[$from] = $rows;
    }
    return ['map' => $map, 'ok' => true];
}

/** Helper: HubSpot v3 batch object read. */
function hs_batch_read_objects(HubSpotClient $hs, string $objectType, array $ids, array $properties): array
{
    if (!$ids) { return []; }
    $inputs = array_values(array_map(static fn($id) => ['id' => (string) $id], $ids));
    $path = '/crm/v3/objects/' . rawurlencode($objectType) . '/batch/read';
    $resp = $hs->request('POST', $path, ['inputs' => $inputs, 'properties' => array_values(array_unique($properties))]);
    if (!$resp['ok']) { return []; }
    $out = [];
    foreach ((array) ($resp['data']['results'] ?? []) as $obj) {
        $id = (string) ($obj['id'] ?? '');
        if ($id !== '') { $out[$id] = is_array($obj) ? $obj : []; }
    }
    return $out;
}

/** Stage 2: companies → contacts associations; pick Primary per company AND
 *  collect hired Teammate-labeled contacts in the same pass (one API call
 *  serves two purposes, and the Teammate label is the actual hired indicator
 *  in this HubSpot tenant). */
function hs_client_step_fetch_primary_contacts(array &$state, HubSpotClient $hs): void
{
    // Drain companies that don't yet have their primary resolved.
    $maps = $state['maps'] ?? [];
    $maps['primary_contact_by_company']    = $maps['primary_contact_by_company']    ?? [];
    $maps['primary_contact_ids']           = $maps['primary_contact_ids']           ?? [];
    $maps['hired_contacts_by_company']     = $maps['hired_contacts_by_company']     ?? [];
    $maps['hired_contact_ids']             = $maps['hired_contact_ids']             ?? [];

    $companyIdsAll = [];
    foreach ($state['pending']['companies'] as $co) {
        $cid = (string) ($co['id'] ?? '');
        if ($cid !== '' && !isset($maps['primary_contact_by_company'][$cid])) {
            $companyIdsAll[] = $cid;
        }
    }
    if (!$companyIdsAll) {
        hs_state_log($state, 'No companies pending primary-contact resolution.');
        $state['maps'] = $maps;
        hs_state_advance_stage($state);
        return;
    }
    $batch = array_slice($companyIdsAll, 0, HS_PROCESS_BATCH);

    $resp = hs_batch_read_associations($hs, 'companies', 'contacts', $batch);
    if (!$resp['ok']) {
        $state['stats']['relationships']['errors']++;
        hs_state_log($state, 'companies->contacts batch failed: ' . ($resp['error'] ?? ''));
        // Still mark them resolved (empty) so we move on.
    }
    foreach ($batch as $cid) {
        $rows = is_array($resp['map'][$cid] ?? null) ? $resp['map'][$cid] : [];
        // (a) Pick Primary Contact for the client login user.
        $primary = hs_pick_primary_contact_id($rows);
        $maps['primary_contact_by_company'][$cid] = $primary;
        if ($primary !== '') { $maps['primary_contact_ids'][$primary] = true; }

        // (b) Same pass: any contact tagged "Teammate" / hired association
        //     becomes a candidate for client_vts. This catches your actual
        //     data shape (typeId 108 / label "Teammate") without depending
        //     on the Contracts pipeline being properly configured.
        foreach ($rows as $row) {
            $assocTypes = is_array($row['association_types'] ?? null) ? $row['association_types'] : [];
            if (!hs_assoc_is_hired_teammate($assocTypes)) { continue; }
            $contactId = trim((string) ($row['to_id'] ?? ''));
            if ($contactId === '') { continue; }
            if (!isset($maps['hired_contacts_by_company'][$cid])) { $maps['hired_contacts_by_company'][$cid] = []; }
            $maps['hired_contacts_by_company'][$cid][$contactId] = true;
            $maps['hired_contact_ids'][$contactId] = true;
        }
    }
    $state['maps'] = $maps;

    // Done?
    $unresolved = 0;
    foreach ($state['pending']['companies'] as $co) {
        $cid = (string) ($co['id'] ?? '');
        if ($cid !== '' && !isset($maps['primary_contact_by_company'][$cid])) { $unresolved++; }
    }
    if ($unresolved === 0) {
        $picked = count(array_filter($maps['primary_contact_by_company'], static fn($v) => $v !== ''));
        hs_state_log($state, 'Primary contacts resolved for ' . $picked . '/' . count($maps['primary_contact_by_company']) . ' companies.');
        hs_state_advance_stage($state);
    }
}

/** Stage 3: companies → contracts associations. */
function hs_client_step_fetch_contracts(array &$state, HubSpotClient $hs): void
{
    $maps = $state['maps'] ?? [];
    $maps['contracts_by_company'] = $maps['contracts_by_company'] ?? [];
    $maps['contract_ids']         = $maps['contract_ids'] ?? [];

    $companyIdsAll = [];
    foreach ($state['pending']['companies'] as $co) {
        $cid = (string) ($co['id'] ?? '');
        if ($cid !== '' && !isset($maps['contracts_by_company'][$cid])) {
            $companyIdsAll[] = $cid;
        }
    }
    if (!$companyIdsAll) {
        hs_state_log($state, 'No companies pending contract resolution.');
        $state['maps'] = $maps;
        hs_state_advance_stage($state);
        return;
    }
    $batch = array_slice($companyIdsAll, 0, HS_PROCESS_BATCH);

    $resp = hs_batch_read_associations($hs, 'companies', HS_CONTRACT_OBJECT_TYPE, $batch);
    if (!$resp['ok']) {
        $state['stats']['relationships']['errors']++;
        hs_state_log($state, 'companies->contracts batch failed: ' . ($resp['error'] ?? ''));
    }
    foreach ($batch as $cid) {
        $rows = is_array($resp['map'][$cid] ?? null) ? $resp['map'][$cid] : [];
        $cids = [];
        foreach ($rows as $r) {
            $tid = (string) ($r['to_id'] ?? '');
            if ($tid === '') { continue; }
            $cids[] = $tid;
            $maps['contract_ids'][$tid] = true;
        }
        $maps['contracts_by_company'][$cid] = $cids;
    }
    $state['stats']['fetched_total']['contracts'] = count($maps['contract_ids']);
    $state['maps'] = $maps;

    $unresolved = 0;
    foreach ($state['pending']['companies'] as $co) {
        $cid = (string) ($co['id'] ?? '');
        if ($cid !== '' && !isset($maps['contracts_by_company'][$cid])) { $unresolved++; }
    }
    if ($unresolved === 0) {
        hs_state_log($state, 'Found ' . count($maps['contract_ids']) . ' contract objects across ' . count($maps['contracts_by_company']) . ' companies.');
        hs_state_advance_stage($state);
    }
}

/** Stage 4: batch-read contracts, translate numeric stage IDs via the pipeline
 *  stage-label map, keep only ones whose label == "first day complete". */
function hs_client_step_filter_first_day_complete(array &$state, HubSpotClient $hs): void
{
    $maps = $state['maps'] ?? [];
    $maps['first_day_contract_ids'] = $maps['first_day_contract_ids'] ?? [];
    $maps['contract_seen']          = $maps['contract_seen'] ?? [];
    // contract_id => ['start' => 'YYYY-MM-DD'|'', 'end' => 'YYYY-MM-DD'|''].
    // Populated here so the assoc step can stamp client_vts.started_at /
    // ended_at with the real HubSpot dates instead of the sync timestamp.
    $maps['contract_dates']         = $maps['contract_dates']         ?? [];
    // contract_id => ['id' => string, 'link' => string]. Workday tracker
    // properties live on the CONTRACT object, not the contact — so the
    // same VT can have different trackers for different clients. Threaded
    // into client_vts via the assoc step.
    $maps['contract_workday']       = $maps['contract_workday']       ?? [];

    // Resolve numeric stage ID -> label once per run; cache in maps.
    if (!isset($maps['contract_stage_labels'])) {
        $maps['contract_stage_labels'] = hs_fetch_pipeline_stage_labels($hs, HS_CONTRACT_OBJECT_TYPE);
        hs_state_log($state, 'Resolved ' . count($maps['contract_stage_labels']) . ' contract pipeline stage labels.');
    }
    $labels = $maps['contract_stage_labels'];

    $allIds = array_keys($maps['contract_ids'] ?? []);
    $pending = [];
    foreach ($allIds as $id) {
        if (!isset($maps['contract_seen'][$id])) { $pending[] = $id; }
    }
    if (!$pending) {
        $state['stats']['fetched_total']['first_day_contracts'] = count($maps['first_day_contract_ids']);
        hs_state_log($state, 'First-Day-Complete filter: ' . count($maps['first_day_contract_ids']) . ' contracts pass.');
        $state['maps'] = $maps;
        hs_state_advance_stage($state);
        return;
    }
    $batch = array_slice($pending, 0, HS_PROCESS_BATCH);
    $objs = hs_batch_read_objects($hs, HS_CONTRACT_OBJECT_TYPE, $batch, [
        'hs_pipeline_stage','pipeline_stage','contract_hired_status','pipeline_stage_label','hs_pipeline_stage_label','hs_object_id',
        // Contract dates — same property-name fallbacks vtadmin uses
        // (includes/hubspot_sync.php lines 2022 + 2027): try the canonical
        // names first, then progressively more generic ones.
        'contract_start_date','start_date','contract_start','service_start_date',
        'date___end_of_service','end_of_service_date','contract_end_date','service_end_date',
        // Workday tracker — staging mu-plugin's full candidate list
        // (vt-hubspot-user-sync.php lines 9487-9508). Either a direct link
        // or just the report id; we derive whichever's missing below.
        'vt_wdt_link','vt_workday_tracker_link','vt_hired_workday_tracker_link',
        'vt_wdt_url','workday_tracker_link','workday_report_url','workdaytracker_report_url',
        'vt_hired_workday_tracker_id','vt_hired_workday_report_id','vt_workday_tracker_id',
        'workday_report_id','workdaytracker_report_id','workday_tracker_report_id','workday_tracker_id',
    ]);
    // Normalize a HubSpot date property — either an epoch-ms string or a
    // 'YYYY-MM-DD' / ISO date — into 'YYYY-MM-DD' so SQLite text comparison
    // and our display layer treat it consistently.
    $normalizeDate = static function ($v): string {
        $v = trim((string) $v);
        if ($v === '') return '';
        if (ctype_digit($v) && strlen($v) >= 10) {
            // Epoch milliseconds → seconds.
            $ts = (int) substr($v, 0, 10);
            return $ts > 0 ? gmdate('Y-m-d', $ts) : '';
        }
        $t = strtotime($v);
        return $t ? gmdate('Y-m-d', $t) : substr($v, 0, 10);
    };
    foreach ($batch as $id) {
        $maps['contract_seen'][$id] = true;
        $row = $objs[$id] ?? null;
        if (!is_array($row)) { continue; }
        $props = is_array($row['properties'] ?? null) ? $row['properties'] : [];

        // Try resolving from label fields first (string), then numeric stage id via lookup.
        $stageLbl = strtolower(preg_replace('/\s+/', ' ', trim(
            (string) ($props['pipeline_stage_label']
                   ?? $props['hs_pipeline_stage_label']
                   ?? $props['contract_hired_status']
                   ?? '')
        )));
        if ($stageLbl === '') {
            $numeric = trim((string) ($props['hs_pipeline_stage'] ?? $props['pipeline_stage'] ?? ''));
            if ($numeric !== '' && isset($labels[$numeric])) {
                $stageLbl = $labels[$numeric];
            }
        }
        if ($stageLbl === 'first day complete') {
            $maps['first_day_contract_ids'][$id] = true;
        }

        // Cache the contract's start + end dates regardless of stage so a
        // contract that subsequently moves to EOS still resolves correctly.
        $start = '';
        foreach (['contract_start_date','start_date','contract_start','service_start_date'] as $k) {
            if (!empty($props[$k])) { $start = $normalizeDate($props[$k]); if ($start !== '') break; }
        }
        $end = '';
        foreach (['date___end_of_service','end_of_service_date','contract_end_date','service_end_date'] as $k) {
            if (!empty($props[$k])) { $end = $normalizeDate($props[$k]); if ($end !== '') break; }
        }
        $maps['contract_dates'][$id] = ['start' => $start, 'end' => $end];

        // Workday tracker — pull link + id from any candidate property.
        $wdLink = '';
        foreach (['vt_wdt_link','vt_workday_tracker_link','vt_hired_workday_tracker_link',
                  'vt_wdt_url','workday_tracker_link','workday_report_url','workdaytracker_report_url'] as $k) {
            if (!empty($props[$k])) { $wdLink = trim((string) $props[$k]); break; }
        }
        $wdId = '';
        foreach (['vt_hired_workday_tracker_id','vt_hired_workday_report_id','vt_workday_tracker_id',
                  'workday_report_id','workdaytracker_report_id','workday_tracker_report_id','workday_tracker_id'] as $k) {
            if (!empty($props[$k])) { $wdId = trim((string) $props[$k]); break; }
        }
        // If link has the canonical workdaytracker.com/app/public-report/{id}
        // shape, pull the id from it when the id field was empty.
        if ($wdId === '' && $wdLink !== '') { $wdId = hs_extract_workday_report_id($wdLink); }
        // Always derive a canonical link from the id when we have one.
        if ($wdId !== '') { $wdLink = hs_build_workday_url($wdId); }
        if ($wdId !== '' || $wdLink !== '') {
            $maps['contract_workday'][$id] = ['id' => $wdId, 'link' => $wdLink];
        }
    }
    $state['maps'] = $maps;
}

/** Mirror of staging's extract_workday_report_id_from_value()
 *  (vt-hubspot-user-sync.php lines 9615-9627). */
function hs_extract_workday_report_id(string $raw): string
{
    $v = trim($raw);
    if ($v === '') return '';
    if (preg_match('#workdaytracker\.com/app/public-report/([^/?]+)#i', $v, $m)) {
        return trim($m[1]);
    }
    return preg_replace('/\s+/', '', $v) ?: '';
}

/** Mirror of staging's build_workday_tracker_url()
 *  (vt-hubspot-user-sync.php lines 9605-9613). Canonical form:
 *  https://workdaytracker.com/app/public-report/{id}/ */
function hs_build_workday_url(string $id): string
{
    $id = hs_extract_workday_report_id($id);
    if ($id === '') return '';
    return 'https://workdaytracker.com/app/public-report/' . rawurlencode($id) . '/';
}

/** Stage 5: contracts → contacts; filter assoc typeId=28 (hired). */
function hs_client_step_fetch_contract_contacts(array &$state, HubSpotClient $hs): void
{
    $maps = $state['maps'] ?? [];
    $maps['hired_contacts_by_company'] = $maps['hired_contacts_by_company'] ?? [];
    $maps['contract_processed']        = $maps['contract_processed'] ?? [];
    $maps['hired_contact_ids']         = $maps['hired_contact_ids'] ?? [];
    // "compId|contactId" => ['start' => '', 'end' => ''] — pulled from the
    // contract that linked this (company, contact) pair so the assoc step
    // can stamp client_vts.started_at / ended_at with real HubSpot dates.
    $maps['vt_link_dates']             = $maps['vt_link_dates']             ?? [];
    // "compId|contactId" => ['id' => '', 'link' => ''] — workday tracker
    // info from the matching contract, written to client_vts.workday_*.
    $maps['vt_link_workday']           = $maps['vt_link_workday']           ?? [];

    $allIds = array_keys($maps['first_day_contract_ids'] ?? []);
    $pending = [];
    foreach ($allIds as $id) {
        if (!isset($maps['contract_processed'][$id])) { $pending[] = $id; }
    }
    if (!$pending) {
        $state['stats']['fetched_total']['hired_contacts'] = count($maps['hired_contact_ids']);
        $companyCount = count(array_filter($maps['hired_contacts_by_company'], static fn($l) => !empty($l)));
        hs_state_log($state, count($maps['hired_contact_ids']) . ' hired contacts found across ' . $companyCount . ' companies.');
        $state['maps'] = $maps;
        hs_state_advance_stage($state);
        return;
    }
    $batch = array_slice($pending, 0, HS_PROCESS_BATCH);
    $resp = hs_batch_read_associations($hs, HS_CONTRACT_OBJECT_TYPE, 'contacts', $batch);

    // Reverse-lookup: which company owns which contract?
    $companyByContract = [];
    foreach (($maps['contracts_by_company'] ?? []) as $compId => $contractIds) {
        foreach ((array) $contractIds as $cid) { $companyByContract[(string)$cid] = (string)$compId; }
    }

    foreach ($batch as $contractId) {
        $maps['contract_processed'][$contractId] = true;
        $rows = is_array($resp['map'][$contractId] ?? null) ? $resp['map'][$contractId] : [];
        $compId = $companyByContract[$contractId] ?? '';
        if ($compId === '') { continue; }
        // Dates + workday of THIS contract — attached to each (company,contact)
        // pair this contract introduces. If a contact has multiple contracts
        // across history we keep the EARLIEST start_date, LATEST end_date
        // (or null when any contract is still active), and the workday from
        // whichever contract is most recently associated (last write wins
        // since contracts are processed roughly in creation order).
        $dates = $maps['contract_dates'][$contractId]   ?? ['start' => '', 'end' => ''];
        $wd    = $maps['contract_workday'][$contractId] ?? null;
        foreach ($rows as $row) {
            $assocTypes = is_array($row['association_types'] ?? null) ? $row['association_types'] : [];
            if (!hs_assoc_is_hired_teammate($assocTypes)) { continue; }
            $contactId = (string) ($row['to_id'] ?? '');
            if ($contactId === '') { continue; }
            if (!isset($maps['hired_contacts_by_company'][$compId])) {
                $maps['hired_contacts_by_company'][$compId] = [];
            }
            $maps['hired_contacts_by_company'][$compId][$contactId] = true;
            $maps['hired_contact_ids'][$contactId] = true;

            // Reconcile dates across multiple contracts for the same VT/company.
            $key = $compId . '|' . $contactId;
            $cur = $maps['vt_link_dates'][$key] ?? null;
            if ($cur === null) {
                $maps['vt_link_dates'][$key] = $dates;
            } else {
                if ($dates['start'] !== '' && ($cur['start'] === '' || $dates['start'] < $cur['start'])) {
                    $cur['start'] = $dates['start'];
                }
                if ($cur['end'] === '' || $dates['end'] === '') {
                    $cur['end'] = '';
                } elseif ($dates['end'] > $cur['end']) {
                    $cur['end'] = $dates['end'];
                }
                $maps['vt_link_dates'][$key] = $cur;
            }
            // Stash workday — keep the first non-empty (an existing tracker
            // is more authoritative than later renewal contracts that
            // may not have it set yet).
            if ($wd !== null && empty($maps['vt_link_workday'][$key])) {
                $maps['vt_link_workday'][$key] = $wd;
            }
        }
    }
    $state['maps'] = $maps;
}

/** Stage 6: for each company, resolve its CSM (csm contact-id property OR
 *  hubspot_owner_id Owner) and upsert into users as role=csm. */
function hs_client_step_fetch_owner_csms(array &$state, HubSpotClient $hs): void
{
    $maps = $state['maps'] ?? [];
    $maps['csm_user_by_company'] = $maps['csm_user_by_company'] ?? [];
    $maps['company_seen_for_csm'] = $maps['company_seen_for_csm'] ?? [];

    $pendingCompanies = [];
    foreach ($state['pending']['companies'] as $co) {
        $cid = (string) ($co['id'] ?? '');
        if ($cid === '' || isset($maps['company_seen_for_csm'][$cid])) { continue; }
        $pendingCompanies[] = $co;
    }
    if (!$pendingCompanies) {
        $resolved = count(array_filter($maps['csm_user_by_company'], static fn($v) => (int)$v > 0));
        hs_state_log($state, "CSM resolution done: {$resolved}/" . count($maps['company_seen_for_csm']) . ' companies linked.');
        $state['maps'] = $maps;
        hs_state_advance_stage($state);
        return;
    }
    $batch = array_slice($pendingCompanies, 0, HS_PROCESS_BATCH);
    foreach ($batch as $co) {
        $cid   = (string) ($co['id'] ?? '');
        $props = is_array($co['properties'] ?? null) ? $co['properties'] : [];
        $maps['company_seen_for_csm'][$cid] = true;

        $csmUserId = 0;
        // The `csm` property in this tenant typically holds a HubSpot OWNER id
        // (e.g. 86501355), not a Contact id. Resolve order:
        //   1) csm-property → try Owners API first, then Contacts API as fallback
        //   2) hubspot_owner_id (the company's HubSpot Owner) → Owners API
        $csmRef = trim((string) ($props['csm'] ?? ''));
        $csmId  = '';
        if ($csmRef !== '' && preg_match('/\b(\d{3,})\b/', $csmRef, $m)) {
            $csmId = (string) $m[1];
        }
        if ($csmId !== '') {
            // Try Owners API first.
            $csmUserId = hs_resolve_owner_as_csm($csmId, $hs, $state);
            // If not an owner, try treating it as a Contact.
            if ($csmUserId === 0) {
                $contactObjs = hs_batch_read_objects($hs, 'contacts', [$csmId], ['email','firstname','lastname','phone','country']);
                $row = $contactObjs[$csmId] ?? null;
                if (is_array($row)) {
                    $csmUserId = hs_upsert_csm_from_contact_props(
                        is_array($row['properties'] ?? null) ? $row['properties'] : [],
                        $csmId,
                        $state
                    );
                }
            }
        }
        // Fall back to the company's HubSpot Owner if `csm` didn't resolve.
        if ($csmUserId === 0) {
            $ownerId = trim((string) ($props['hubspot_owner_id'] ?? ''));
            if ($ownerId !== '') {
                $csmUserId = hs_resolve_owner_as_csm($ownerId, $hs, $state);
            }
        }
        $maps['csm_user_by_company'][$cid] = $csmUserId;
    }
    $state['maps'] = $maps;
}

/** Upsert a CSM user from contact properties. Returns user id (0 on failure). */
function hs_upsert_csm_from_contact_props(array $props, string $contactId, array &$state): int
{
    $pdo   = db();
    $email = strtolower(trim((string) ($props['email'] ?? '')));
    if ($email === '') { return 0; }
    $first = trim((string) ($props['firstname'] ?? ''));
    $last  = trim((string) ($props['lastname']  ?? ''));
    $phone = trim((string) ($props['phone'] ?? ($props['mobilephone'] ?? '')));
    $country = trim((string) ($props['country'] ?? ''));
    $full  = trim($first . ' ' . $last) ?: $email;

    $stmt = $pdo->prepare('SELECT id, role FROM users WHERE email = :e LIMIT 1');
    $stmt->execute([':e' => $email]);
    if ($u = $stmt->fetch()) {
        $newRole = $u['role'] === 'super_admin' ? 'super_admin' : 'csm';
        $pdo->prepare(
            'UPDATE users SET role = :r, first_name = :fn, last_name = :ln, full_name = :full,
                              phone = :p, country = :c, hubspot_contact_id = :hcid,
                              updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        )->execute([
            ':r' => $newRole, ':fn' => $first, ':ln' => $last, ':full' => $full,
            ':p' => $phone, ':c' => $country, ':hcid' => $contactId, ':id' => $u['id'],
        ]);
        $state['stats']['csms']['updated']++;
        return (int) $u['id'];
    }
    $pdo->prepare(
        "INSERT INTO users (email, password_hash, role, first_name, last_name, full_name,
                            phone, country, hubspot_contact_id, active, notify_by_email)
         VALUES (:e, :h, 'csm', :fn, :ln, :full, :p, :c, :hcid, 1, 1)"
    )->execute([
        ':e' => $email, ':h' => password_hash(hs_default_password('csm'), PASSWORD_DEFAULT),
        ':fn' => $first, ':ln' => $last, ':full' => $full, ':p' => $phone, ':c' => $country,
        ':hcid' => $contactId,
    ]);
    $state['stats']['csms']['created']++;
    return (int) $pdo->lastInsertId();
}

/** Stage 7 (NEW): batch-fetch each hired contact and upsert it as a vt_hired
 *  user so the association stage finds a matching local user row. This makes
 *  the client sync self-sufficient — it doesn't require the talent sync to
 *  have run first for the same contacts. (Same pattern vtadmin uses inside
 *  `sync_hubspot_client_profiles`'s upsertContactUser closure.) */
function hs_client_step_upsert_hired_vts(array &$state, HubSpotClient $hs): void
{
    $maps = $state['maps'] ?? [];
    $hired = array_keys((array) ($maps['hired_contact_ids'] ?? []));
    $maps['hired_vt_seen'] = $maps['hired_vt_seen'] ?? [];

    $pending = [];
    foreach ($hired as $cid) {
        $cid = (string) $cid;
        if (!isset($maps['hired_vt_seen'][$cid])) { $pending[] = $cid; }
    }
    if (!$pending) {
        $created = (int) ($maps['hired_vt_created'] ?? 0);
        $updated = (int) ($maps['hired_vt_updated'] ?? 0);
        hs_state_log($state, "Hired VTs ensured locally: {$created} created, {$updated} updated.");
        $state['maps'] = $maps;
        hs_state_advance_stage($state);
        return;
    }
    $batch = array_slice($pending, 0, HS_PROCESS_BATCH);
    $objs = hs_batch_read_objects($hs, 'contacts', $batch, [
        'email','firstname','lastname','phone','mobilephone','country','jobtitle',
        'hs_lead_status','vt_status','hs_vt_status','contract_hired_status',
    ]);
    $pdo = db();
    foreach ($batch as $contactId) {
        $maps['hired_vt_seen'][$contactId] = true;
        $row = $objs[$contactId] ?? null;
        if (!is_array($row)) { continue; }
        $props = is_array($row['properties'] ?? null) ? $row['properties'] : [];
        $email = strtolower(trim((string) ($props['email'] ?? '')));
        if ($email === '') { continue; }
        $first = trim((string) ($props['firstname'] ?? ''));
        $last  = trim((string) ($props['lastname']  ?? ''));
        $full  = trim($first . ' ' . $last) ?: $email;
        $phone = trim((string) ($props['phone'] ?? ($props['mobilephone'] ?? '')));
        $country = trim((string) ($props['country'] ?? ''));
        $jobTitle = trim((string) ($props['jobtitle'] ?? ''));
        $vtStatus = trim((string) ($props['hs_vt_status'] ?? ($props['vt_status'] ?? '')));
        $leadStatus = trim((string) ($props['hs_lead_status'] ?? ''));

        // GUARD: contract/company associations also surface CLIENT-side contacts
        // (decision-makers, doctors, owners), not just the hired VA. Only import a
        // contact as a Virtual Teammate if HubSpot actually tags it as one
        // (hs_lead_status = the configured VT lead value). Otherwise skip — this is
        // what kept importing client contacts (e.g. drajay@…) as vt_hired.
        $leadValue = get_setting('hs_vt_lead_status_value', 'Virtual Teammate');
        if ($leadValue !== '' && strcasecmp($leadStatus, $leadValue) !== 0) {
            $maps['hired_vt_skipped'] = (int) ($maps['hired_vt_skipped'] ?? 0) + 1;
            hs_state_log($state, "Skipped hired-contact {$email}: hs_lead_status=\"{$leadStatus}\" is not a Virtual Teammate (client-side contact).");
            continue;
        }

        // Confirmed Virtual Teammate associated with a client via hired contract.
        $role = 'vt_hired';
        $existing = hs_find_user_for_contact($contactId, $email);
        if ($existing) {
            $userId = (int) $existing['id'];
            // Don't demote a super_admin/csm if they happen to be in this list.
            $newRole = in_array($existing['role'], ['super_admin','csm','client'], true) ? $existing['role'] : $role;
            $pdo->prepare(
                'UPDATE users SET role = :r, first_name = :fn, last_name = :ln, full_name = :full,
                                  phone = :p, country = :c, job_title = :jt,
                                  hubspot_contact_id = :hcid, vt_status = :vs, hs_lead_status = :ls,
                                  is_hired = :ih, active = 1, updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id'
            )->execute([
                ':r' => $newRole, ':fn' => $first, ':ln' => $last, ':full' => $full,
                ':p' => $phone, ':c' => $country, ':jt' => $jobTitle,
                ':hcid' => $contactId, ':vs' => $vtStatus, ':ls' => $leadStatus,
                ':ih' => ($newRole === 'vt_hired' ? 1 : 0), ':id' => $userId,
            ]);
            $maps['hired_vt_updated'] = ($maps['hired_vt_updated'] ?? 0) + 1;
        } else {
            $pdo->prepare(
                'INSERT INTO users (email, password_hash, role, first_name, last_name, full_name,
                                    phone, country, job_title, hubspot_contact_id, vt_status, hs_lead_status,
                                    is_hired, active, notify_by_email)
                 VALUES (:e, :h, :r, :fn, :ln, :full, :p, :c, :jt, :hcid, :vs, :ls, 1, 1, 1)'
            )->execute([
                ':e' => $email, ':h' => password_hash(hs_default_password($role), PASSWORD_DEFAULT),
                ':r' => $role, ':fn' => $first, ':ln' => $last, ':full' => $full,
                ':p' => $phone, ':c' => $country, ':jt' => $jobTitle,
                ':hcid' => $contactId, ':vs' => $vtStatus, ':ls' => $leadStatus,
            ]);
            $userId = (int) $pdo->lastInsertId();
            $maps['hired_vt_created'] = ($maps['hired_vt_created'] ?? 0) + 1;
        }

        // Ensure a vt_profiles row exists so subsequent UI views render.
        $exists = $pdo->prepare('SELECT 1 FROM vt_profiles WHERE user_id = :u');
        $exists->execute([':u' => $userId]);
        if (!$exists->fetchColumn()) {
            $pdo->prepare("INSERT INTO vt_profiles (user_id, status) VALUES (:u, 'hired')")
                ->execute([':u' => $userId]);
        } else {
            $pdo->prepare("UPDATE vt_profiles SET status = 'hired', updated_at = CURRENT_TIMESTAMP WHERE user_id = :u")
                ->execute([':u' => $userId]);
        }
    }
    $state['maps'] = $maps;
}

/** Stage 8: process each company → upsert clients + login user + company_profiles.
 *  Primary Contact properties drive the login user identity. */
function hs_client_step_process_clients(array &$state, HubSpotClient $hs): void
{
    if (empty($state['pending']['companies'])) {
        hs_state_log($state, 'All client companies upserted.');
        hs_state_advance_stage($state);
        return;
    }
    $batch = array_splice($state['pending']['companies'], 0, HS_PROCESS_BATCH);

    // Bulk-fetch the primary contacts for this batch.
    $maps = $state['maps'] ?? [];
    $batchPrimaryIds = [];
    foreach ($batch as $co) {
        $cid = (string) ($co['id'] ?? '');
        $pcid = (string) ($maps['primary_contact_by_company'][$cid] ?? '');
        if ($pcid !== '') { $batchPrimaryIds[] = $pcid; }
    }
    $primaryContacts = $batchPrimaryIds
        ? hs_batch_read_objects($hs, 'contacts', $batchPrimaryIds, [
            'email','firstname','lastname','phone','mobilephone','country','jobtitle',
        ])
        : [];

    foreach ($batch as $co) {
        try {
            $companyId   = (string) ($co['id'] ?? '');
            $primaryCid  = (string) ($maps['primary_contact_by_company'][$companyId] ?? '');
            $primary     = $primaryCid !== '' ? ($primaryContacts[$primaryCid] ?? null) : null;
            hs_process_one_client($co, $primary, $state);
        } catch (Throwable $ex) {
            $state['stats']['clients']['errors']++;
            $who = hs_company_label_from_props($co);
            hs_state_log($state, "Process client failed for {$who}: " . $ex->getMessage());
        }
    }
    if (empty($state['pending']['companies'])) {
        hs_state_log($state, 'All client companies upserted.');
        hs_state_advance_stage($state);
    }
}

/** Upsert one (company, primary contact) tuple → clients + users + company_profiles. */
function hs_process_one_client(array $company, ?array $primary, array &$state): void
{
    $pdo       = db();
    $companyId = (string) ($company['id'] ?? '');
    $props     = is_array($company['properties'] ?? null) ? $company['properties'] : [];
    $companyName = trim((string) ($props['name'] ?? ''));
    if ($companyName === '') {
        $state['stats']['clients']['skipped']++;
        return;
    }

    // Primary Contact drives the login user; fall back if missing.
    $pProps  = is_array(($primary['properties'] ?? null)) ? $primary['properties'] : [];
    $pEmail  = strtolower(trim((string) ($pProps['email'] ?? '')));
    $pFirst  = trim((string) ($pProps['firstname'] ?? ''));
    $pLast   = trim((string) ($pProps['lastname']  ?? ''));
    $pPhone  = trim((string) ($pProps['phone'] ?? ''));
    if ($pPhone === '') { $pPhone = trim((string) ($pProps['mobilephone'] ?? '')); }
    $pCountry= trim((string) ($pProps['country'] ?? ''));
    $pJob    = trim((string) ($pProps['jobtitle'] ?? ''));
    $pContactId = (string) ($primary['id'] ?? '');

    $clientLoginEmail = $pEmail;
    if ($clientLoginEmail === '') {
        // No Primary Contact → fall back to company-level email or synthesize.
        $clientLoginEmail = strtolower(trim((string) hs_pick($props, [
            'hs_contact_email','company_email','billing_email','billing_contact_email',
        ])));
        if ($clientLoginEmail === '') {
            $domain = trim((string) ($props['domain'] ?? ''));
            $clientLoginEmail = 'client+' . $companyId . '@' . ($domain !== '' ? $domain : 'imported.local');
        }
    }
    $clientLoginFull = trim($pFirst . ' ' . $pLast) ?: ($companyName ?: $clientLoginEmail);

    $domain   = trim((string) ($props['domain'] ?? ''));
    $website  = trim((string) ($props['website'] ?? ''));
    $industry = trim((string) ($props['industry'] ?? ''));
    $employees= trim((string) ($props['numberofemployees'] ?? ''));
    $descr    = trim((string) ($props['description'] ?? ''));
    $city     = trim((string) ($props['city'] ?? ''));
    $state2   = trim((string) ($props['state'] ?? ''));
    $address  = trim((string) ($props['address'] ?? ''));
    $ownerId  = trim((string) ($props['hubspot_owner_id'] ?? ''));

    $pdo->beginTransaction();
    try {
        // ── Upsert the client login user ──
        $userStmt = $pdo->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
        $userStmt->execute([':e' => $clientLoginEmail]);
        $userId = (int) ($userStmt->fetchColumn() ?: 0);
        if ($userId > 0) {
            $pdo->prepare(
                "UPDATE users SET role = 'client', first_name = :fn, last_name = :ln, full_name = :full,
                                  phone = :p, country = :c, job_title = :jt,
                                  hubspot_contact_id = :hcid, active = 1, updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id"
            )->execute([
                ':fn' => $pFirst, ':ln' => $pLast, ':full' => $clientLoginFull,
                ':p' => $pPhone, ':c' => $pCountry, ':jt' => $pJob,
                ':hcid' => $pContactId, ':id' => $userId,
            ]);
        } else {
            $pdo->prepare(
                "INSERT INTO users (email, password_hash, role, first_name, last_name, full_name,
                                    phone, country, job_title, hubspot_contact_id, active, notify_by_email)
                 VALUES (:e, :h, 'client', :fn, :ln, :full, :p, :c, :jt, :hcid, 1, 1)"
            )->execute([
                ':e' => $clientLoginEmail,
                ':h' => password_hash(hs_default_password('client'), PASSWORD_DEFAULT),
                ':fn' => $pFirst, ':ln' => $pLast, ':full' => $clientLoginFull,
                ':p' => $pPhone, ':c' => $pCountry, ':jt' => $pJob,
                ':hcid' => $pContactId,
            ]);
            $userId = (int) $pdo->lastInsertId();
        }

        // ── Upsert the clients row ──
        $clientStmt = $pdo->prepare('SELECT id FROM clients WHERE hubspot_company_id = :h LIMIT 1');
        $clientStmt->execute([':h' => $companyId]);
        $clientId = (int) ($clientStmt->fetchColumn() ?: 0);
        if ($clientId === 0) {
            $byNameStmt = $pdo->prepare('SELECT id FROM clients WHERE company_name = :n LIMIT 1');
            $byNameStmt->execute([':n' => $companyName]);
            $clientId = (int) ($byNameStmt->fetchColumn() ?: 0);
        }
        if ($clientId > 0) {
            $pdo->prepare(
                "UPDATE clients SET user_id = :u, company_name = :n, company_email = :ce,
                                    company_domain = :d, contract_status = 'active',
                                    hubspot_company_id = :hcid, hubspot_owner_id = :hoid,
                                    updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id"
            )->execute([
                ':u' => $userId, ':n' => $companyName, ':ce' => $clientLoginEmail,
                ':d' => $domain, ':hcid' => $companyId, ':hoid' => $ownerId, ':id' => $clientId,
            ]);
            $state['stats']['clients']['updated']++;
        } else {
            $pdo->prepare(
                "INSERT INTO clients (user_id, company_name, company_email, company_domain,
                                       contract_status, hubspot_company_id, hubspot_owner_id)
                 VALUES (:u, :n, :ce, :d, 'active', :hcid, :hoid)"
            )->execute([
                ':u' => $userId, ':n' => $companyName, ':ce' => $clientLoginEmail,
                ':d' => $domain, ':hcid' => $companyId, ':hoid' => $ownerId,
            ]);
            $clientId = (int) $pdo->lastInsertId();
            $state['stats']['clients']['created']++;
        }

        // ── Upsert company_profiles ──
        $cpStmt = $pdo->prepare('SELECT id FROM company_profiles WHERE client_id = :c LIMIT 1');
        $cpStmt->execute([':c' => $clientId]);
        $cpId = (int) ($cpStmt->fetchColumn() ?: 0);
        if ($cpId > 0) {
            $pdo->prepare(
                "UPDATE company_profiles SET website = :w, industry = :i, company_size = :sz,
                                              description = :ds, address = :ad, city = :ct, state = :st,
                                              record_state = 'active', updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id"
            )->execute([
                ':w' => $website, ':i' => $industry, ':sz' => $employees, ':ds' => $descr,
                ':ad' => $address, ':ct' => $city, ':st' => $state2, ':id' => $cpId,
            ]);
        } else {
            $pdo->prepare(
                "INSERT INTO company_profiles (client_id, website, industry, company_size,
                                                description, address, city, state, record_state)
                 VALUES (:c, :w, :i, :sz, :ds, :ad, :ct, :st, 'active')"
            )->execute([
                ':c' => $clientId, ':w' => $website, ':i' => $industry, ':sz' => $employees,
                ':ds' => $descr, ':ad' => $address, ':ct' => $city, ':st' => $state2,
            ]);
        }

        $pdo->commit();
        audit_log('hs_client_upsert', 'client', $clientId, 'company=' . $companyId . ' primary=' . $pContactId);

        // Stash the resolved client id for stage 8.
        $state['maps']['client_id_by_company'][$companyId] = $clientId;
    } catch (Throwable $ex) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        throw $ex;
    }
}

/** Stage 8: write csm_clients + client_vts using the maps built in earlier stages.
 *  Authoritative: existing rows for these clients get wiped + re-inserted so the
 *  sync mirrors HubSpot truth (VTs that left a company get unlinked). */
function hs_client_step_process_associations(array &$state, HubSpotClient $hs): void
{
    $maps = $state['maps'] ?? [];
    $clientByCompany = $maps['client_id_by_company'] ?? [];
    if (!$clientByCompany) {
        hs_state_log($state, 'No clients to link associations for.');
        hs_state_advance_stage($state);
        return;
    }

    $pdo = db();
    $companyIds = array_keys($clientByCompany);
    $batch = array_slice($companyIds, 0, HS_PROCESS_BATCH);

    foreach ($batch as $companyId) {
        $clientId = (int) ($clientByCompany[$companyId] ?? 0);
        if ($clientId <= 0) { continue; }

        // Wipe existing associations for this client so we're authoritative.
        $pdo->prepare('DELETE FROM client_vts  WHERE client_id = :c')->execute([':c' => $clientId]);
        $pdo->prepare('DELETE FROM csm_clients WHERE client_id = :c')->execute([':c' => $clientId]);

        // ── CSM link ──
        $csmUserId = (int) ($maps['csm_user_by_company'][$companyId] ?? 0);
        if ($csmUserId > 0) {
            $pdo->prepare('INSERT OR IGNORE INTO csm_clients (csm_user_id, client_id) VALUES (:csm, :c)')
                ->execute([':csm' => $csmUserId, ':c' => $clientId]);
            $state['stats']['relationships']['csm_links']++;
        }

        // ── VT links (hired contacts, via contracts pipeline) ──
        $hiredContactIds = array_keys((array) ($maps['hired_contacts_by_company'][$companyId] ?? []));
        if ($hiredContactIds) {
            $placeholders = implode(',', array_fill(0, count($hiredContactIds), '?'));
            $vtsStmt = $pdo->prepare(
                "SELECT id, role, hubspot_contact_id FROM users
                 WHERE hubspot_contact_id IN ($placeholders) AND role IN ('vt_hired','vt_onpool')"
            );
            $vtsStmt->execute($hiredContactIds);
            // Use INSERT OR REPLACE so a re-sync OVERWRITES previously-stored
            // sync-time defaults with the real values from HubSpot contracts.
            $linkStmt = $pdo->prepare(
                'INSERT OR REPLACE INTO client_vts
                   (client_id, vt_user_id, contract_status, started_at, ended_at,
                    workday_tracker_id, workday_link)
                 VALUES (:c, :v, :s, :start, :end, :wid, :wlink)'
            );
            foreach ($vtsStmt as $row) {
                $status   = $row['role'] === 'vt_hired' ? 'active' : 'pool';
                $linkKey  = (string) $companyId . '|' . (string) $row['hubspot_contact_id'];
                $linkDate = $maps['vt_link_dates'][$linkKey]   ?? ['start' => '', 'end' => ''];
                $linkWd   = $maps['vt_link_workday'][$linkKey] ?? ['id' => '', 'link' => ''];
                // Fall back to NULL when we have no contract start — SQLite
                // CURRENT_TIMESTAMP default doesn't apply on REPLACE, so we
                // pass an explicit ISO 'now()' as a safety net. The DB
                // schema column is TEXT so '' would compare badly later.
                $start = $linkDate['start'] !== '' ? $linkDate['start'] : date('Y-m-d H:i:s');
                $end   = $linkDate['end']   !== '' ? $linkDate['end']   : null;
                $linkStmt->execute([
                    ':c'     => $clientId,
                    ':v'     => (int) $row['id'],
                    ':s'     => $status,
                    ':start' => $start,
                    ':end'   => $end,
                    ':wid'   => $linkWd['id']   ?? '',
                    ':wlink' => $linkWd['link'] ?? '',
                ]);
                $state['stats']['relationships']['vt_links']++;
            }
        }
        unset($clientByCompany[$companyId]);
    }
    $state['maps']['client_id_by_company'] = $clientByCompany;

    if (empty($clientByCompany)) {
        hs_state_log($state,
            'Linked ' . $state['stats']['relationships']['vt_links'] . ' VT' .
            ' + ' . $state['stats']['relationships']['csm_links'] . ' CSM associations.'
        );
        hs_state_advance_stage($state);
    }
}

function hs_client_control(string $action): array
{
    $state = hs_client_state_load();
    switch ($action) {
        case 'start':
            if ($state['status'] === 'running') { break; }
            $state = hs_client_state_default();
            $state['status'] = 'running';
            $state['stage']  = 'init';
            break;
        case 'pause':
            if ($state['status'] === 'running') { $state['status'] = 'paused'; }
            break;
        case 'resume':
            if (in_array($state['status'], ['paused', 'error'], true)) {
                $state['status']     = 'running';
                $state['last_error'] = null;
            }
            break;
        case 'reset':
            $state = hs_client_state_default();
            break;
    }
    hs_client_state_save($state);
    return $state;
}

/* ─── Shared: build a compact report snapshot from a finished state ─── */

function hs_build_report(array $state): array
{
    return [
        'pipeline'     => (string) ($state['pipeline'] ?? 'unknown'),
        'started_at'   => $state['started_at'] ?? null,
        'finished_at'  => $state['finished_at'] ?? null,
        'duration_sec' => ($state['started_at'] && $state['finished_at'])
            ? max(0, strtotime($state['finished_at']) - strtotime($state['started_at']))
            : null,
        'stats'        => $state['stats'] ?? [],
        'errors'       => array_values(array_filter(array_map(
            static fn(array $m): string => (string) ($m['m'] ?? ''),
            $state['messages'] ?? []
        ), static fn(string $line): bool => stripos($line, 'failed') !== false || stripos($line, 'error') !== false)),
        'last_error'   => $state['last_error'] ?? null,
    ];
}
