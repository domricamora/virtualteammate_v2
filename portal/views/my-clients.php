<?php
/** @var array $user @var array $clients @var array $requests @var int $pending_count */
$pageTitle = 'My Clients & VTs';
$subtitle  = 'The accounts you manage and the Virtual Teammates on each.';
$requests      = $requests ?? [];
$pending_count = (int) ($pending_count ?? 0);
$reqPill = static function (string $s): string {
    $map = ['approved' => ['is-active', 'Approved'], 'rejected' => ['is-late', 'Rejected'], 'pending' => ['is-pending', 'Pending']];
    [$cls, $lbl] = $map[$s] ?? ['is-pending', ucfirst($s)];
    return '<span class="rq-status rq-status--' . substr($cls, 3) . '">' . e($lbl) . '</span>';
};

$pill = static function (string $status): string {
    $s   = strtolower(trim($status));
    $cls = ['active' => 'pill-active', 'paused' => 'pill-paused', 'churned' => 'pill-churned'][$s] ?? 'pill-default';
    return '<span class="pill ' . $cls . '">' . e($s !== '' ? ucfirst($s) : 'Unknown') . '</span>';
};
$cInitial  = static fn (array $c): string => strtoupper(mb_substr(trim((string) ($c['company_name'] ?? '')) ?: '?', 0, 1));
$vtName    = static fn (array $v): string => trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? '')) ?: 'Virtual Teammate';
$vtInitial = static fn (array $v): string => strtoupper(mb_substr(trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? '')) ?: 'V', 0, 1));
$totalVts  = 0; foreach ($clients as $c) { $totalVts += (int) $c['vt_count']; }
?>

<div class="stat-grid mc-stats">
  <div class="stat-card mc-stat"><div class="stat-num"><?= count($clients) ?></div><div class="stat-lbl">Clients managed</div></div>
  <div class="stat-card mc-stat"><div class="stat-num"><?= (int) $totalVts ?></div><div class="stat-lbl">Active VTs</div></div>
  <a class="stat-card mc-stat" href="<?= e(portal_url('productivity')) ?>"><div class="stat-num"><i class="fa-solid fa-chart-line" style="color:var(--gold-lt);"></i></div><div class="stat-lbl">Productivity reports &rarr;</div></a>
</div>

<?php if (!empty($requests)): ?>
  <section class="card mc-reqs">
    <div class="card-h">
      <h3 style="margin:0;"><i class="fa-solid fa-inbox" style="color:var(--gold);margin-right:8px;"></i> VT Requests
        <?php if ($pending_count > 0): ?><span class="mc-req-badge"><?= $pending_count ?> to review</span><?php endif; ?>
      </h3>
    </div>
    <div class="mc-req-list">
      <?php foreach ($requests as $rq):
        $vtNm = trim(($rq['vt_first'] ?? '') . ' ' . ($rq['vt_last'] ?? '')) ?: 'Virtual Teammate';
        $clNm = trim(($rq['cl_first'] ?? '') . ' ' . ($rq['cl_last'] ?? '')) ?: ($rq['company_name'] ?? 'Client');
        $rRole = trim((string) ($rq['role_title'] ?? '')) ?: trim((string) ($rq['department'] ?? ''));
        $rCtry = trim((string) ($rq['vt_country'] ?? $rq['vt_user_country'] ?? ''));
        $rStatus = (string) ($rq['status'] ?? 'pending');
        $rMeta = implode(' · ', array_filter([$rRole, $rCtry]));
      ?>
        <div class="mc-req mc-req--<?= e($rStatus) ?>">
          <div class="mc-req-main">
            <div class="mc-req-co"><i class="fa-solid fa-building"></i> <?= e($rq['company_name'] ?? '') ?></div>
            <div class="mc-req-vt"><?= e($vtNm) ?><?php if ($rMeta !== ''): ?> <span class="muted small">· <?= e($rMeta) ?></span><?php endif; ?></div>
            <div class="muted small">Requested by <?= e($clNm) ?></div>
            <?php if ($rStatus !== 'pending' && trim((string) $rq['csm_note']) !== ''): ?>
              <div class="mc-req-note"><i class="fa-solid fa-quote-left"></i> <?= e($rq['csm_note']) ?></div>
            <?php endif; ?>
          </div>
          <div class="mc-req-side">
            <?php if ($rStatus === 'pending'): ?>
              <form method="post" action="<?= e(portal_url('request-vt.decide')) ?>" class="mc-req-form">
                <?= csrf_field() ?>
                <input type="hidden" name="request_id" value="<?= (int) $rq['id'] ?>">
                <input type="text" name="note" maxlength="1000" class="mc-req-input" placeholder="Optional note to the client…">
                <div class="mc-req-btns">
                  <button type="submit" name="action" value="approve" class="btn-portal-primary btn-sm"><i class="fa-solid fa-check"></i> Approve</button>
                  <button type="submit" name="action" value="reject" class="btn-portal-danger btn-sm"><i class="fa-solid fa-xmark"></i> Reject</button>
                </div>
              </form>
            <?php else: ?>
              <?= $reqPill($rStatus) ?>
            <?php endif; ?>
            <form method="post" action="<?= e(portal_url('request-vt.decide')) ?>" class="mc-req-del-form"
                  onsubmit="return confirm('Remove this request from your list? The client keeps their own copy.');">
              <?= csrf_field() ?>
              <input type="hidden" name="request_id" value="<?= (int) $rq['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button type="submit" class="mc-req-del"><i class="fa-solid fa-trash-can"></i> Remove from list</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<?php if (empty($clients)): ?>
  <div class="card mc-empty">
    <i class="fa-solid fa-building"></i>
    <h3>No clients assigned yet</h3>
    <p class="muted">When a super admin assigns client accounts to you, they'll appear here with their teams.</p>
  </div>
<?php else: ?>
  <div class="mc-grid">
    <?php foreach ($clients as $i => $c):
      $name    = trim((string) ($c['company_name'] ?? '')) ?: 'Client';
      $domain  = trim((string) ($c['company_domain'] ?? ''));
      $contact = trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''));
      $email   = trim((string) ($c['contact_email'] ?? $c['company_email'] ?? ''));
      $uid     = (int) ($c['user_id'] ?? 0);
      $vts     = (array) $c['vts'];
      $vtCount = (int) $c['vt_count'];
    ?>
      <article class="mc-card" style="animation-delay:<?= number_format(min($i, 12) * 0.05, 2) ?>s;">
        <header class="mc-card-top">
          <span class="mc-logo"><?= e($cInitial($c)) ?></span>
          <div class="mc-id">
            <?php if ($domain !== ''): ?><div class="mc-eyebrow"><?= e($domain) ?></div><?php endif; ?>
            <h3 class="mc-name"><?= e($name) ?></h3>
            <div class="mc-id-row"><?= $pill((string) ($c['contract_status'] ?? '')) ?><span class="mc-count"><?= $vtCount ?> VT<?= $vtCount === 1 ? '' : 's' ?></span></div>
          </div>
        </header>

        <?php if ($contact !== '' || $email !== ''): ?>
          <div class="mc-contact">
            <?php if ($contact !== ''): ?><span><i class="fa-solid fa-user"></i> <?= e($contact) ?></span><?php endif; ?>
            <?php if ($email !== ''): ?><span><i class="fa-solid fa-envelope"></i> <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></span><?php endif; ?>
          </div>
        <?php endif; ?>

        <div class="mc-team">
          <div class="mc-team-h">Team</div>
          <?php if (empty($vts)): ?>
            <p class="muted small" style="margin:0;">No active VTs on this account yet.</p>
          <?php else: ?>
            <ul class="mc-team-list">
              <?php foreach ($vts as $v):
                $thumb = media_thumb_src($v['photo_url'] ?? '');
                $role  = trim((string) ($v['role_title'] ?? '')) ?: (trim((string) ($v['department'] ?? '')) ?: 'Virtual Teammate');
              ?>
                <li class="mc-vt">
                  <button type="button" class="mc-vt-open" data-open-vt="<?= (int) $v['vt_id'] ?>" title="View <?= e($vtName($v)) ?>'s profile">
                    <?php if ($thumb !== ''): ?>
                      <img class="mc-vt-av" src="<?= e($thumb) ?>" alt="" loading="lazy"
                           onerror="this.onerror=null;this.outerHTML='<span class=&quot;mc-vt-av mc-vt-av-ph&quot;><?= e($vtInitial($v)) ?></span>';">
                    <?php else: ?>
                      <span class="mc-vt-av mc-vt-av-ph"><?= e($vtInitial($v)) ?></span>
                    <?php endif; ?>
                    <span class="mc-vt-meta">
                      <span class="mc-vt-name"><?= e($vtName($v)) ?></span>
                      <span class="mc-vt-role"><?= e($role) ?></span>
                    </span>
                  </button>
                  <a class="mc-vt-msg" href="<?= e(portal_url('messages', ['with' => (int) $v['vt_id']])) ?>" title="Message <?= e($vtName($v)) ?>"><i class="fa-solid fa-comment"></i></a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <div class="mc-actions">
          <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('productivity')) ?>"><i class="fa-solid fa-chart-line"></i> Reports</a>
          <?php if ($uid > 0): ?>
            <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('messages', ['with' => $uid])) ?>"><i class="fa-solid fa-comments"></i> Message client</a>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<style>
.mc-stats{margin-bottom:4px;}
.mc-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:18px;}
.mc-card{display:flex;flex-direction:column;gap:14px;padding:22px;border-radius:18px;
  background:var(--bg-1);backdrop-filter:var(--glass-blur);-webkit-backdrop-filter:var(--glass-blur);
  border:1px solid var(--line);box-shadow:0 12px 32px -18px rgba(13,8,40,.7);position:relative;overflow:hidden;
  transition:transform .22s cubic-bezier(.2,.7,.2,1),border-color .22s ease,box-shadow .22s ease;
  animation:mcIn .55s cubic-bezier(.2,.7,.2,1) both;}
.mc-card::before{content:'';position:absolute;inset:0 0 auto 0;height:3px;background:linear-gradient(90deg,#3919BA,#7c3aed 55%,#F6B845);opacity:.0;transition:opacity .22s ease;}
.mc-card:hover{transform:translateY(-5px);border-color:rgba(247,185,69,.45);box-shadow:0 26px 54px -22px rgba(247,185,69,.35);}
.mc-card:hover::before{opacity:1;}
@keyframes mcIn{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:none;}}
@media (prefers-reduced-motion:reduce){.mc-card{animation:none;}}
.mc-card-top{display:flex;align-items:flex-start;gap:13px;}
.mc-logo{width:52px;height:52px;flex:0 0 52px;border-radius:14px;display:flex;align-items:center;justify-content:center;
  font-size:22px;font-weight:800;color:var(--violet-deep);background:linear-gradient(135deg,var(--gold),var(--gold-dk));box-shadow:0 8px 20px rgba(0,0,0,.35);}
.mc-id{min-width:0;flex:1;}
.mc-eyebrow{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-mute);}
.mc-name{margin:2px 0 6px;font-size:19px;font-weight:800;color:#fff;letter-spacing:-.2px;}
.mc-id-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.mc-count{font-size:11px;font-weight:700;color:var(--gold-lt);background:rgba(247,185,69,.12);border:1px solid rgba(247,185,69,.3);padding:3px 9px;border-radius:30px;}
.mc-contact{display:flex;flex-direction:column;gap:5px;font-size:12.5px;color:var(--text-mute);}
.mc-contact i{color:var(--gold);width:15px;text-align:center;margin-right:4px;}
.mc-contact a{color:var(--gold-lt);}
.mc-team{border-top:1px solid var(--line);padding-top:12px;}
.mc-team-h{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--text-mute);margin-bottom:9px;}
.mc-team-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:6px;}
.mc-vt{display:flex;align-items:center;gap:8px;}
.mc-vt-open{flex:1;min-width:0;display:flex;align-items:center;gap:10px;padding:5px 7px;border-radius:10px;
  background:transparent;border:1px solid transparent;cursor:pointer;text-align:left;font-family:inherit;transition:background .15s,border-color .15s;}
.mc-vt-open:hover{background:rgba(255,255,255,.05);border-color:var(--line);}
.mc-vt-av{width:36px;height:36px;flex:0 0 36px;border-radius:50%;object-fit:cover;background:#1a1535;border:1px solid rgba(247,185,69,.3);}
.mc-vt-av-ph{display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;background:linear-gradient(135deg,#4a4178,#322a5a);}
.mc-vt-meta{display:flex;flex-direction:column;min-width:0;flex:1;}
.mc-vt-name{font-size:13.5px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.mc-vt-role{font-size:11.5px;color:var(--text-mute);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.mc-vt-msg{flex:0 0 auto;width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;
  color:var(--text-mute);border:1px solid var(--line);background:rgba(255,255,255,.04);text-decoration:none;transition:all .15s;}
.mc-vt-msg:hover{color:var(--gold-lt);border-color:rgba(247,185,69,.45);background:rgba(247,185,69,.1);}
.mc-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:auto;}
.mc-empty{text-align:center;padding:42px 22px;}
.mc-empty i{font-size:30px;color:var(--gold);margin-bottom:12px;}
.mc-empty h3{margin:0 0 8px;color:#fff;}
/* VT requests review */
.mc-reqs{margin-bottom:16px;}
.mc-req-badge{font-size:11px;font-weight:800;color:#1a1330;background:linear-gradient(135deg,var(--gold),#fbd97a);padding:3px 10px;border-radius:30px;margin-left:8px;}
.mc-req-list{display:flex;flex-direction:column;gap:10px;}
.mc-req{display:flex;gap:16px;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;
  padding:14px 16px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.03);}
.mc-req--pending{border-color:rgba(247,185,69,.4);background:rgba(247,185,69,.06);}
.mc-req-main{min-width:220px;flex:1;display:flex;flex-direction:column;gap:3px;}
.mc-req-co{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text-mute);}
.mc-req-co i{color:var(--gold);margin-right:4px;}
.mc-req-vt{font-size:15px;font-weight:800;color:#fff;}
.mc-req-note{margin-top:6px;font-size:12.5px;color:rgba(255,255,255,.8);background:rgba(255,255,255,.04);border-left:2px solid var(--gold);padding:6px 10px;border-radius:0 8px 8px 0;}
.mc-req-note i{color:var(--gold);margin-right:6px;font-size:10px;}
.mc-req-side{display:flex;flex-direction:column;align-items:flex-end;gap:9px;}
.mc-req-form{display:flex;flex-direction:column;gap:8px;min-width:260px;}
.mc-req-del-form{margin:0;}
.mc-req-del{display:inline-flex;align-items:center;gap:6px;background:transparent;border:1px solid var(--line-2);color:var(--text-mute);
  font-size:11.5px;font-weight:700;padding:6px 11px;border-radius:8px;cursor:pointer;transition:all .15s ease;}
.mc-req-del:hover{background:rgba(225,87,87,.16);border-color:rgba(225,87,87,.45);color:#f4baba;}
.mc-req-input{background:rgba(255,255,255,.06);border:1px solid var(--line-2);border-radius:8px;padding:8px 11px;font-size:13px;color:var(--text);font-family:inherit;width:100%;}
.mc-req-input:focus{outline:none;border-color:var(--gold);}
.mc-req-btns{display:flex;gap:8px;}
.rq-status{display:inline-flex;align-items:center;padding:5px 13px;border-radius:30px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;border:1px solid transparent;}
.rq-status--active{background:rgba(78,196,126,.16);color:#bcf0d2;border-color:rgba(78,196,126,.4);}
.rq-status--late{background:rgba(225,87,87,.18);color:#f4baba;border-color:rgba(225,87,87,.45);}
.rq-status--pending{background:rgba(247,185,69,.16);color:#ffe2a8;border-color:rgba(247,185,69,.4);}
@media (max-width:1100px){ .mc-grid{grid-template-columns:repeat(2,1fr);} }
@media (max-width:680px){ .mc-grid{grid-template-columns:1fr;} }
</style>

<?php include __DIR__ . '/_vt_profile_modal.php'; ?>
