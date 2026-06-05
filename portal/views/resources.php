<?php
/** @var array|null $client  ['resources'=>[...], 'playbook'=>[...]]  (clients/csm/admin) */
/** @var array|null $kc      ['shortcuts'=>[['url','label','icon','desc','tag','group','ext']], 'home'=>url]  (vts/csm/admin) */
$pageTitle = 'Resources';
$subtitle  = $kc && !$client
    ? 'Your Knowledge Center — training, SOPs and references.'
    : 'Playbooks, worksheets and references.';
?>

<?php if ($kc): ?>
  <div class="card kc-hub">
    <div class="kc-hub-hero">
      <div class="kc-hub-hero-l">
        <h2 class="kc-hub-title"><i class="fa-solid fa-life-ring"></i> Help Center</h2>
        <p class="kc-hub-sub">Quick access to key resources and guides. Use search or the filters to jump straight to what you need.</p>
      </div>
      <a class="btn-portal-primary kc-hub-cta" href="<?= e($kc['home']) ?>">Go to Knowledge Center <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="kc-hub-panel">
      <div class="kc-hub-bar">
        <div class="kc-hub-search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="search" id="kcSearch" placeholder="Search the Help Center — “benefits”, “IT”, “billing”, “policies”…" autocomplete="off" aria-label="Search resources">
        </div>
        <div class="kc-hub-chips" role="group" aria-label="Filters">
          <button class="kc-chip is-on" type="button" data-filter="all" aria-pressed="true">All</button>
          <button class="kc-chip" type="button" data-filter="ops" aria-pressed="false">Operations</button>
          <button class="kc-chip" type="button" data-filter="admin" aria-pressed="false">Admin</button>
          <button class="kc-chip" type="button" data-filter="support" aria-pressed="false">Support</button>
          <button class="kc-chip kc-chip-clear" type="button" id="kcClear">Clear</button>
        </div>
      </div>

      <div class="kc-hub-grid" id="kcTiles">
        <?php foreach ($kc['shortcuts'] as $s): ?>
          <a class="kc-hub-tile" href="<?= e($s['url']) ?>"<?= !empty($s['ext']) ? ' target="_blank" rel="noopener"' : '' ?>
             data-tags="<?= e($s['group']) ?>" aria-label="Open <?= e($s['label']) ?>">
            <span class="kc-hub-ico"><i class="fa-solid <?= e($s['icon']) ?>"></i></span>
            <span class="kc-hub-meta">
              <span class="kc-hub-name"><?= e($s['label']) ?></span>
              <span class="kc-hub-desc"><?= e($s['desc']) ?></span>
              <span class="kc-hub-foot">
                <span class="kc-hub-tag"><?= e($s['tag']) ?></span>
                <span class="kc-hub-go">Open <i class="fa-solid <?= !empty($s['ext']) ? 'fa-arrow-up-right-from-square' : 'fa-arrow-right' ?>"></i></span>
              </span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="kc-hub-empty" id="kcTilesEmpty" hidden>No results found. Try a different keyword or click <b>All</b>.</div>
    </div>
  </div>
<?php endif; ?>

<?php if ($client):
  $pb = $client['playbook']; ?>
  <div class="card" style="<?= $kc ? 'margin-top:18px;' : '' ?>background:linear-gradient(135deg,rgba(57,25,186,0.18),rgba(247,185,69,0.08) 60%,rgba(255,255,255,0.02));border:1px solid rgba(247,185,69,.3);">
    <div style="display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
      <div style="flex:0 0 60px;width:60px;height:60px;border-radius:14px;background:linear-gradient(135deg,#3919BA,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;"><i class="fa-solid fa-book-open"></i></div>
      <div style="flex:1;min-width:240px;">
        <div class="cd-hero-eyebrow"><span style="display:inline-block;width:8px;height:8px;background:var(--gold,#d4a64a);border-radius:50%;vertical-align:middle;margin-right:6px;"></span>Recommended first step</div>
        <h2 style="margin:0 0 4px;color:#fff;font-size:22px;font-weight:800;"><?= e($pb['title']) ?></h2>
        <p class="muted" style="margin:0;font-size:13.5px;line-height:1.55;"><?= e($pb['desc']) ?></p>
      </div>
      <a class="btn-portal-primary" href="<?= e($pb['url']) ?>" target="_blank" rel="noopener">Open playbook <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>

  <div class="card" style="margin-top:18px;">
    <div class="card-h">
      <h3 style="margin:0;"><i class="fa-solid fa-download"></i> Client Downloads</h3>
      <span class="muted small">Fast implementation tools: define measurable outcomes, build delegation systems, remove time drains.</span>
    </div>
    <div class="cd-dl-grid">
      <?php foreach ($client['resources'] as $r): ?>
        <a class="cd-dl-tile" href="<?= e($r['url']) ?>" target="_blank" rel="noopener" download style="--accent:<?= e($r['accent']) ?>">
          <span class="cd-dl-ico"><i class="fa-solid fa-file-pdf"></i></span>
          <div class="cd-dl-meta">
            <div class="cd-dl-title"><?= e($r['title']) ?></div>
            <div class="cd-dl-desc muted small"><?= e($r['description']) ?></div>
          </div>
          <span class="cd-dl-arrow"><i class="fa-solid fa-arrow-down"></i></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<?php if ($kc): ?>
<script>
(function () {
  var root = document.querySelector('.kc-hub');
  if (!root) return;
  var search = root.querySelector('#kcSearch');
  var tiles  = Array.prototype.slice.call(root.querySelectorAll('.kc-hub-tile'));
  var chips  = Array.prototype.slice.call(root.querySelectorAll('.kc-chip[data-filter]'));
  var clear  = root.querySelector('#kcClear');
  var empty  = root.querySelector('#kcTilesEmpty');
  var filter = 'all';

  function apply() {
    var q = (search && search.value || '').trim().toLowerCase();
    var shown = 0;
    tiles.forEach(function (t) {
      var tags = (t.getAttribute('data-tags') || '').toLowerCase();
      var hitFilter = filter === 'all' || tags.indexOf(filter) !== -1;
      var hitSearch = !q || t.innerText.toLowerCase().indexOf(q) !== -1;
      var show = hitFilter && hitSearch;
      t.style.display = show ? '' : 'none';
      if (show) shown++;
    });
    if (empty) empty.hidden = shown !== 0;
  }
  function setChip(f) {
    filter = f;
    chips.forEach(function (c) {
      var on = c.getAttribute('data-filter') === f;
      c.classList.toggle('is-on', on);
      c.setAttribute('aria-pressed', String(on));
    });
    apply();
  }
  chips.forEach(function (c) { c.addEventListener('click', function () { setChip(c.getAttribute('data-filter')); }); });
  if (search) search.addEventListener('input', apply);
  if (clear) clear.addEventListener('click', function () { if (search) { search.value = ''; search.focus(); } setChip('all'); });
  apply();
})();
</script>
<?php endif; ?>
