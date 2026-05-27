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
const HS_MEDIA_ROOT      = __DIR__ . '/../data/media';
const HS_SEARCH_PAGE_MAX = 100;   // HubSpot cap.
const HS_PROCESS_BATCH   = 5;     // Contacts per process step (media downloads are expensive).

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
    return ['name','domain','website','hs_contact_email','company_email','billing_email','hs_lead_status'];
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

function hs_media_dir(string $entity, int $id): string
{
    $dir = HS_MEDIA_ROOT . '/' . $entity . '/' . $id;
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
    return $dir;
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
function hs_import_media(string $url, string $entity, int $id, string $kind): string
{
    $url = hs_normalize_media_url($url, $kind);
    if ($url === '' || !preg_match('#^https?://#i', $url)) { return ''; }
    if (!isset(hs_media_kind_map()[$kind])) { return ''; }

    if ($kind === 'video' && hs_is_embedded_video_url($url)) {
        // Keep the embed URL intact — we don't re-encode YouTube / Vimeo / Loom.
        return $url;
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

    $dir  = hs_media_dir($entity, $id);
    $file = $dir . '/' . $kind . '.' . $ext;

    if (file_put_contents($file, $body) === false) { return ''; }

    // Remove stale artifacts in a different extension for the same kind.
    foreach (glob($dir . '/' . $kind . '.*') as $existing) {
        if ($existing !== $file) { @unlink($existing); }
    }
    return 'index.php?p=media&e=' . urlencode($entity) . '&id=' . $id . '&k=' . urlencode($kind);
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
            'done',
        ],
        'after_cursor' => null,
        'pending'      => ['vts' => [], 'clients' => [], 'csms' => []],
        'started_at'   => null,
        'updated_at'   => null,
        'finished_at'  => null,
        'last_error'   => null,
        'stats' => [
            'vts'             => ['created'=>0,'updated'=>0,'skipped'=>0,'deleted'=>0,'errors'=>0],
            'clients'         => ['created'=>0,'updated'=>0,'skipped'=>0,'errors'=>0],
            'csms'            => ['created'=>0,'updated'=>0,'skipped'=>0,'errors'=>0],
            'media_downloads' => 0,
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
        hs_state_log($state, "Skipped {$email}: unmapped vt_status \"{$vtStatus}\".");
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
            'INSERT INTO users (email, password_hash, role, first_name, last_name, phone, country, hubspot_contact_id, active)
             VALUES (:e, :h, :r, :fn, :ln, :p, :c, :hcid, 1)'
        )->execute([
            ':e'=>$email, ':h'=>password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
            ':r'=>$role, ':fn'=>$first, ':ln'=>$last, ':p'=>$phone, ':c'=>$country, ':hcid'=>$contactId,
        ]);
        $userId = (int) $pdo->lastInsertId();
        $state['stats']['vts']['created']++;
    }

    // Media import (high-quality, original bytes).
    $localPhoto = $localResume = $localVideo = '';
    if (!empty($settings['hs_import_media'])) {
        $localPhoto  = hs_import_media($remotePhoto,  'vt', $userId, 'photo');
        $localResume = hs_import_media($remoteResume, 'vt', $userId, 'resume');
        $localVideo  = hs_import_media($remoteVideo,  'vt', $userId, 'video');
        $state['stats']['media_downloads'] += (int) ($localPhoto !== '') + (int) ($localResume !== '') + (int) ($localVideo !== '');
    }
    if ($localPhoto !== '') {
        $pdo->prepare('UPDATE users SET photo_url = :ph WHERE id = :id')->execute([':ph' => $localPhoto, ':id' => $userId]);
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
        'video_url'          => $localVideo  !== '' ? $localVideo  : hs_pick($props, ['intro_video_link','intro_video_url','intro_video','introduction_video','video_url']),
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
                'INSERT INTO users (email, password_hash, role, first_name, last_name, active)
                 VALUES (:e, :h, "client", "", :ln, 1)'
            )->execute([
                ':e' => $email, ':h' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), ':ln' => $name,
            ]);
            $userId = (int) $pdo->lastInsertId();
        }
    }

    if ($row) {
        $pdo->prepare(
            'UPDATE clients SET user_id=:u, company_name=:n, company_email=:e, company_domain=:d,
                                contract_status="active", hubspot_company_id=:h, updated_at=CURRENT_TIMESTAMP
             WHERE id=:id'
        )->execute([
            ':u'=>$userId ?: $row['user_id'], ':n'=>$name, ':e'=>$email, ':d'=>$domain, ':h'=>$companyId, ':id'=>$row['id'],
        ]);
        $state['stats']['clients']['updated']++;
        audit_log('hs_sync_upsert', 'client', (int) $row['id'], 'company=' . $companyId);
    } else {
        $pdo->prepare(
            'INSERT INTO clients (user_id, company_name, company_email, company_domain, contract_status, hubspot_company_id)
             VALUES (:u, :n, :e, :d, "active", :h)'
        )->execute([
            ':u'=>$userId, ':n'=>$name, ':e'=>$email, ':d'=>$domain, ':h'=>$companyId,
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
            'UPDATE users SET email=:e, role="csm", first_name=:fn, last_name=:ln, phone=:p, country=:c,
                              hubspot_contact_id=:h, updated_at=CURRENT_TIMESTAMP
             WHERE id=:id'
        )->execute([
            ':e'=>$email, ':fn'=>$first, ':ln'=>$last, ':p'=>$phone, ':c'=>$country,
            ':h'=>$contactId, ':id'=>$existing['id'],
        ]);
        $state['stats']['csms']['updated']++;
    } else {
        $pdo->prepare(
            'INSERT INTO users (email, password_hash, role, first_name, last_name, phone, country, hubspot_contact_id, active)
             VALUES (:e, :h, "csm", :fn, :ln, :p, :c, :hcid, 1)'
        )->execute([
            ':e'=>$email, ':h'=>password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
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
            case 'fetch_csms':      hs_step_fetch_csms($state, $hs, $settings);     break;
            case 'process_csms':    hs_step_process_csms($state, $hs, $settings);   break;
            default:                hs_state_advance_stage($state);                 break;
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
