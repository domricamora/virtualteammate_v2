<?php /** @var array $settings @var array $state @var ?array $test_result */
$pageTitle = 'HubSpot sync';
$subtitle  = 'One run imports VTs, clients, and CSMs from HubSpot (in that order). Media is downloaded into data/media/ and tagged per VT.';

$canStart   = in_array($state['status'], ['idle','done','error'], true);
$canPause   = $state['status'] === 'running';
$canResume  = in_array($state['status'], ['paused','error'], true);
$canReset   = $state['status'] !== 'running';

$stages     = $state['stages'];
$stageIdx   = max(0, array_search($state['stage'], $stages, true));
$progressPc = (int) floor(($stageIdx / max(1, count($stages) - 1)) * 100);
?>

<?php if ($test_result): ?>
  <div class="portal-flash <?= $test_result['ok'] ? 'flash-success' : 'flash-error' ?>"><?= e($test_result['msg']) ?></div>
<?php endif; ?>

<div class="grid-2">
  <div class="card">
    <h3>Connection &amp; sync settings</h3>
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
      <label>Records per batch (1-100)
        <input type="number" name="hs_batch_size" min="1" max="100" value="<?= (int) $settings['hs_batch_size'] ?>">
      </label>

      <label>Client lead-status field
        <input type="text" name="hs_client_lead_status_field" value="<?= e($settings['hs_client_lead_status_field']) ?>">
      </label>
      <label>Client lead-status value
        <input type="text" name="hs_client_lead_status_value" value="<?= e($settings['hs_client_lead_status_value']) ?>">
      </label>

      <label>CSM lead-status field
        <input type="text" name="hs_csm_lead_status_field" value="<?= e($settings['hs_csm_lead_status_field']) ?>">
      </label>
      <label>CSM lead-status value
        <input type="text" name="hs_csm_lead_status_value" value="<?= e($settings['hs_csm_lead_status_value']) ?>">
      </label>

      <label class="span-2 check-row">
        <input type="checkbox" name="hs_import_media" <?= !empty($settings['hs_import_media']) ? 'checked' : '' ?>>
        Download photos, resumes and videos (high-quality originals, into data/media/)
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

  <div class="card hs-runner">
    <h3>Run controls</h3>
    <div class="hs-status" id="hsStatus" data-status="<?= e($state['status']) ?>">
      <span class="hs-status-pill" id="hsStatusPill"><?= e(strtoupper($state['status'])) ?></span>
      <span class="hs-status-stage">Stage: <strong id="hsStage"><?= e($state['stage']) ?></strong></span>
    </div>

    <div class="hs-progress" aria-hidden="true"><div class="hs-progress-bar" id="hsProgressBar" style="width:<?= $progressPc ?>%;"></div></div>

    <div class="actions-row" style="margin-top:14px;">
      <form method="post" action="<?= e(portal_url('hubspot.control')) ?>" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="start">
        <button class="btn-portal-primary" type="submit" <?= $canStart ? '' : 'disabled' ?>>
          <i class="fa-solid fa-play"></i> Start sync
        </button>
      </form>
      <form method="post" action="<?= e(portal_url('hubspot.control')) ?>" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="pause">
        <button class="btn-portal-secondary" type="submit" <?= $canPause ? '' : 'disabled' ?>>
          <i class="fa-solid fa-pause"></i> Pause
        </button>
      </form>
      <form method="post" action="<?= e(portal_url('hubspot.control')) ?>" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="resume">
        <button class="btn-portal-secondary" type="submit" <?= $canResume ? '' : 'disabled' ?>>
          <i class="fa-solid fa-rotate-right"></i> Resume
        </button>
      </form>
      <form method="post" action="<?= e(portal_url('hubspot.control')) ?>" class="inline-form" onsubmit="return confirm('Reset the sync state? This abandons any in-progress run.');">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="reset">
        <button class="btn-portal-danger" type="submit" <?= $canReset ? '' : 'disabled' ?>>
          <i class="fa-solid fa-arrows-rotate"></i> Reset
        </button>
      </form>
    </div>

    <?php if (!empty($state['last_error'])): ?>
      <div class="portal-flash flash-error" style="margin-top:14px;"><strong>Error:</strong> <?= e($state['last_error']) ?></div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3>Live status</h3>
  <div class="hs-stats-grid" id="hsStatsGrid">
    <?php foreach (['vts'=>'VTs','clients'=>'Clients','csms'=>'CSMs'] as $key => $label):
      $s = $state['stats'][$key]; ?>
      <div class="hs-stat-card">
        <div class="hs-stat-h"><?= e($label) ?></div>
        <div class="hs-stat-row"><span>Created</span><strong data-stat="<?= e($key) ?>.created"><?= (int) $s['created'] ?></strong></div>
        <div class="hs-stat-row"><span>Updated</span><strong data-stat="<?= e($key) ?>.updated"><?= (int) $s['updated'] ?></strong></div>
        <div class="hs-stat-row"><span>Skipped</span><strong data-stat="<?= e($key) ?>.skipped"><?= (int) $s['skipped'] ?></strong></div>
        <?php if (array_key_exists('deleted', $s)): ?>
          <div class="hs-stat-row"><span>Deleted</span><strong data-stat="<?= e($key) ?>.deleted"><?= (int) $s['deleted'] ?></strong></div>
        <?php endif; ?>
        <div class="hs-stat-row"><span>Errors</span><strong data-stat="<?= e($key) ?>.errors"><?= (int) $s['errors'] ?></strong></div>
      </div>
    <?php endforeach; ?>
    <div class="hs-stat-card">
      <div class="hs-stat-h">Other</div>
      <div class="hs-stat-row"><span>Media files</span><strong data-stat="media_downloads"><?= (int) $state['stats']['media_downloads'] ?></strong></div>
      <div class="hs-stat-row"><span>Started</span><strong id="hsStarted"><?= e($state['started_at'] ? fmt_dt($state['started_at']) : '—') ?></strong></div>
      <div class="hs-stat-row"><span>Finished</span><strong id="hsFinished"><?= e($state['finished_at'] ? fmt_dt($state['finished_at']) : '—') ?></strong></div>
    </div>
  </div>
</div>

<div class="card">
  <h3>Activity log</h3>
  <ol class="hs-log" id="hsLog" reversed>
    <?php foreach (array_reverse($state['messages']) as $m): ?>
      <li><span class="hs-log-t"><?= e($m['t']) ?></span> <?= e($m['m']) ?></li>
    <?php endforeach; ?>
    <?php if (empty($state['messages'])): ?>
      <li class="muted">No activity yet.</li>
    <?php endif; ?>
  </ol>
</div>

<div class="card hs-danger">
  <h3><i class="fa-solid fa-triangle-exclamation"></i> Danger zone</h3>
  <p class="muted">
    Permanently delete <strong>every user, client, profile and media file</strong> that
    came in from HubSpot (anything with a <code>hubspot_contact_id</code> or
    <code>hubspot_company_id</code>). Manually-created portal records are untouched.
    The sync state is reset so the next run starts clean.
  </p>
  <form method="post" action="<?= e(portal_url('hubspot.purge')) ?>" class="hs-danger-form"
        onsubmit="return confirm('Last chance — this permanently deletes all HubSpot-synced data and downloaded media. Continue?');">
    <?= csrf_field() ?>
    <label class="hs-danger-label">Type <code>DELETE</code> to confirm
      <input type="text" name="confirm" autocomplete="off" pattern="DELETE" required placeholder="DELETE">
    </label>
    <button class="btn-portal-danger" type="submit">
      <i class="fa-solid fa-trash"></i> Delete all HubSpot data
    </button>
  </form>
</div>

<script>
(function(){
  var csrfToken = <?= json_encode(csrf_token()) ?>;
  var stepUrl   = <?= json_encode(portal_url('hubspot.step')) ?>;
  var stages    = <?= json_encode($stages) ?>;
  var polling   = false;

  function $(id){ return document.getElementById(id); }

  function setStatus(state){
    $('hsStatusPill').textContent = (state.status || 'idle').toUpperCase();
    $('hsStatusPill').className = 'hs-status-pill pill-' + (state.status || 'idle');
    $('hsStage').textContent = state.stage || '—';
    $('hsStarted').textContent  = state.started_at || '—';
    $('hsFinished').textContent = state.finished_at || '—';

    var idx = stages.indexOf(state.stage);
    if (idx < 0) idx = 0;
    var pct = Math.floor((idx / Math.max(1, stages.length - 1)) * 100);
    $('hsProgressBar').style.width = pct + '%';

    if (state.stats) {
      document.querySelectorAll('[data-stat]').forEach(function(el){
        var path = el.getAttribute('data-stat').split('.');
        var v = state.stats;
        for (var i = 0; i < path.length; i++) { v = v ? v[path[i]] : undefined; }
        if (typeof v === 'number') el.textContent = v;
      });
    }

    if (state.messages) {
      var log = $('hsLog');
      log.innerHTML = '';
      state.messages.slice().reverse().forEach(function(m){
        var li = document.createElement('li');
        var span = document.createElement('span');
        span.className = 'hs-log-t'; span.textContent = m.t;
        li.appendChild(span); li.appendChild(document.createTextNode(' ' + m.m));
        log.appendChild(li);
      });
      if (!state.messages.length) {
        var li = document.createElement('li');
        li.className = 'muted'; li.textContent = 'No activity yet.';
        log.appendChild(li);
      }
    }
  }

  function step(){
    if (!polling) return;
    var form = new FormData();
    form.append('_csrf', csrfToken);
    fetch(stepUrl, { method:'POST', body: form, credentials:'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(state){
        setStatus(state);
        if (state.status === 'running') {
          setTimeout(step, 600);
        } else {
          polling = false;
          if (state.status === 'done') {
            location.reload(); // pick up server-rendered completion summary + final button states
          }
        }
      })
      .catch(function(){ polling = false; });
  }

  // Kick off polling if we arrived in 'running' state (e.g. user just clicked Start).
  var statusEl = $('hsStatus');
  if (statusEl && statusEl.getAttribute('data-status') === 'running') {
    polling = true;
    setTimeout(step, 200);
  }
})();
</script>
