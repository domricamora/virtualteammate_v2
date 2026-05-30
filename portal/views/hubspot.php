<?php
/** @var array $settings @var array $state @var array $talent_state @var array $client_state @var ?array $test_result */
$pageTitle = 'HubSpot sync';
$subtitle  = 'Talent (VTs) and Client (companies + CSMs + relationships) pipelines run independently. Each batches, pauses, resumes, and reports.';

/** Render the controls + stats block for one pipeline (talent or client). */
$renderPipelineCard = function (string $key, string $title, string $icon, array $st, string $controlPath, string $stepPath) {
    $status = (string) ($st['status'] ?? 'idle');
    $stage  = (string) ($st['stage'] ?? '-');
    $stages = is_array($st['stages'] ?? null) ? $st['stages'] : [];
    $idx    = max(0, array_search($stage, $stages, true));
    $pct    = $stages ? (int) floor(($idx / max(1, count($stages) - 1)) * 100) : 0;
    $canStart  = in_array($status, ['idle','done','error'], true);
    $canPause  = $status === 'running';
    $canResume = in_array($status, ['paused','error'], true);
    $canReset  = $status !== 'running';
    ?>
    <div class="card hs-pipeline" data-pipeline="<?= e($key) ?>" data-step-url="<?= e($controlPath) ?>">
      <div class="hs-pipeline-head">
        <h3 style="margin:0;"><i class="fa-solid <?= e($icon) ?>"></i> <?= e($title) ?></h3>
        <span class="hs-status-pill pill-<?= e($status) ?>" data-el="statusPill"><?= e(strtoupper($status)) ?></span>
      </div>
      <div class="hs-pipeline-sub muted small">
        Stage: <strong data-el="stage"><?= e($stage) ?></strong>
        &middot; <span data-el="started">Started: <?= $st['started_at'] ? e(fmt_dt($st['started_at'])) : '—' ?></span>
        &middot; <span data-el="finished">Finished: <?= $st['finished_at'] ? e(fmt_dt($st['finished_at'])) : '—' ?></span>
      </div>
      <div class="hs-progress" aria-hidden="true"><div class="hs-progress-bar" data-el="progress" style="width:<?= $pct ?>%;"></div></div>

      <div class="actions-row" style="margin-top:14px;">
        <form method="post" action="<?= e($controlPath) ?>" class="inline-form" data-action="start">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="start">
          <button class="btn-portal-primary" type="submit" <?= $canStart ? '' : 'disabled' ?>>
            <i class="fa-solid fa-play"></i> Start <?= e(strtolower($title)) ?>
          </button>
        </form>
        <form method="post" action="<?= e($controlPath) ?>" class="inline-form" data-action="pause">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="pause">
          <button class="btn-portal-secondary" type="submit" <?= $canPause ? '' : 'disabled' ?>>
            <i class="fa-solid fa-pause"></i> Pause
          </button>
        </form>
        <form method="post" action="<?= e($controlPath) ?>" class="inline-form" data-action="resume">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="resume">
          <button class="btn-portal-secondary" type="submit" <?= $canResume ? '' : 'disabled' ?>>
            <i class="fa-solid fa-rotate-right"></i> Resume
          </button>
        </form>
        <form method="post" action="<?= e($controlPath) ?>" class="inline-form" data-action="reset"
              onsubmit="return confirm('Reset <?= e($title) ?> state? This abandons any in-progress run.');">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="reset">
          <button class="btn-portal-danger" type="submit" <?= $canReset ? '' : 'disabled' ?>>
            <i class="fa-solid fa-arrows-rotate"></i> Reset
          </button>
        </form>
      </div>

      <?php if (!empty($st['last_error'])): ?>
        <div class="portal-flash flash-error" style="margin-top:14px;" data-el="lastError">
          <strong>Error:</strong> <?= e($st['last_error']) ?>
        </div>
      <?php endif; ?>

      <div class="hs-stats-grid" style="margin-top:18px;" data-el="statsGrid">
        <?php foreach ((array) ($st['stats'] ?? []) as $bucket => $vals): ?>
          <?php if (!is_array($vals)) {
            // Scalar stat — render as a single tile.
            echo '<div class="hs-stat-card"><div class="hs-stat-h">' . e(ucwords(str_replace('_', ' ', (string) $bucket))) . '</div>'
              . '<div class="hs-stat-row"><span>Count</span><strong data-stat="' . e($bucket) . '">' . (int) $vals . '</strong></div></div>';
            continue;
          } ?>
          <div class="hs-stat-card">
            <div class="hs-stat-h"><?= e(ucwords(str_replace('_', ' ', (string) $bucket))) ?></div>
            <?php foreach ($vals as $statKey => $statVal): ?>
              <?php if (is_array($statVal)) { continue; } // skip nested arrays (e.g. failed_urls) — surfaced in report ?>
              <div class="hs-stat-row">
                <span><?= e(ucwords(str_replace('_', ' ', (string) $statKey))) ?></span>
                <strong data-stat="<?= e($bucket . '.' . $statKey) ?>"><?= e((string) $statVal) ?></strong>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($st['last_report'])):
        $r = $st['last_report'];
        $duration = (int) ($r['duration_sec'] ?? 0);
        $dHuman = $duration > 60 ? floor($duration / 60) . 'm ' . ($duration % 60) . 's' : $duration . 's';
        $failed = (array) ($st['stats']['media']['failed_urls'] ?? []);
        $media  = (array) ($st['stats']['media'] ?? []);
      ?>
        <div class="card" style="margin-top:18px;background:rgba(126,194,126,.06);border:1px solid rgba(126,194,126,.25);">
          <div class="card-h"><h3 style="margin:0;color:#7ec27e;">📊 Last run report</h3>
            <span class="muted small">Finished <?= e(fmt_dt($r['finished_at'])) ?> &middot; ran for <?= e($dHuman) ?></span>
          </div>
          <?php if (!empty($media)): ?>
            <p class="muted small" style="margin:6px 0 4px;">
              Media:
              <strong><?= (int) ($media['downloaded'] ?? 0) ?></strong> downloaded &middot;
              <strong><?= (int) ($media['cache_hits'] ?? 0) ?></strong> cached &middot;
              <strong><?= (int) ($media['fallbacks'] ?? 0) ?></strong> external-link fallbacks &middot;
              <strong><?= (int) ($media['skipped'] ?? 0) ?></strong> skipped &middot;
              <strong><?= (int) ($media['errors'] ?? 0) ?></strong> errors
            </p>
          <?php endif; ?>
          <?php if ($failed): ?>
            <details>
              <summary><strong><?= count($failed) ?> media items needing attention</strong> (skipped / fallback / failed) — click to view</summary>
              <table class="data-table" style="margin-top:10px;">
                <thead><tr><th>User</th><th>Email</th><th>Kind</th><th>Reason</th><th>URL</th></tr></thead>
                <tbody>
                  <?php foreach ($failed as $f):
                      $name = (string) ($f['name']  ?? '');
                      $em   = (string) ($f['email'] ?? '');
                      $uid  = (string) ($f['user_id'] ?? '');
                  ?>
                    <tr>
                      <td><?= e($name !== '' ? $name : '—') ?> <span class="muted small">#<?= e($uid) ?></span></td>
                      <td class="muted small"><?= e($em) ?></td>
                      <td><?= e((string) ($f['kind'] ?? '')) ?></td>
                      <td class="muted small"><?= e((string) ($f['reason'] ?? '')) ?></td>
                      <td class="muted small" style="max-width:360px;overflow:hidden;text-overflow:ellipsis;"><?= e((string) ($f['url'] ?? '')) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </details>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <details style="margin-top:14px;">
        <summary>Activity log (<?= count($st['messages'] ?? []) ?>)</summary>
        <ol class="hs-log" data-el="log" reversed>
          <?php foreach (array_reverse((array) ($st['messages'] ?? [])) as $m): ?>
            <li><span class="hs-log-t"><?= e($m['t']) ?></span> <?= e($m['m']) ?></li>
          <?php endforeach; ?>
          <?php if (empty($st['messages'])): ?>
            <li class="muted">No activity yet.</li>
          <?php endif; ?>
        </ol>
      </details>
    </div>
    <?php
};
?>

<?php if ($test_result): ?>
  <div class="portal-flash <?= $test_result['ok'] ? 'flash-success' : 'flash-error' ?>"><?= e($test_result['msg']) ?></div>
<?php endif; ?>

<!-- ─── Two-pipeline runner ─── -->
<div class="grid-2">
  <?php $renderPipelineCard(
    'talent', 'Talent sync', 'fa-user-doctor', $talent_state,
    portal_url('hubspot.talent_control'), portal_url('hubspot.talent_step')
  ); ?>
  <?php $renderPipelineCard(
    'client', 'Client sync', 'fa-building', $client_state,
    portal_url('hubspot.client_control'), portal_url('hubspot.client_step')
  ); ?>
</div>

<!-- ─── Single-fetch ─── -->
<div class="grid-2" style="margin-top:18px;">
  <div class="card hs-single" data-single="talent">
    <h3><i class="fa-solid fa-magnifying-glass"></i> Re-sync one VT</h3>
    <p class="muted small">Search HubSpot by name or email and re-sync a single VT contact. Useful after a HubSpot edit — no need to run the full batch.</p>
    <div class="hs-single-input">
      <input type="text" data-el="q" placeholder="Name or email — e.g. Maya Reyes or maya@…">
      <button class="btn-portal-secondary btn-sm" type="button" data-el="searchBtn"><i class="fa-solid fa-search"></i> Search</button>
    </div>
    <div class="hs-single-results" data-el="results"></div>
  </div>

  <div class="card hs-single" data-single="client">
    <h3><i class="fa-solid fa-magnifying-glass"></i> Re-sync one client</h3>
    <p class="muted small">Search HubSpot by company name or domain and re-sync a single company (including its CSM + hired-VT links).</p>
    <div class="hs-single-input">
      <input type="text" data-el="q" placeholder="Company name or domain — e.g. Elkhart Clinic">
      <button class="btn-portal-secondary btn-sm" type="button" data-el="searchBtn"><i class="fa-solid fa-search"></i> Search</button>
    </div>
    <div class="hs-single-results" data-el="results"></div>
  </div>
</div>

<!-- ─── Settings ─── -->
<div class="card" style="margin-top:18px;">
  <h3><i class="fa-solid fa-gears"></i> Connection &amp; sync settings</h3>
  <form method="post" action="<?= e(portal_url('hubspot.save_settings')) ?>" class="form-grid">
    <?= csrf_field() ?>
    <label class="span-2">HubSpot Private App token
      <input type="password" name="hs_token" autocomplete="off"
             placeholder="<?= $settings['hs_token'] !== '' ? '•••••• (stored — leave blank to keep)' : 'pat-na1-…' ?>">
    </label>

    <label>VT lead-status field
      <input type="text" name="hs_vt_lead_status_field" value="<?= e($settings['hs_vt_lead_status_field']) ?>">
    </label>
    <label>VT lead-status value
      <input type="text" name="hs_vt_lead_status_value" value="<?= e($settings['hs_vt_lead_status_value']) ?>">
    </label>

    <label>VT status field
      <input type="text" name="hs_vt_status_field" value="<?= e($settings['hs_vt_status_field']) ?>">
    </label>
    <label>Records per batch (1–100)
      <input type="number" name="hs_batch_size" min="1" max="100" value="<?= (int) $settings['hs_batch_size'] ?>">
    </label>

    <label>Client lead-status field
      <input type="text" name="hs_client_lead_status_field" value="<?= e($settings['hs_client_lead_status_field']) ?>">
    </label>
    <label>Client lead-status value
      <input type="text" name="hs_client_lead_status_value" value="<?= e($settings['hs_client_lead_status_value']) ?>">
    </label>

    <label class="span-2 check-row">
      <input type="checkbox" name="hs_import_media" <?= !empty($settings['hs_import_media']) ? 'checked' : '' ?>>
      Download photos, resumes and videos (Bearer-authed for HubSpot-hosted files; saved to <code>data/media/vt/{id}/</code>)
    </label>

    <div class="form-actions span-2">
      <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save settings</button>
      <form method="post" action="<?= e(portal_url('hubspot.test')) ?>" class="inline-form" style="display:inline-block;">
        <?= csrf_field() ?>
        <button class="btn-portal-secondary" type="submit"><i class="fa-solid fa-plug"></i> Test connection</button>
      </form>
    </div>
  </form>
</div>

<!-- ─── Demo + Danger zone ─── -->
<div class="card" style="margin-top:18px;">
  <h3><i class="fa-solid fa-user-plus"></i> Demo users</h3>
  <p class="muted">Idempotent — creates one of each role with predictable credentials so the portal can be exercised without a HubSpot run.</p>
  <table class="data-table" style="margin-bottom:14px;">
    <thead><tr><th>Role</th><th>Email</th><th>Password</th></tr></thead>
    <tbody>
      <tr><td>Client</td>      <td><code>demo-client@virtualteammate.com</code></td>      <td><code>client12345</code></td></tr>
      <tr><td>CSM</td>         <td><code>demo-csm@virtualteammate.com</code></td>         <td><code>csm12345</code></td></tr>
      <tr><td>VT (Hired)</td>  <td><code>demo-vt-hired@virtualteammate.com</code></td>    <td><code>vthired12345</code></td></tr>
      <tr><td>VT (On-Pool)</td><td><code>demo-vt-onpool@virtualteammate.com</code></td>   <td><code>vtonpool12345</code></td></tr>
    </tbody>
  </table>
  <form method="post" action="<?= e(portal_url('hubspot.seed_demo')) ?>" class="inline-form">
    <?= csrf_field() ?>
    <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-wand-magic-sparkles"></i> Seed demo users</button>
  </form>
</div>

<div class="card hs-danger" style="margin-top:18px;">
  <h3><i class="fa-solid fa-triangle-exclamation"></i> Danger zone</h3>
  <p class="muted">
    Permanently delete <strong>every user, client, profile and media file</strong> that came in from HubSpot.
    Manually-created portal records (and demo users) are untouched.
  </p>
  <form method="post" action="<?= e(portal_url('hubspot.purge')) ?>" class="hs-danger-form"
        onsubmit="return confirm('Last chance — this permanently deletes all HubSpot-synced data and downloaded media. Continue?');">
    <?= csrf_field() ?>
    <label class="hs-danger-label">Type <code>DELETE</code> to confirm
      <input type="text" name="confirm" autocomplete="off" pattern="DELETE" required placeholder="DELETE">
    </label>
    <button class="btn-portal-danger" type="submit"><i class="fa-solid fa-trash"></i> Delete all HubSpot data</button>
  </form>
</div>

<style>
.hs-pipeline-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:6px;}
.hs-pipeline-sub{margin-bottom:12px;}
.hs-status-pill{display:inline-block;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:800;letter-spacing:1px;background:rgba(255,255,255,.08);color:rgba(255,255,255,.85);}
.hs-status-pill.pill-running{background:rgba(247,185,69,.22);color:#f7b945;}
.hs-status-pill.pill-paused{background:rgba(255,255,255,.1);color:rgba(255,255,255,.7);}
.hs-status-pill.pill-done{background:rgba(126,194,126,.2);color:#7ec27e;}
.hs-status-pill.pill-error{background:rgba(229,62,62,.2);color:#ff8585;}
.hs-progress{height:8px;background:rgba(255,255,255,.06);border-radius:30px;overflow:hidden;margin-top:6px;}
.hs-progress-bar{height:100%;background:linear-gradient(90deg,#3919BA,#F6B845);border-radius:30px;transition:width .3s ease;}
.hs-stats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;}
.hs-stat-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:10px;padding:10px 12px;}
.hs-stat-h{font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--gold,#d4a64a);font-weight:700;margin-bottom:6px;}
.hs-stat-row{display:flex;justify-content:space-between;font-size:12px;color:rgba(255,255,255,.7);padding:3px 0;}
.hs-stat-row strong{color:#fff;}
.hs-log{max-height:260px;overflow-y:auto;padding-left:18px;font-size:12.5px;line-height:1.6;color:rgba(255,255,255,.75);}
.hs-log li{padding:2px 0;}
.hs-log-t{color:rgba(255,255,255,.45);margin-right:6px;}
.hs-single-input{display:flex;gap:8px;margin-top:10px;}
.hs-single-input input{flex:1;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.12);color:#fff;padding:9px 12px;border-radius:8px;font-family:inherit;}
.hs-single-input input:focus{outline:none;border-color:var(--gold,#d4a64a);}
.hs-single-results{margin-top:12px;display:flex;flex-direction:column;gap:8px;}
.hs-match{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:10px 12px;border:1px solid rgba(255,255,255,.08);border-radius:10px;background:rgba(255,255,255,.02);}
.hs-match-name{font-size:14px;font-weight:700;color:#fff;}
.hs-match-meta{font-size:11.5px;color:rgba(255,255,255,.55);margin-top:2px;}
.hs-match-result{font-size:12px;padding:4px 8px;border-radius:6px;}
.hs-match-result.ok{background:rgba(126,194,126,.15);color:#7ec27e;}
.hs-match-result.err{background:rgba(229,62,62,.15);color:#ff8585;}
</style>

<script>
(function(){
  var csrf = <?= json_encode(csrf_token()) ?>;

  // ── Pipeline polling ── one PipelineRunner per .hs-pipeline card.
  document.querySelectorAll('.hs-pipeline').forEach(function(card){
    var pipeline = card.getAttribute('data-pipeline');
    var stepUrl  = '<?= e(portal_url('hubspot.')) ?>' + pipeline + '_step';
    var q  = function(s){ return card.querySelector('[data-el="' + s + '"]'); };
    var statePoll = null;

    function setStatus(s){
      if (!s) return;
      var status = (s.status || 'idle').toUpperCase();
      q('statusPill').textContent = status;
      q('statusPill').className = 'hs-status-pill pill-' + (s.status || 'idle');
      q('stage').textContent = s.stage || '—';
      q('started').textContent  = 'Started: '  + (s.started_at  || '—');
      q('finished').textContent = 'Finished: ' + (s.finished_at || '—');

      // Progress
      var stages = s.stages || [];
      var idx = Math.max(0, stages.indexOf(s.stage));
      var pct = stages.length ? Math.floor((idx / Math.max(1, stages.length - 1)) * 100) : 0;
      q('progress').style.width = pct + '%';

      // Update any data-stat numbers we can find inside this card.
      if (s.stats) {
        card.querySelectorAll('[data-stat]').forEach(function(el){
          var path = el.getAttribute('data-stat').split('.');
          var val = s.stats;
          for (var i = 0; i < path.length && val != null; i++) val = val[path[i]];
          if (typeof val === 'number' || typeof val === 'string') el.textContent = val;
        });
      }

      // Refresh log tail
      var logEl = q('log');
      if (logEl && Array.isArray(s.messages)) {
        logEl.innerHTML = '';
        s.messages.slice().reverse().forEach(function(m){
          var li = document.createElement('li');
          var span = document.createElement('span');
          span.className = 'hs-log-t'; span.textContent = m.t;
          li.appendChild(span); li.appendChild(document.createTextNode(' ' + m.m));
          logEl.appendChild(li);
        });
      }
    }

    var consecutiveErrors = 0;
    function poll(){
      var form = new FormData(); form.append('_csrf', csrf);
      fetch(stepUrl, { method:'POST', body: form, credentials:'same-origin' })
        .then(function(r){
          // Non-2xx (504, 500 from PHP timeout, etc.) → treat as transient.
          if (!r.ok) { throw new Error('HTTP ' + r.status); }
          return r.json();
        })
        .then(function(s){
          consecutiveErrors = 0;
          setStatus(s);
          if (s.status === 'running') {
            statePoll = setTimeout(poll, 700);
          } else {
            statePoll = null;
            if (s.status === 'done' || s.status === 'error') {
              setTimeout(function(){ location.reload(); }, 600);
            }
          }
        })
        .catch(function(err){
          // Network blip, PHP timeout, browser tab in background. Don't
          // give up — back off (1.5s → 3s → 6s → 12s → 24s capped) and
          // keep polling. State is checkpointed per-item in the media
          // loop so the next successful tick resumes cleanly.
          consecutiveErrors++;
          var backoffMs = Math.min(24000, 1500 * Math.pow(2, consecutiveErrors - 1));
          var logEl = q('log');
          if (logEl) {
            var li = document.createElement('li');
            li.style.color = '#f7b945';
            li.textContent = '⚠ poll error (' + (err && err.message || 'network') + ') — retry in ' + Math.round(backoffMs/1000) + 's (attempt #' + consecutiveErrors + ')';
            logEl.insertBefore(li, logEl.firstChild);
          }
          statePoll = setTimeout(poll, backoffMs);
        });
    }

    // Kick off polling if we landed on the page mid-run.
    var initStatus = q('statusPill').textContent.trim().toLowerCase();
    if (initStatus === 'running') { statePoll = setTimeout(poll, 250); }

    // Intercept the Start form so we begin polling right after the POST without waiting for the redirect to settle.
    var startForm = card.querySelector('form[data-action="start"]');
    if (startForm) {
      startForm.addEventListener('submit', function(){
        // Server will redirect back; the page reload + initStatus check picks polling up.
        // Nothing else needed here.
      });
    }
  });

  // ── Single-fetch ──
  document.querySelectorAll('.hs-single').forEach(function(card){
    var kind = card.getAttribute('data-single'); // 'talent' or 'client'
    var qEl  = card.querySelector('[data-el="q"]');
    var btn  = card.querySelector('[data-el="searchBtn"]');
    var out  = card.querySelector('[data-el="results"]');
    var searchUrl  = '<?= e(portal_url('hubspot.')) ?>' + kind + '_search';
    var syncOneUrl = '<?= e(portal_url('hubspot.')) ?>' + kind + '_sync_one';

    function renderMatches(matches){
      out.innerHTML = '';
      if (!matches || !matches.length) { out.innerHTML = '<p class="muted small">No matches.</p>'; return; }
      matches.forEach(function(m){
        var row = document.createElement('div');
        row.className = 'hs-match';
        var nameLine, metaLine;
        if (kind === 'talent') {
          nameLine = m.full_name || m.email || ('Contact #' + m.id);
          metaLine = (m.email || '') + ' · vt_status: ' + (m.vt_status || '—') + ' · lead: ' + (m.hs_lead_status || '—');
        } else {
          nameLine = m.name || ('Company #' + m.id);
          metaLine = (m.domain || '') + ' · lead: ' + (m.hs_lead_status || '—') + (m.industry ? ' · ' + m.industry : '');
        }
        row.innerHTML = '<div><div class="hs-match-name"></div><div class="hs-match-meta"></div></div>'
                       + '<button class="btn-portal-primary btn-sm" type="button"><i class="fa-solid fa-rotate"></i> Sync this</button>';
        row.querySelector('.hs-match-name').textContent = nameLine;
        row.querySelector('.hs-match-meta').textContent = metaLine;
        row.querySelector('button').addEventListener('click', function(){
          var btn2 = row.querySelector('button');
          btn2.disabled = true; btn2.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Syncing…';
          var fd = new FormData(); fd.append('_csrf', csrf); fd.append('id', m.id);
          fetch(syncOneUrl, { method:'POST', body: fd, credentials:'same-origin' })
            .then(function(r){ return r.json(); })
            .then(function(res){
              btn2.disabled = false;
              var resultBadge = document.createElement('span');
              resultBadge.className = 'hs-match-result ' + (res.ok ? 'ok' : 'err');
              if (res.ok) {
                if (kind === 'talent') {
                  resultBadge.textContent = (res.action || 'done') + (res.stats && res.stats.media ? ' · media: ' + (res.stats.media.downloaded || 0) + ' dl / ' + (res.stats.media.errors || 0) + ' err' : '');
                } else {
                  resultBadge.textContent = 'client_id=' + (res.client_id || '?') + ' · ' + (res.hired_contact_count || 0) + ' hired';
                }
                btn2.innerHTML = '<i class="fa-solid fa-check"></i> Re-sync';
              } else {
                resultBadge.textContent = res.error || 'Failed';
                btn2.innerHTML = '<i class="fa-solid fa-rotate"></i> Retry';
              }
              // Replace any prior result badge
              var prev = row.querySelector('.hs-match-result');
              if (prev) prev.remove();
              row.insertBefore(resultBadge, btn2);
            })
            .catch(function(e){
              btn2.disabled = false; btn2.innerHTML = '<i class="fa-solid fa-rotate"></i> Retry';
              var b = document.createElement('span');
              b.className = 'hs-match-result err'; b.textContent = String(e);
              row.insertBefore(b, btn2);
            });
        });
        out.appendChild(row);
      });
    }

    function doSearch(){
      var q = (qEl.value || '').trim();
      if (q === '') return;
      out.innerHTML = '<p class="muted small">Searching…</p>';
      var fd = new FormData(); fd.append('q', q);
      fetch(searchUrl, { method:'POST', body: fd, credentials:'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(res){
          if (!res.ok) { out.innerHTML = '<p class="muted small">' + (res.error || 'Search failed.') + '</p>'; return; }
          renderMatches(res.matches);
        })
        .catch(function(e){ out.innerHTML = '<p class="muted small">Error: ' + String(e) + '</p>'; });
    }
    btn.addEventListener('click', doSearch);
    qEl.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); doSearch(); }});
  });
})();
</script>
