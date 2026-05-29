<?php /** @var ?array $meeting @var array $clients @var array $candidates @var array $user */
$isNew     = $meeting === null;
$pageTitle = $isNew ? 'New meeting' : 'Edit meeting';
$subtitle  = $isNew
    ? 'Add a client meeting — pick a client account, fill in the call details, save it for everyone who needs to see it.'
    : 'Update the meeting details below.';

// Split the stored scheduled_at + end_at into the three form inputs
// (Day / Starts / Ends). Falls back gracefully when end_at is empty.
$startDay = $startTime = $endTime = '';
if (!empty($meeting['scheduled_at'])) {
    try {
        $dt = new DateTime((string) $meeting['scheduled_at']);
        $startDay  = $dt->format('Y-m-d');
        $startTime = $dt->format('H:i');
    } catch (Throwable $_) {}
}
if (!empty($meeting['end_at'])) {
    try { $endTime = (new DateTime((string) $meeting['end_at']))->format('H:i'); } catch (Throwable $_) {}
} elseif ($startTime !== '' && !empty($meeting['duration_minutes'])) {
    try {
        $endDt = (new DateTime((string) $meeting['scheduled_at']))->modify('+' . (int) $meeting['duration_minutes'] . ' minutes');
        $endTime = $endDt->format('H:i');
    } catch (Throwable $_) {}
}

$callApps = [
    'zoom'        => 'Zoom',
    'google_meet' => 'Google Meet',
    'teams'       => 'Microsoft Teams',
    'webex'       => 'Webex',
    'phone'       => 'Phone call',
    'other'       => 'Other',
];
$callAppNow = strtolower((string) ($meeting['call_app'] ?? 'zoom'));
if (!isset($callApps[$callAppNow])) { $callAppNow = 'other'; }
?>

<div class="mtg-shell">
  <form method="post" action="<?= e(portal_url('meetings.edit', $isNew ? [] : ['id' => $meeting['id']])) ?>" class="mtg-card">
    <?= csrf_field() ?>

    <header class="mtg-head">
      <h2>Add a client meeting</h2>
      <p>Pick a client account, add the call details, and save it for everyone who needs to see it.</p>
    </header>

    <div class="mtg-field">
      <label for="mtg-name" class="mtg-label">Meeting name</label>
      <input id="mtg-name" type="text" name="topic" required maxlength="200"
             value="<?= e($meeting['topic'] ?? '') ?>"
             placeholder="Weekly catch-up, launch review, planning call">
    </div>

    <?php
      // Clients always own exactly one client account, so skip the picker
      // and auto-bind to it. CSMs (multi-account) and super admins (any)
      // still get the dropdown.
      $autoBindClient = ($user['role'] === 'client' && count($clients) === 1);
      $autoClient     = $autoBindClient ? $clients[0] : null;
    ?>
    <?php if ($autoBindClient): ?>
      <div class="mtg-field">
        <label class="mtg-label">Client account</label>
        <div class="mtg-static">
          <i class="fa-solid fa-building"></i>
          <span><?= e($autoClient['company_name']) ?></span>
        </div>
        <input type="hidden" name="client_id" value="<?= (int) $autoClient['id'] ?>">
      </div>
    <?php else: ?>
      <div class="mtg-field">
        <label for="mtg-client" class="mtg-label">Choose a client account</label>
        <select id="mtg-client" name="client_id" required>
          <option value="">Select a client account</option>
          <?php foreach ($clients as $cl): ?>
            <option value="<?= (int) $cl['id'] ?>"<?= (int) ($meeting['client_id'] ?? 0) === (int) $cl['id'] ? ' selected' : '' ?>><?= e($cl['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
        <small class="mtg-hint">Pick the account you want this meeting to appear under.</small>
      </div>
    <?php endif; ?>

    <div class="mtg-grid-2">
      <div class="mtg-field">
        <label for="mtg-link" class="mtg-label">Meeting link</label>
        <input id="mtg-link" type="url" name="meeting_link"
               value="<?= e($meeting['meeting_link'] ?? '') ?>"
               placeholder="https://zoom.us/...">
      </div>
      <div class="mtg-field">
        <label for="mtg-app" class="mtg-label">Call app</label>
        <select id="mtg-app" name="call_app">
          <?php foreach ($callApps as $k => $lbl): ?>
            <option value="<?= e($k) ?>"<?= $k === $callAppNow ? ' selected' : '' ?>><?= e($lbl) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="mtg-grid-3">
      <div class="mtg-field">
        <label for="mtg-day" class="mtg-label">Day</label>
        <input id="mtg-day" type="date" name="day" required value="<?= e($startDay) ?>">
      </div>
      <div class="mtg-field">
        <label for="mtg-start" class="mtg-label">Starts</label>
        <input id="mtg-start" type="time" name="start" required value="<?= e($startTime) ?>">
      </div>
      <div class="mtg-field">
        <label for="mtg-end" class="mtg-label">Ends</label>
        <input id="mtg-end" type="time" name="end" value="<?= e($endTime) ?>">
      </div>
    </div>

    <div class="mtg-field">
      <label for="mtg-notes" class="mtg-label">Notes for the meeting</label>
      <textarea id="mtg-notes" name="notes" rows="5" placeholder="Add the topics you want to cover."><?= e($meeting['notes'] ?? '') ?></textarea>
    </div>

    <!-- Multi-attendee picker. Candidate list is scoped by role:
         super_admin → any active CSM / VT / client,
         client      → only their assigned CSM(s) and hired VT(s),
         csm         → only their clients + the VTs on those engagements.
         Posts as attendee_user_ids[]; handler re-validates every id. -->
    <div class="mtg-field">
      <label class="mtg-label">Invite teammates
        <span class="muted small" style="font-weight:400;text-transform:none;letter-spacing:0;">
          (check anyone you want to join the call)
        </span>
      </label>
      <?php
        $preSel = $selected_attendee_ids ?? [];
        if (!$preSel && !empty($meeting['attendee_user_id'])) { $preSel = [(int) $meeting['attendee_user_id']]; }
      ?>
      <?php if (empty($candidates)): ?>
        <div class="mtg-static">No teammates available to invite yet.</div>
      <?php else: ?>
        <div class="mtg-attendees" data-mtg-attendees>
          <div class="mtg-attendees-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" data-mtg-search placeholder="Filter by name, email, or role…" autocomplete="off">
            <span class="mtg-attendees-count" data-mtg-count>0 selected</span>
          </div>
          <div class="mtg-attendees-list" role="group" aria-label="Attendees">
            <?php foreach ($candidates as $cnd):
              $cid = (int) ($cnd['id'] ?? 0);
              $nm  = trim(($cnd['first_name'] ?? '') . ' ' . ($cnd['last_name'] ?? ''));
              $nm  = $nm !== '' ? $nm : (string) ($cnd['email'] ?? ('User #' . $cid));
              $roleLbl = role_label($cnd['role'] ?? '');
              $em  = (string) ($cnd['email'] ?? '');
              $isChecked = in_array($cid, $preSel, true);
              $blob = strtolower(trim($nm . ' ' . $em . ' ' . $roleLbl));
            ?>
              <label class="mtg-attendee<?= $isChecked ? ' is-checked' : '' ?>" data-search="<?= e($blob) ?>">
                <input type="checkbox" name="attendee_user_ids[]" value="<?= $cid ?>"<?= $isChecked ? ' checked' : '' ?>>
                <span class="mtg-attendee-meta">
                  <span class="mtg-attendee-name"><?= e($nm) ?></span>
                  <span class="mtg-attendee-sub"><?= e($roleLbl) ?><?= $em !== '' ? ' · ' . e($em) : '' ?></span>
                </span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <?php if (!$isNew): ?>
      <div class="mtg-field">
        <label for="mtg-status" class="mtg-label">Status</label>
        <?php $st = $meeting['status'] ?? 'scheduled'; ?>
        <select id="mtg-status" name="status">
          <?php foreach (['scheduled','completed','cancelled'] as $s): ?>
            <option value="<?= e($s) ?>"<?= $s === $st ? ' selected' : '' ?>><?= e(ucfirst($s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php else: ?>
      <input type="hidden" name="status" value="scheduled">
    <?php endif; ?>
    <input type="hidden" name="meeting_with_role" value="<?= e($meeting['meeting_with_role'] ?? 'csm') ?>">

    <div class="mtg-actions">
      <a class="btn-portal-secondary" href="<?= e(portal_url('meetings')) ?>">Cancel</a>
      <button class="btn-portal-primary" type="submit">
        <i class="fa-solid fa-calendar-plus"></i> <?= $isNew ? 'Save meeting' : 'Save changes' ?>
      </button>
    </div>
  </form>
</div>

<style>
.mtg-shell{max-width:720px;margin:0 auto;}
.mtg-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:26px 30px;display:flex;flex-direction:column;gap:18px;}
.mtg-head h2{margin:0 0 6px;font-size:22px;font-weight:800;color:#fff;}
.mtg-head p{margin:0;color:rgba(255,255,255,.65);font-size:13.5px;line-height:1.5;}
.mtg-field{display:flex;flex-direction:column;gap:6px;}
.mtg-label{font-size:12px;font-weight:700;color:rgba(255,255,255,.85);text-transform:none;letter-spacing:.2px;}
.mtg-card input[type=text],
.mtg-card input[type=url],
.mtg-card input[type=date],
.mtg-card input[type=time],
.mtg-card select,
.mtg-card textarea{
  background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.14);color:#fff;
  border-radius:8px;padding:10px 12px;font-size:13.5px;font-family:inherit;
}
.mtg-card input:focus,.mtg-card select:focus,.mtg-card textarea:focus{outline:none;border-color:var(--gold,#d4a64a);background:rgba(255,255,255,.07);}
.mtg-card input::placeholder,.mtg-card textarea::placeholder{color:rgba(255,255,255,.35);}
.mtg-card textarea{resize:vertical;min-height:120px;line-height:1.5;}
.mtg-hint{color:rgba(255,255,255,.5);font-size:11.5px;}
.mtg-static{
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.14);
  border-radius:8px;padding:10px 12px;color:#fff;font-size:13.5px;
  display:flex;align-items:center;gap:8px;
}
.mtg-static i{color:var(--gold,#d4a64a);font-size:13px;}

/* Multi-attendee picker */
.mtg-attendees{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.14);border-radius:10px;overflow:hidden;}
.mtg-attendees-search{display:flex;align-items:center;gap:8px;padding:10px 12px;background:rgba(255,255,255,.02);border-bottom:1px solid rgba(255,255,255,.08);}
.mtg-attendees-search i{color:rgba(255,255,255,.5);font-size:12px;}
.mtg-attendees-search input{flex:1;background:transparent;border:0;color:#fff;font-family:inherit;font-size:13.5px;outline:none;padding:2px 0;}
.mtg-attendees-search input::placeholder{color:rgba(255,255,255,.35);}
.mtg-attendees-count{font-size:11px;font-weight:700;color:var(--gold,#d4a64a);background:rgba(247,185,69,.12);padding:3px 9px;border-radius:30px;border:1px solid rgba(247,185,69,.25);white-space:nowrap;}
.mtg-attendees-list{max-height:300px;overflow-y:auto;display:flex;flex-direction:column;}
.mtg-attendee{display:flex;align-items:center;gap:10px;padding:9px 12px;cursor:pointer;border-top:1px solid rgba(255,255,255,.04);transition:background .12s;}
.mtg-attendee:first-child{border-top:0;}
.mtg-attendee:hover{background:rgba(247,185,69,.05);}
.mtg-attendee input[type=checkbox]{
  appearance:none;-webkit-appearance:none;
  width:18px;height:18px;border:1.5px solid rgba(255,255,255,.25);
  border-radius:4px;background:transparent;cursor:pointer;
  display:flex;align-items:center;justify-content:center;flex:0 0 18px;
  transition:background .12s,border-color .12s;
}
.mtg-attendee input[type=checkbox]:checked{background:var(--gold,#d4a64a);border-color:var(--gold,#d4a64a);}
.mtg-attendee input[type=checkbox]:checked::after{content:'\2713';color:#1a1535;font-size:12px;font-weight:800;}
.mtg-attendee.is-checked{background:rgba(247,185,69,.07);}
.mtg-attendee-meta{display:flex;flex-direction:column;gap:1px;min-width:0;flex:1;}
.mtg-attendee-name{font-size:13.5px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.mtg-attendee-sub{font-size:11px;color:rgba(255,255,255,.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.mtg-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.mtg-grid-3{display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:14px;}
@media (max-width:640px){.mtg-grid-2,.mtg-grid-3{grid-template-columns:1fr;}}
.mtg-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:6px;}
</style>

<script>
(function(){
  document.querySelectorAll('[data-mtg-attendees]').forEach(function(wrap){
    var search = wrap.querySelector('[data-mtg-search]');
    var counter = wrap.querySelector('[data-mtg-count]');
    var labels  = Array.prototype.slice.call(wrap.querySelectorAll('.mtg-attendee'));
    var boxes   = Array.prototype.slice.call(wrap.querySelectorAll('input[type=checkbox]'));

    function refreshCount(){
      var n = boxes.filter(function(b){ return b.checked; }).length;
      counter.textContent = n + ' selected';
    }
    boxes.forEach(function(b){
      b.addEventListener('change', function(){
        var lbl = b.closest('.mtg-attendee');
        if (lbl) { lbl.classList.toggle('is-checked', b.checked); }
        refreshCount();
      });
    });

    if (search){
      var t = null;
      search.addEventListener('input', function(){
        clearTimeout(t);
        t = setTimeout(function(){
          var q = search.value.trim().toLowerCase();
          labels.forEach(function(l){
            var blob = l.getAttribute('data-search') || '';
            l.style.display = (q === '' || blob.indexOf(q) !== -1) ? '' : 'none';
          });
        }, 80);
      });
    }
    refreshCount();
  });
})();
</script>
