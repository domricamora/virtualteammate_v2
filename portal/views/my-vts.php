<?php
/** @var array $user @var array $vts */
$pageTitle = 'My Virtual Teammates';
$subtitle  = $user['role'] === 'client'
    ? 'Your assigned Virtual Teammates — click a card to see the full profile.'
    : ($user['role'] === 'csm'
        ? 'VTs across all your assigned client engagements.'
        : 'All Virtual Teammates in the portal (hired and on-pool).');

$nameOf = static function (array $v): string {
    $n = trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''));
    return $n !== '' ? $n : (string) ($v['email'] ?? 'Virtual Teammate');
};
$initial = static function (array $v): string {
    $n = trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''));
    if ($n !== '') { return strtoupper(mb_substr($n, 0, 1)); }
    return strtoupper(mb_substr((string) ($v['email'] ?? '?'), 0, 1));
};
// Canonical URL format mirrors staging's build_workday_tracker_url():
// https://workdaytracker.com/app/public-report/{id}/. If a link is already
// stored in that shape, use it as-is. If it's just an id, build the URL.
$resolveWorkday = static function (array $v): string {
    foreach (['workday_link','profile_workday_link','cv_workday_link'] as $k) {
        $val = trim((string) ($v[$k] ?? ''));
        if ($val !== '' && preg_match('#^https?://#i', $val)) { return $val; }
    }
    foreach (['workday_tracker_id','profile_tracker_id','cv_workday_tracker_id'] as $k) {
        $tid = trim((string) ($v[$k] ?? ''));
        if ($tid !== '') {
            return 'https://workdaytracker.com/app/public-report/' . rawurlencode($tid) . '/';
        }
    }
    return '';
};
?>
<div class="card" style="margin-bottom:0;background:transparent;border:0;box-shadow:none;padding:0;">
  <div class="card-h" style="border:0;padding:0;margin-bottom:0;">
    <h3 style="margin:0;color:#fff;"><i class="fa-solid fa-user-doctor"></i> <?= e($pageTitle) ?> <span class="muted small">(<?= count($vts) ?>)</span></h3>
  </div>
</div>

<?php if (empty($vts)): ?>
  <div class="codex-selected-va-list codex-selected-va-list--empty">
    <p>No Virtual Teammates assigned yet. Talk to your CSM about adding one to your team.</p>
  </div>
<?php else: ?>
  <div class="codex-selected-va-list">
    <?php foreach ($vts as $v):
      $vid        = (int) ($v['user_id'] ?? $v['id'] ?? 0);
      $status     = $v['status'] ?? ($v['role'] ?? '');
      $isHired    = $status === 'hired' || $status === 'vt_hired';
      $role       = trim((string) ($v['role_title'] ?? ''));
      $department = trim((string) ($v['department'] ?? ''));
      $country    = trim((string) ($v['country'] ?? ''));
      $company    = trim((string) ($v['company_name'] ?? ''));
      $meta       = implode(' | ', array_filter([$department, $country]));
      $workdayUrl = $resolveWorkday($v);
    ?>
      <article class="codex-selected-va-card" tabindex="0"
               data-vt-id="<?= $vid ?>"
               aria-label="<?= e('Open profile for ' . $nameOf($v)) ?>">
        <div class="codex-selected-va-card__inner">
          <div class="codex-selected-va-card__media">
            <?php /* Initials only — photo_url is a PHP-served endpoint (p=avatar|media),
                     so one <img> per VT card fired a full portal request and slowed the grid.
                     The selected-VT detail panel still loads the real photo on click. */ ?>
            <span class="codex-selected-va-card__avatar"><?= e($initial($v)) ?></span>
          </div>
          <p class="codex-selected-va-card__meta"><?= e($isHired ? 'Team member' : 'On talent pool') ?></p>
          <h3 class="codex-selected-va-card__name"><?= e($nameOf($v)) ?></h3>
          <?php if ($role !== ''): ?>
            <p class="codex-selected-va-card__role"><?= e($role) ?></p>
          <?php endif; ?>
          <?php if ($meta !== ''): ?>
            <p class="codex-selected-va-card__details"><?= e($meta) ?></p>
          <?php endif; ?>
          <?php if ($company !== ''): ?>
            <p class="codex-selected-va-card__details"><i class="fa-solid fa-building"></i> <?= e($company) ?></p>
          <?php endif; ?>
        </div>
        <div class="codex-selected-va-card__actions" data-no-modal>
          <?php if ($workdayUrl !== ''): ?>
            <a class="codex-selected-va-card__btn" href="<?= e($workdayUrl) ?>" target="_blank" rel="noopener">Workday</a>
          <?php else: ?>
            <span class="codex-selected-va-card__btn codex-selected-va-card__btn--disabled" aria-disabled="true">Workday</span>
          <?php endif; ?>
          <a class="codex-selected-va-card__btn" href="<?= e(portal_url('productivity')) ?>#prod-eod">EOD</a>
          <button type="button" class="codex-selected-va-card__btn" data-open-vt="<?= $vid ?>">Profile</button>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- ── VT profile modal (one instance, populated by JS) ────────────── -->
<div class="vtm-modal" id="vtmModal" hidden>
  <div class="vtm-modal__backdrop" data-close></div>
  <div class="vtm-modal__panel" role="dialog" aria-modal="true" aria-labelledby="vtmName">
    <button class="vtm-modal__close" type="button" data-close aria-label="Close">
      <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="vtm-modal__body" id="vtmBody">
      <div class="vtm-modal__loading" aria-live="polite">Loading…</div>
    </div>
  </div>
</div>

<style>
/* Cards */
.codex-selected-va-list{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin:18px 0;}
@media (max-width:1024px){.codex-selected-va-list{grid-template-columns:repeat(2,minmax(0,1fr));}}
@media (max-width:767px){.codex-selected-va-list{grid-template-columns:1fr;}}
.codex-selected-va-card{
  position:relative;display:flex;flex-direction:column;min-height:100%;
  border:1px solid rgba(255,255,255,.08);border-radius:20px;cursor:pointer;
  background:linear-gradient(180deg,rgba(255,255,255,.06) 0%,rgba(255,255,255,.02) 100%);
  box-shadow:0 18px 44px rgba(0,0,0,.35);
  overflow:hidden;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;
}
.codex-selected-va-card::before{
  content:'';position:absolute;inset:0 auto auto 0;width:100%;height:3px;
  background:linear-gradient(90deg,#3919BA 0%,#7c3aed 50%,#F6B845 100%);
}
.codex-selected-va-card:hover,.codex-selected-va-card:focus-visible{
  transform:translateY(-4px);border-color:rgba(247,185,69,.42);
  box-shadow:0 28px 56px -20px rgba(247,185,69,.35);outline:none;
}
.codex-selected-va-card__inner{
  display:flex;flex-direction:column;align-items:center;justify-content:flex-start;
  width:100%;padding:26px 20px 14px;text-align:center;color:#fff;flex:1;
}
.codex-selected-va-card__media{display:flex;align-items:center;justify-content:center;margin-bottom:14px;}
.codex-selected-va-card__media img,
.codex-selected-va-card__avatar{
  width:96px;height:96px;border-radius:999px;
  display:flex;align-items:center;justify-content:center;
  object-fit:cover;border:2px solid rgba(247,185,69,.35);
  box-shadow:0 10px 26px rgba(0,0,0,.4);
  background:linear-gradient(135deg,#322a5a 0%,#4a4178 100%);
}
.codex-selected-va-card__avatar{font-size:34px;font-weight:800;color:#fff;}
.codex-selected-va-card__meta{
  margin:0 0 8px;font-size:10px;font-weight:800;letter-spacing:.10em;
  text-transform:uppercase;color:var(--gold,#d4a64a);
}
.codex-selected-va-card__name{margin:0;font-size:21px;line-height:1.2;color:#fff;font-weight:800;letter-spacing:-.2px;}
.codex-selected-va-card__role{margin:8px 0 0;font-size:13px;line-height:1.5;color:rgba(255,255,255,.78);}
.codex-selected-va-card__details{margin:6px 0 0;font-size:11.5px;line-height:1.5;color:rgba(255,255,255,.55);}
.codex-selected-va-card__details i{color:rgba(255,255,255,.4);margin-right:3px;}
.codex-selected-va-card__actions{display:flex;gap:6px;flex-wrap:wrap;justify-content:center;padding:6px 20px 18px;}
.codex-selected-va-card__btn{
  display:inline-flex;align-items:center;justify-content:center;
  padding:7px 14px;border-radius:999px;
  background:rgba(247,185,69,.14);color:var(--gold,#d4a64a) !important;
  font-size:10.5px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;
  text-decoration:none !important;border:1px solid rgba(247,185,69,.22);cursor:pointer;
  transition:background .15s,transform .15s,border-color .15s;font-family:inherit;line-height:1;
}
.codex-selected-va-card__btn:hover,.codex-selected-va-card__btn:focus{
  background:rgba(247,185,69,.26);border-color:rgba(247,185,69,.5);
  transform:translateY(-1px);color:var(--gold,#d4a64a) !important;outline:none;
}
.codex-selected-va-card__btn--disabled{opacity:.35;pointer-events:none;}
.codex-selected-va-list--empty{
  margin:18px 0;padding:18px 22px;
  background:rgba(255,255,255,.03);border:1px dashed rgba(255,255,255,.14);
  border-radius:18px;color:rgba(255,255,255,.7);
}
.codex-selected-va-list--empty p{margin:0;}

/* Modal */
.vtm-modal{position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;padding:24px;}
.vtm-modal[hidden]{display:none;}
.vtm-modal__backdrop{position:absolute;inset:0;background:rgba(8,6,24,.78);backdrop-filter:blur(6px);}
.vtm-modal__panel{
  position:relative;background:#0f0c28;border:1px solid rgba(255,255,255,.08);
  border-radius:18px;max-width:960px;width:100%;max-height:90vh;overflow:hidden;
  box-shadow:0 40px 80px rgba(0,0,0,.7);display:flex;flex-direction:column;
  animation:vtmPanelIn .25s cubic-bezier(.2,.9,.3,1) both;
}
@keyframes vtmPanelIn{from{transform:translateY(14px) scale(.97);opacity:0;}to{transform:none;opacity:1;}}
.vtm-modal__close{
  position:absolute;top:14px;right:14px;z-index:2;
  width:34px;height:34px;border-radius:50%;border:1px solid rgba(255,255,255,.1);
  background:rgba(255,255,255,.06);color:#fff;font-size:14px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;transition:background .15s;
}
.vtm-modal__close:hover{background:rgba(247,185,69,.18);border-color:rgba(247,185,69,.5);}
.vtm-modal__body{overflow:auto;padding:28px 30px 24px;}
.vtm-modal__loading{padding:60px 0;text-align:center;color:rgba(255,255,255,.6);}

/* Modal content */
.vtm-hero{display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;margin-bottom:18px;}
.vtm-hero-photo{
  width:118px;height:118px;border-radius:50%;object-fit:cover;flex:0 0 118px;
  border:3px solid rgba(247,185,69,.35);
  background:linear-gradient(135deg,#322a5a 0%,#4a4178 100%);
  display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:800;color:#fff;
  box-shadow:0 10px 26px rgba(0,0,0,.4);
}
.vtm-hero-meta{flex:1;min-width:240px;}
.vtm-hero-name{margin:0 0 6px;font-size:22px;font-weight:800;color:#fff;line-height:1.15;}
.vtm-hero-role{margin:0 0 10px;font-size:13.5px;color:var(--gold-lt,#e9c879);}
.vtm-hero-row{display:flex;flex-wrap:wrap;gap:6px;}
.vtm-pill{
  display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;
  padding:4px 10px;border-radius:999px;background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.08);color:rgba(255,255,255,.85);
}
.vtm-pill i{color:var(--gold,#d4a64a);}
.vtm-pill.is-active{background:rgba(78,196,126,.18);color:#bcf0d2;border-color:rgba(78,196,126,.35);}
.vtm-pill.is-paused{background:rgba(233,176,75,.18);color:#ffe2a8;border-color:rgba(233,176,75,.35);}

.vtm-kv{display:grid;grid-template-columns:140px 1fr;gap:8px 14px;font-size:13.5px;margin:12px 0;}
.vtm-kv dt{color:rgba(255,255,255,.55);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;padding-top:2px;}
.vtm-kv dd{color:rgba(255,255,255,.92);word-break:break-word;margin:0;}

.vtm-media{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px;}
@media (max-width:780px){.vtm-media{grid-template-columns:1fr;}}
.vtm-media-card{background:rgba(0,0,0,.25);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:12px;}
.vtm-media-h{font-size:11px;font-weight:800;color:var(--gold,#d4a64a);text-transform:uppercase;letter-spacing:1.1px;margin-bottom:8px;display:flex;align-items:center;gap:6px;}
.vtm-video,.vtm-pdf{background:#000;border-radius:8px;overflow:hidden;aspect-ratio:16/9;}
.vtm-video video,.vtm-pdf embed{width:100%;height:100%;display:block;border:0;}
.vtm-pdf{aspect-ratio:8.5/11;min-height:360px;background:#1a1535;}
.vtm-media-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:34px 14px;color:rgba(255,255,255,.45);text-align:center;background:rgba(255,255,255,.02);border-radius:8px;aspect-ratio:16/9;}
.vtm-media-empty i{font-size:34px;color:rgba(247,185,69,.3);margin-bottom:8px;}
.vtm-media-foot{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;}

.vtm-section{margin-top:18px;}
.vtm-section h4{margin:0 0 8px;font-size:12px;font-weight:800;color:var(--gold,#d4a64a);text-transform:uppercase;letter-spacing:1.1px;}
.vtm-section p{margin:0;font-size:13.5px;color:rgba(255,255,255,.85);line-height:1.6;white-space:pre-wrap;}
.vtm-people{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:6px;}
.vtm-people li{padding:8px 12px;background:rgba(255,255,255,.03);border-radius:8px;font-size:13px;color:rgba(255,255,255,.85);}
.vtm-people li strong{color:#fff;}

.vtm-err{padding:30px 0;text-align:center;color:#ff8b8b;}
</style>

<script>
(function(){
  var modal   = document.getElementById('vtmModal');
  var bodyEl  = document.getElementById('vtmBody');
  if (!modal || !bodyEl) return;

  function escapeHtml(s){
    return String(s == null ? '' : s)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }
  function fmt(v, fallback){ v = (v == null ? '' : String(v)).trim(); return v !== '' ? escapeHtml(v) : (fallback || '<span style="color:rgba(255,255,255,.4);">—</span>'); }
  function initialsOf(vt){
    var f = (vt.first_name||'').trim(); var l = (vt.last_name||'').trim();
    if (f || l) return (f.charAt(0)+l.charAt(0)).toUpperCase();
    return (vt.email||'?').charAt(0).toUpperCase();
  }
  function isPortalMediaUrl(u){ return /^index\.php\?p=media/.test(String(u||'')); }

  function render(data){
    var vt = data.vt || {};
    var clients = data.clients || [];
    var csms = data.csms || [];
    var fullName = ((vt.first_name||'') + ' ' + (vt.last_name||'')).trim() || vt.email || 'Virtual Teammate';
    var roleStr = (vt.role_title || '').trim();
    var statusPill = vt.status === 'hired'
      ? '<span class="vtm-pill is-active"><i class="fa-solid fa-check"></i> Hired</span>'
      : (vt.status === 'onpool' ? '<span class="vtm-pill is-paused"><i class="fa-solid fa-clock"></i> On talent pool</span>'
                                : (vt.status ? '<span class="vtm-pill">'+escapeHtml(vt.status)+'</span>' : ''));

    var photoBlock = vt.photo_url
      ? '<img class="vtm-hero-photo" src="'+escapeHtml(vt.photo_url)+'" alt="" onerror="this.onerror=null;this.outerHTML=\'<div class=&quot;vtm-hero-photo&quot;>'+escapeHtml(initialsOf(vt))+'</div>\';">'
      : '<div class="vtm-hero-photo">'+escapeHtml(initialsOf(vt))+'</div>';

    var pills = [];
    if (statusPill) pills.push(statusPill);
    if (vt.country) pills.push('<span class="vtm-pill"><i class="fa-solid fa-location-dot"></i> '+escapeHtml(vt.country)+'</span>');
    if (vt.english_level) pills.push('<span class="vtm-pill"><i class="fa-solid fa-language"></i> '+escapeHtml(vt.english_level)+'</span>');
    if (vt.experience_years) pills.push('<span class="vtm-pill"><i class="fa-solid fa-briefcase"></i> '+escapeHtml(vt.experience_years)+' yrs exp</span>');
    if (vt.hipaa_certified) pills.push('<span class="vtm-pill"><i class="fa-solid fa-shield"></i> HIPAA '+escapeHtml(vt.hipaa_certified)+'</span>');

    // Media blocks
    function videoBlock(){
      if (!vt.video_url) {
        return '<div class="vtm-media-empty"><i class="fa-solid fa-video-slash"></i><p>No intro video on file.</p></div>';
      }
      var poster = vt.photo_url ? ' poster="'+escapeHtml(vt.photo_url)+'"' : '';
      return '<div class="vtm-video"><video controls preload="metadata" playsinline'+poster+'>'+
             '<source src="'+escapeHtml(vt.video_url)+'">Your browser does not support inline video.</video></div>'+
             '<div class="vtm-media-foot">'+
               '<a class="btn-portal-secondary btn-sm" href="'+escapeHtml(vt.video_url)+'" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> '+(isPortalMediaUrl(vt.video_url)?'Open':'External link')+'</a>'+
             '</div>';
    }
    function resumeBlock(){
      if (!vt.resume_url) {
        return '<div class="vtm-media-empty"><i class="fa-solid fa-file-circle-xmark"></i><p>No resume on file.</p></div>';
      }
      return '<div class="vtm-pdf"><embed src="'+escapeHtml(vt.resume_url)+'#toolbar=1&navpanes=0" type="application/pdf"></div>'+
             '<div class="vtm-media-foot">'+
               '<a class="btn-portal-secondary btn-sm" href="'+escapeHtml(vt.resume_url)+'" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> '+(isPortalMediaUrl(vt.resume_url)?'Open':'External link')+'</a>'+
               (isPortalMediaUrl(vt.resume_url)?' <a class="btn-portal-secondary btn-sm" href="'+escapeHtml(vt.resume_url)+'" download><i class="fa-solid fa-download"></i> Download</a>':'')+
             '</div>';
    }

    // KV grid
    var kv = [
      ['Department',    vt.department],
      ['Role title',    vt.role_title],
      ['EHR / software',vt.ehr_software],
      ['Email',         vt.email],
      ['Phone',         vt.phone],
      ['IQ band',       vt.iq_band],
      ['Technical band',vt.technical_band],
      ['CI role',       vt.ci_role],
      ['DISC profile',  vt.disc_profile],
      ['Last login',    vt.last_login_at],
      ['HubSpot id',    vt.hubspot_contact_id]
    ];
    var kvRows = kv.filter(function(r){ return r[1]; })
                   .map(function(r){ return '<dt>'+escapeHtml(r[0])+'</dt><dd>'+fmt(r[1])+'</dd>'; })
                   .join('');

    var clientsHtml = clients.length
      ? '<ul class="vtm-people">'+clients.map(function(c){
          return '<li><strong>'+escapeHtml(c.company_name||'')+'</strong>'
               + (c.started_at ? ' &middot; <span class="muted small">started '+escapeHtml(String(c.started_at).slice(0,10))+'</span>' : '')
               + '</li>';
        }).join('')+'</ul>'
      : '<p style="color:rgba(255,255,255,.5);font-size:13px;">Not currently assigned to any client.</p>';

    var csmsHtml = csms.length
      ? '<ul class="vtm-people">'+csms.map(function(c){
          var nm = ((c.first_name||'')+' '+(c.last_name||'')).trim() || c.email || ('User #'+c.id);
          return '<li><strong>'+escapeHtml(nm)+'</strong> &middot; <span class="muted small">'+escapeHtml(c.email||'')+'</span></li>';
        }).join('')+'</ul>'
      : '<p style="color:rgba(255,255,255,.5);font-size:13px;">No CSMs cover this VT.</p>';

    var summary = (vt.summary||'').trim();
    var exp     = (vt.experience_text||'').trim();

    bodyEl.innerHTML =
      '<div class="vtm-hero">'+
        photoBlock+
        '<div class="vtm-hero-meta">'+
          '<h3 class="vtm-hero-name" id="vtmName">'+escapeHtml(fullName)+'</h3>'+
          (roleStr ? '<p class="vtm-hero-role">'+escapeHtml(roleStr)+'</p>' : '')+
          '<div class="vtm-hero-row">'+pills.join('')+'</div>'+
        '</div>'+
      '</div>'+
      '<div class="vtm-media">'+
        '<div class="vtm-media-card">'+
          '<div class="vtm-media-h"><i class="fa-solid fa-video"></i> Intro video</div>'+
          videoBlock()+
        '</div>'+
        '<div class="vtm-media-card">'+
          '<div class="vtm-media-h"><i class="fa-solid fa-file-pdf"></i> Resume</div>'+
          resumeBlock()+
        '</div>'+
      '</div>'+
      (kvRows ? '<div class="vtm-section"><h4>Profile</h4><dl class="vtm-kv">'+kvRows+'</dl></div>' : '')+
      (summary ? '<div class="vtm-section"><h4>Summary</h4><p>'+escapeHtml(summary)+'</p></div>' : '')+
      (exp ? '<div class="vtm-section"><h4>Experience</h4><p>'+escapeHtml(exp)+'</p></div>' : '')+
      '<div class="vtm-section"><h4>Assigned clients</h4>'+clientsHtml+'</div>'+
      '<div class="vtm-section"><h4>CSMs on this engagement</h4>'+csmsHtml+'</div>';
  }

  function open(vid){
    if (!vid) return;
    bodyEl.innerHTML = '<div class="vtm-modal__loading">Loading…</div>';
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
    fetch('index.php?p=vts.profile_json&id=' + encodeURIComponent(vid), { credentials:'same-origin' })
      .then(function(r){
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function(data){
        if (data && data.error) throw new Error(data.error);
        render(data);
      })
      .catch(function(err){
        bodyEl.innerHTML = '<div class="vtm-err">Could not load profile: ' + (err && err.message || 'unknown') + '</div>';
      });
  }

  function close(){
    modal.hidden = true;
    document.body.style.overflow = '';
    bodyEl.innerHTML = '';
  }

  // Card click / keyboard activation. Skip when click came from an action
  // button or external link inside the card.
  document.querySelectorAll('.codex-selected-va-card').forEach(function(card){
    card.addEventListener('click', function(e){
      if (e.target.closest('[data-no-modal] a, [data-no-modal] .codex-selected-va-card__btn--disabled')) return;
      var btn = e.target.closest('[data-open-vt]');
      if (btn) { e.preventDefault(); open(btn.getAttribute('data-open-vt')); return; }
      // Click anywhere else on the card opens the modal (matches card-as-link UX).
      if (!e.target.closest('[data-no-modal]')) { open(card.getAttribute('data-vt-id')); }
    });
    card.addEventListener('keydown', function(e){
      if ((e.key === 'Enter' || e.key === ' ') && e.target === card) { e.preventDefault(); open(card.getAttribute('data-vt-id')); }
    });
  });

  // Close handlers — backdrop, X button, Escape.
  modal.querySelectorAll('[data-close]').forEach(function(el){ el.addEventListener('click', close); });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && !modal.hidden) close(); });
})();
</script>
