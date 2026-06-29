<?php
/** @var array $user @var array $pool @var array $links @var string $base_url @var int $now */
$pageTitle = 'Special Links';
$subtitle  = 'Generate an expiring, no-login link that opens a single VT profile.';

$vtName = static fn (array $v): string => trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? '')) ?: 'Virtual Teammate';
$statusOf = static function (array $l, int $now): array {
    if ((int) $l['revoked'] === 1)      { return ['revoked', 'Revoked']; }
    if ((int) $l['expires_at'] <= $now) { return ['expired', 'Expired']; }
    return ['active', 'Active'];
};
$expiryLabel = static function (int $expires, int $now): string {
    if ($expires <= $now) { return 'expired ' . date('M j', $expires); }
    $h = (int) ceil(($expires - $now) / 3600);
    if ($h >= 48) { return 'expires in ' . (int) round($h / 24) . ' days'; }
    return 'expires in ' . $h . 'h';
};
$activeCount = 0; foreach ($links as $l) { if ((int) $l['revoked'] !== 1 && (int) $l['expires_at'] > $now) { $activeCount++; } }
?>

<div class="sl-grid">
  <!-- Generator -->
  <section class="card sl-gen">
    <div class="card-h"><h3 style="margin:0;"><i class="fa-solid fa-wand-magic-sparkles" style="color:var(--gold);margin-right:8px;"></i> Generate a link</h3></div>
    <p class="muted small" style="margin:0 0 14px;">Creates a secure, expiring link that opens the selected teammate's profile — no login required. Share it with a prospect or stakeholder.</p>
    <?php if (empty($pool)): ?>
      <p class="muted">No available teammates to link to right now.</p>
    <?php else: ?>
      <form method="post" action="<?= e(portal_url('special-links')) ?>" class="sl-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="generate">
        <label class="sl-label">Virtual Teammate
          <?php
            $optionRow = static function (array $v) use ($vtName): string {
                $r = trim((string) ($v['role_title'] ?? '')) ?: trim((string) ($v['department'] ?? ''));
                return '<option value="' . (int) $v['id'] . '">' . e($vtName($v)) . ($r !== '' ? ' — ' . e($r) : '') . '</option>';
            };
            $engaged   = array_filter($pool, static fn (array $v): bool => ($v['role'] ?? '') === 'vt_hired');
            $available = array_filter($pool, static fn (array $v): bool => ($v['role'] ?? '') !== 'vt_hired');
          ?>
          <select name="vt_id" class="sl-select" required>
            <option value="">Choose a teammate…</option>
            <?php if ($engaged): ?>
              <optgroup label="Hired / Engaged">
                <?php foreach ($engaged as $v) { echo $optionRow($v); } ?>
              </optgroup>
            <?php endif; ?>
            <?php if ($available): ?>
              <optgroup label="Available (Pool)">
                <?php foreach ($available as $v) { echo $optionRow($v); } ?>
              </optgroup>
            <?php endif; ?>
          </select>
        </label>
        <label class="sl-label">Link lifetime
          <select name="hours" class="sl-select">
            <option value="24">24 hours</option>
            <option value="72" selected>3 days</option>
            <option value="168">1 week</option>
            <option value="720">30 days</option>
          </select>
        </label>
        <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-link"></i> Generate link</button>
      </form>
    <?php endif; ?>
  </section>

  <!-- Links -->
  <section class="card sl-list-card">
    <div class="card-h">
      <h3 style="margin:0;"><i class="fa-solid fa-list-ul" style="color:var(--gold);margin-right:8px;"></i> Your links
        <span class="muted small">(<?= $activeCount ?> active)</span>
      </h3>
    </div>
    <?php if (empty($links)): ?>
      <div class="sl-empty"><i class="fa-solid fa-link-slash"></i><p class="muted">No links yet. Generate one to share a teammate's profile.</p></div>
    <?php else: ?>
      <div class="sl-list">
        <?php foreach ($links as $l):
          [$stKey, $stLbl] = $statusOf($l, $now);
          $url  = $base_url . '?t=' . rawurlencode($l['token']);
          $live = $stKey === 'active';
        ?>
          <div class="sl-item sl-item--<?= $stKey ?>">
            <div class="sl-item-top">
              <div class="sl-item-vt"><?= e($vtName($l)) ?><?php if (!empty($l['role_title'])): ?> <span class="muted small">· <?= e($l['role_title']) ?></span><?php endif; ?></div>
              <span class="sl-status sl-status--<?= $stKey ?>"><?= e($stLbl) ?></span>
            </div>
            <div class="sl-url-row">
              <input type="text" class="sl-url" value="<?= e($url) ?>" readonly onclick="this.select();">
              <button type="button" class="btn-portal-secondary btn-sm sl-copy" data-url="<?= e($url) ?>"><i class="fa-solid fa-copy"></i> Copy</button>
            </div>
            <div class="sl-item-foot">
              <span class="muted small"><i class="fa-regular fa-clock"></i> <?= e($expiryLabel((int) $l['expires_at'], $now)) ?> · created <?= e(date('M j, Y', strtotime((string) $l['created_at']))) ?></span>
              <?php if ($live): ?>
                <div class="sl-foot-btns">
                  <a class="btn-portal-secondary btn-sm" href="<?= e($url) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> Open</a>
                  <form method="post" action="<?= e(portal_url('special-links')) ?>" onsubmit="return confirm('Revoke this link? It will stop working immediately.');" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="revoke">
                    <input type="hidden" name="token" value="<?= e($l['token']) ?>">
                    <button type="submit" class="btn-portal-danger btn-sm"><i class="fa-solid fa-ban"></i> Revoke</button>
                  </form>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>

<style>
.sl-grid{display:grid;grid-template-columns:minmax(0,380px) minmax(0,1fr);gap:18px;align-items:start;}
.sl-form{display:flex;flex-direction:column;gap:14px;}
.sl-label{display:flex;flex-direction:column;gap:6px;font-size:12px;font-weight:700;color:var(--text-mute);text-transform:uppercase;letter-spacing:.8px;}
.sl-select{background:rgba(255,255,255,.06);border:1px solid var(--line-2);border-radius:10px;padding:11px 12px;font-size:14px;color:var(--text);font-family:inherit;font-weight:500;text-transform:none;letter-spacing:0;}
.sl-select:focus{outline:none;border-color:var(--gold);}
.sl-list{display:flex;flex-direction:column;gap:12px;}
.sl-item{padding:15px 16px;border-radius:14px;border:1px solid var(--line);background:rgba(255,255,255,.03);}
.sl-item--active{border-color:rgba(78,196,126,.3);}
.sl-item--expired,.sl-item--revoked{opacity:.7;}
.sl-item-top{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;}
.sl-item-vt{font-size:15px;font-weight:800;color:#fff;}
.sl-status{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;padding:4px 10px;border-radius:30px;border:1px solid transparent;}
.sl-status--active{background:rgba(78,196,126,.16);color:#bcf0d2;border-color:rgba(78,196,126,.4);}
.sl-status--expired{background:rgba(247,185,69,.14);color:#ffe2a8;border-color:rgba(247,185,69,.35);}
.sl-status--revoked{background:rgba(225,87,87,.16);color:#f4baba;border-color:rgba(225,87,87,.4);}
.sl-url-row{display:flex;gap:8px;margin-bottom:10px;}
.sl-url{flex:1;min-width:0;background:rgba(0,0,0,.25);border:1px solid var(--line);border-radius:9px;padding:9px 11px;font-size:12.5px;color:var(--gold-lt);font-family:ui-monospace,monospace;}
.sl-url:focus{outline:none;border-color:var(--gold);}
.sl-copy{flex:0 0 auto;}
.sl-item-foot{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;}
.sl-foot-btns{display:flex;gap:8px;}
.sl-empty{text-align:center;padding:34px 18px;}
.sl-empty i{font-size:26px;color:var(--gold);margin-bottom:10px;}
@media (max-width:900px){ .sl-grid{grid-template-columns:1fr;} }
</style>
<script>
(function(){
  document.querySelectorAll('.sl-copy').forEach(function(btn){
    btn.addEventListener('click', function(){
      var url = btn.getAttribute('data-url');
      var done = function(){ var o = btn.innerHTML; btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied'; setTimeout(function(){ btn.innerHTML = o; }, 1600); };
      if (navigator.clipboard && navigator.clipboard.writeText){ navigator.clipboard.writeText(url).then(done, done); }
      else { var t = document.createElement('textarea'); t.value = url; document.body.appendChild(t); t.select(); try{document.execCommand('copy');}catch(e){} t.remove(); done(); }
    });
  });
})();
</script>
