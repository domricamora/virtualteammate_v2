<?php
/** @var array $user @var array $pool @var int $team_count @var array $requests */
$pageTitle = 'Request an Additional VT';
$subtitle  = 'Hand-picked available teammates ready to join your team.';
$requests  = $requests ?? [];
$rqPill = static function (string $s): string {
    $map = ['approved' => ['approved', 'Approved', 'fa-circle-check'], 'rejected' => ['rejected', 'Declined', 'fa-circle-xmark'], 'pending' => ['pending', 'Pending review', 'fa-clock']];
    [$cls, $lbl, $ic] = $map[$s] ?? ['pending', ucfirst($s), 'fa-clock'];
    return '<span class="rq-st rq-st--' . $cls . '"><i class="fa-solid ' . $ic . '"></i> ' . e($lbl) . '</span>';
};

$nameOf = static function (array $v): string {
    $n = trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''));
    return $n !== '' ? $n : 'Virtual Teammate';
};
$initial = static fn (array $v): string => strtoupper(mb_substr(trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? '')) ?: 'V', 0, 1));
$excerpt = static function (string $s, int $len = 150): string {
    $s = trim(preg_replace('/\s+/', ' ', strip_tags($s)));
    return $s === '' ? '' : mb_strimwidth($s, 0, $len, '…');
};
?>

<div class="rq-hero card">
  <div class="rq-hero-l">
    <div class="rq-eyebrow"><i class="fa-solid fa-wand-magic-sparkles"></i> Suggested teammates</div>
    <h2 class="rq-title">Ready to grow your team? Here are strong matches.</h2>
    <p class="rq-copy">These available Virtual Teammates are suggested based on the work your current team already supports — excluding people you've already hired. Request anyone below and your Client Success Manager will arrange the next step.</p>
    <a class="btn-portal-secondary rq-hero-btn" href="<?= e(site_url('virtual-teammates/')) ?>" target="_blank" rel="noopener">
      <i class="fa-solid fa-users-viewfinder"></i> View more Virtual Teammates
    </a>
  </div>
  <div class="rq-stats">
    <div class="rq-stat"><strong><?= count($pool) ?></strong><span>Profiles shown</span></div>
    <div class="rq-stat"><strong><?= (int) $team_count ?></strong><span>Current team</span></div>
  </div>
</div>

<?php if (!empty($requests)): ?>
  <section class="card rq-reqs">
    <div class="card-h">
      <h3 style="margin:0;"><i class="fa-solid fa-clipboard-list" style="color:var(--gold);margin-right:8px;"></i> Your requests</h3>
      <span class="muted small">Status updates from your Client Success Manager</span>
    </div>
    <ul class="rq-req-list">
      <?php foreach ($requests as $r):
        $nm   = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: 'Virtual Teammate';
        $role = trim((string) ($r['role_title'] ?? '')) ?: trim((string) ($r['department'] ?? ''));
        $st   = (string) ($r['status'] ?? 'pending');
        $when = (string) ($r['created_at'] ?? '');
      ?>
        <li class="rq-req">
          <div class="rq-req-l">
            <div class="rq-req-name"><?= e($nm) ?><?php if ($role !== ''): ?> <span class="muted small">· <?= e($role) ?></span><?php endif; ?></div>
            <div class="muted small">Requested <?= $when !== '' ? e(date('M j, Y', strtotime($when))) : '' ?></div>
            <?php if ($st !== 'pending' && trim((string) $r['csm_note']) !== ''): ?>
              <div class="rq-req-note"><i class="fa-solid fa-quote-left"></i> <?= e($r['csm_note']) ?></div>
            <?php endif; ?>
          </div>
          <div class="rq-req-r">
            <?= $rqPill($st) ?>
            <form method="post" action="<?= e(portal_url('request-vt')) ?>" class="rq-del-form"
                  onsubmit="return confirm('Remove this request from your list? Your Client Success Manager keeps their own copy.');">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="request_id" value="<?= (int) ($r['id'] ?? 0) ?>">
              <button type="submit" class="rq-del" title="Remove from my list" aria-label="Remove from my list"><i class="fa-solid fa-trash-can"></i></button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endif; ?>

<?php if (empty($pool)): ?>
  <div class="card rq-empty">
    <i class="fa-solid fa-user-clock"></i>
    <h3>No new profiles to review right now</h3>
    <p class="muted">Your team already covers our current bench. Message your Client Success Manager and we'll source a tailored match.</p>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('messages')) ?>"><i class="fa-solid fa-comments"></i> Message my CSM</a>
  </div>
<?php else: ?>
  <div class="rq-grid">
    <?php foreach ($pool as $v):
      $vid   = (int) $v['id'];
      $name  = $nameOf($v);
      $role  = trim((string) ($v['role_title'] ?? '')) ?: (trim((string) ($v['department'] ?? '')) ?: 'Virtual Teammate');
      $dept  = trim((string) ($v['department'] ?? ''));
      $ctry  = trim((string) ($v['country'] ?? ''));
      $yrs   = (int) ($v['experience_years'] ?? 0);
      $thumb = media_thumb_src($v['photo_url'] ?? '');
      $scores = array_values(array_filter([
          trim((string) ($v['predictive_index'] ?? '')),
          trim((string) ($v['quiz_tier'] ?? '')),
          trim((string) ($v['engagement_score'] ?? '')),
      ], static fn ($s) => $s !== ''));
      $sum = $excerpt((string) ($v['summary'] ?? ''));
    ?>
      <article class="rq-card">
        <div class="rq-card-top">
          <?php if ($thumb !== ''): ?>
            <img class="rq-avatar" src="<?= e($thumb) ?>" alt="" loading="lazy"
                 onerror="this.onerror=null;this.outerHTML='<span class=&quot;rq-avatar rq-avatar-ph&quot;><?= e($initial($v)) ?></span>';">
          <?php else: ?>
            <span class="rq-avatar rq-avatar-ph"><?= e($initial($v)) ?></span>
          <?php endif; ?>
          <div class="rq-id">
            <h3 class="rq-name"><?= e($name) ?></h3>
            <div class="rq-role"><?= e($role) ?></div>
          </div>
        </div>

        <div class="rq-chips">
          <span class="rq-chip rq-chip--avail"><i class="fa-solid fa-circle-check"></i> Available now</span>
          <?php if ($dept !== ''): ?><span class="rq-chip"><?= e($dept) ?></span><?php endif; ?>
          <?php if ($ctry !== ''): ?><span class="rq-chip"><i class="fa-solid fa-location-dot"></i> <?= e($ctry) ?></span><?php endif; ?>
          <?php if ($yrs > 0): ?><span class="rq-chip"><i class="fa-solid fa-briefcase"></i> <?= $yrs ?>y</span><?php endif; ?>
        </div>

        <?php if ($scores): ?>
          <div class="rq-scores">
            <?php foreach ($scores as $s): ?><span class="rq-score"><?= e($s) ?></span><?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ($sum !== ''): ?><p class="rq-sum"><?= e($sum) ?></p><?php endif; ?>

        <form method="post" action="<?= e(portal_url('request-vt')) ?>" class="rq-actions">
          <?= csrf_field() ?>
          <input type="hidden" name="vt_id" value="<?= $vid ?>">
          <button type="submit" class="btn-portal-primary rq-btn"><i class="fa-solid fa-paper-plane"></i> Request <?= e($v['first_name'] ?: 'this teammate') ?></button>
        </form>
      </article>
    <?php endforeach; ?>
  </div>
  <div class="rq-more">
    <a class="btn-portal-primary" href="<?= e(site_url('virtual-teammates/')) ?>" target="_blank" rel="noopener">
      <i class="fa-solid fa-users-viewfinder"></i> View more Virtual Teammates <i class="fa-solid fa-arrow-up-right-from-square"></i>
    </a>
  </div>
<?php endif; ?>

<style>
.rq-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;
  background:linear-gradient(135deg,rgba(57,25,186,.20),rgba(124,58,237,.12) 60%,rgba(247,185,69,.10));}
.rq-hero-l{flex:1;min-width:260px;}
.rq-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11px;font-weight:800;letter-spacing:1.2px;
  text-transform:uppercase;color:var(--gold-lt);background:rgba(247,185,69,.12);border:1px solid rgba(247,185,69,.3);
  padding:5px 12px;border-radius:30px;}
.rq-title{font-size:24px;font-weight:800;color:#fff;letter-spacing:-.3px;margin:12px 0 8px;line-height:1.15;}
.rq-copy{font-size:13.5px;line-height:1.6;color:var(--text-mute);max-width:760px;margin:0;}
.rq-hero-btn{margin-top:16px;}
.rq-more{display:flex;justify-content:center;margin-top:22px;}
.rq-stats{display:flex;gap:12px;}
.rq-stat{min-width:104px;text-align:center;padding:14px 16px;border-radius:14px;background:rgba(255,255,255,.06);border:1px solid var(--line);}
.rq-stat strong{display:block;font-size:26px;font-weight:800;color:var(--gold-lt);line-height:1;}
.rq-stat span{display:block;margin-top:6px;font-size:10.5px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-mute);}

.rq-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:18px;}
.rq-card{display:flex;flex-direction:column;gap:13px;padding:20px;border-radius:16px;
  background:var(--bg-1);backdrop-filter:var(--glass-blur);-webkit-backdrop-filter:var(--glass-blur);
  border:1px solid var(--line);box-shadow:0 10px 30px -16px rgba(13,8,40,.6);transition:transform .15s ease,border-color .15s ease;}
.rq-card:hover{transform:translateY(-3px);border-color:rgba(247,185,69,.4);}
.rq-card-top{display:flex;align-items:center;gap:13px;}
.rq-avatar{width:62px;height:62px;border-radius:50%;object-fit:cover;flex:0 0 62px;
  border:2px solid rgba(247,185,69,.35);background:#1a1535;box-shadow:0 8px 20px rgba(0,0,0,.4);}
.rq-avatar-ph{display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;
  background:linear-gradient(135deg,#4a4178,#322a5a);}
.rq-id{min-width:0;}
.rq-name{margin:0;font-size:18px;font-weight:800;color:#fff;letter-spacing:-.2px;}
.rq-role{font-size:12.5px;color:var(--text-mute);margin-top:2px;}
.rq-chips,.rq-scores{display:flex;flex-wrap:wrap;gap:6px;}
.rq-chip{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:30px;font-size:11px;font-weight:600;
  background:rgba(255,255,255,.06);color:rgba(255,255,255,.82);border:1px solid var(--line);}
.rq-chip i{color:var(--gold);font-size:10px;}
.rq-chip--avail{background:rgba(78,196,126,.16);color:#bcf0d2;border-color:rgba(78,196,126,.4);}
.rq-chip--avail i{color:#4ec47e;}
.rq-score{display:inline-flex;align-items:center;padding:4px 10px;border-radius:8px;font-size:11px;font-weight:700;
  background:rgba(57,25,186,.22);color:#cdbcfb;border:1px solid rgba(187,167,250,.3);}
.rq-sum{font-size:13px;line-height:1.55;color:var(--text-mute);margin:0;flex:1;}
.rq-actions{margin-top:auto;}
.rq-btn{width:100%;justify-content:center;}

.rq-empty{text-align:center;padding:42px 22px;}
.rq-empty i{font-size:30px;color:var(--gold);margin-bottom:12px;}
.rq-empty h3{margin:0 0 8px;color:#fff;}
.rq-reqs{margin-top:16px;}
.rq-req-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px;}
.rq-req{display:flex;gap:14px;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;
  padding:13px 15px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.03);}
.rq-req-l{min-width:200px;flex:1;}
.rq-req-r{display:flex;align-items:center;gap:10px;flex:0 0 auto;}
.rq-del-form{margin:0;display:inline-flex;}
.rq-del{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;cursor:pointer;
  background:rgba(225,87,87,.1);border:1px solid rgba(225,87,87,.3);color:#f4baba;font-size:13px;transition:all .15s ease;}
.rq-del:hover{background:rgba(225,87,87,.22);border-color:rgba(225,87,87,.55);color:#fff;}
.rq-req-name{font-size:14.5px;font-weight:700;color:#fff;}
.rq-req-note{margin-top:7px;font-size:12.5px;color:rgba(255,255,255,.82);background:rgba(255,255,255,.04);border-left:2px solid var(--gold);padding:7px 11px;border-radius:0 8px 8px 0;}
.rq-req-note i{color:var(--gold);margin-right:6px;font-size:10px;}
.rq-st{display:inline-flex;align-items:center;gap:6px;padding:5px 13px;border-radius:30px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;border:1px solid transparent;white-space:nowrap;}
.rq-st--approved{background:rgba(78,196,126,.16);color:#bcf0d2;border-color:rgba(78,196,126,.4);}
.rq-st--rejected{background:rgba(225,87,87,.18);color:#f4baba;border-color:rgba(225,87,87,.45);}
.rq-st--pending{background:rgba(247,185,69,.16);color:#ffe2a8;border-color:rgba(247,185,69,.4);}
.rq-empty .btn-portal-primary{margin-top:14px;}

@media (max-width:1100px){ .rq-grid{grid-template-columns:repeat(2,1fr);} }
@media (max-width:640px){
  .rq-grid{grid-template-columns:1fr;}
  .rq-hero{flex-direction:column;align-items:flex-start;}
  .rq-stats{width:100%;}
  .rq-stat{flex:1;}
}
</style>
