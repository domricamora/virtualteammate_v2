<?php
/** @var array $user @var array $engagements */
$pageTitle = 'My CSM & Teammates';
$subtitle  = 'Your client, your CSM, and the teammates on your account.';

$personName = static function (array $p): string {
    $n = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''));
    return $n !== '' ? $n : (string) ($p['email'] ?? '—');
};
$initial = static function (array $p) use ($personName): string {
    return strtoupper(mb_substr($personName($p), 0, 1));
};
$roleLabel = static function (string $role): string {
    return [
        'client'    => 'Client',
        'csm'       => 'Client Success Manager',
        'vt_hired'  => 'Virtual Teammate',
        'vt_onpool' => 'Virtual Teammate',
        'super_admin' => 'Admin',
    ][$role] ?? 'Teammate';
};

// One person card with chat / email / call actions.
$card = static function (array $p, string $tag) use ($personName, $initial, $roleLabel): void {
    $pid   = (int) ($p['id'] ?? 0);
    $name  = $personName($p);
    $email = trim((string) ($p['email'] ?? ''));
    $phone = trim((string) ($p['phone'] ?? ''));
    $title = trim((string) ($p['job_title'] ?? '')) ?: $roleLabel((string) ($p['role'] ?? ''));
    $thumb = media_thumb_src($p['photo_url'] ?? '');
    ?>
    <div class="team-person">
      <div class="team-person-top">
        <?php if ($thumb !== ''): ?>
          <img class="team-av" src="<?= e($thumb) ?>" alt="" loading="lazy"
               onerror="this.onerror=null;this.outerHTML='<span class=&quot;team-av team-av--ph&quot;><?= e($initial($p)) ?></span>';">
        <?php else: ?>
          <span class="team-av team-av--ph"><?= e($initial($p)) ?></span>
        <?php endif; ?>
        <div class="team-person-meta">
          <div class="team-person-name"><?= e($name) ?> <span class="team-person-tag"><?= e($tag) ?></span></div>
          <div class="team-person-role"><?= e($title) ?></div>
          <?php if ($email !== ''): ?><div class="team-person-sub"><i class="fa-regular fa-envelope"></i> <?= e($email) ?></div><?php endif; ?>
          <?php if ($phone !== ''): ?><div class="team-person-sub"><i class="fa-solid fa-phone"></i> <?= e($phone) ?></div><?php endif; ?>
        </div>
      </div>
      <div class="team-actions">
        <?php if ($pid > 0): ?>
          <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('messages', ['with' => $pid])) ?>"><i class="fa-solid fa-comments"></i> Chat</a>
        <?php endif; ?>
        <?php if ($email !== ''): ?>
          <a class="btn-portal-secondary btn-sm" href="mailto:<?= e($email) ?>"><i class="fa-regular fa-envelope"></i> Email</a>
        <?php endif; ?>
        <?php if ($phone !== ''): ?>
          <a class="btn-portal-secondary btn-sm" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><i class="fa-solid fa-phone"></i> Call</a>
        <?php else: ?>
          <span class="btn-portal-secondary btn-sm team-nolink" title="No phone on file"><i class="fa-solid fa-phone-slash"></i> No phone</span>
        <?php endif; ?>
      </div>
    </div>
    <?php
};
?>

<?php if (empty($engagements)): ?>
  <div class="card prod-empty"><i class="fa-solid fa-people-group"></i><h3>No active engagement yet</h3><p class="muted">Once you're placed with a client, your client, CSM, and teammates will appear here.</p></div>
<?php else: ?>
  <?php foreach ($engagements as $eng): $c = $eng['client']; ?>
    <section class="card team-eng">
      <div class="team-client-head">
        <div>
          <div class="team-eyebrow"><i class="fa-solid fa-building"></i> Your client</div>
          <h2 class="team-company"><?= e($c['company_name']) ?></h2>
          <div class="team-client-sub">
            <?php if (!empty($c['company_domain'])): ?><span><i class="fa-solid fa-globe"></i> <?= e($c['company_domain']) ?></span><?php endif; ?>
            <?php if (!empty($c['company_email'])): ?><span><i class="fa-regular fa-envelope"></i> <?= e($c['company_email']) ?></span><?php endif; ?>
          </div>
        </div>
        <span class="team-status team-status--<?= e($c['contract_status'] ?? 'active') ?>"><?= e(ucfirst($c['contract_status'] ?? 'active')) ?></span>
      </div>

      <?php if ($eng['contact']): ?>
        <h3 class="team-sec-h">Client contact</h3>
        <div class="team-grid"><?php $card($eng['contact'], 'Client'); ?></div>
      <?php endif; ?>

      <h3 class="team-sec-h">Your CSM<?= count($eng['csms']) === 1 ? '' : 's' ?></h3>
      <?php if ($eng['csms']): ?>
        <div class="team-grid"><?php foreach ($eng['csms'] as $p) { $card($p, 'CSM'); } ?></div>
      <?php else: ?>
        <p class="muted small">No CSM assigned to this account yet.</p>
      <?php endif; ?>

      <h3 class="team-sec-h">Teammates on this account</h3>
      <?php if ($eng['teammates']): ?>
        <div class="team-grid"><?php foreach ($eng['teammates'] as $p) { $card($p, 'VT'); } ?></div>
      <?php else: ?>
        <p class="muted small">You're the only Virtual Teammate on this account right now.</p>
      <?php endif; ?>
    </section>
  <?php endforeach; ?>
<?php endif; ?>

<style>
.team-eng{margin-bottom:18px;}
.team-client-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;padding-bottom:16px;margin-bottom:6px;border-bottom:1px solid var(--line);}
.team-eyebrow{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--gold-lt);margin-bottom:4px;}
.team-company{margin:0;font-size:22px;font-weight:800;color:#fff;}
.team-client-sub{display:flex;gap:16px;flex-wrap:wrap;margin-top:6px;font-size:12.5px;color:var(--text-mute);}
.team-client-sub i{color:var(--gold-lt);margin-right:4px;}
.team-status{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;padding:5px 11px;border-radius:999px;border:1px solid var(--line-2);color:#fff;background:rgba(255,255,255,.06);}
.team-status--active{color:#7ee0a8;background:rgba(34,197,94,.14);border-color:rgba(34,197,94,.35);}
.team-sec-h{margin:18px 0 12px;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:var(--gold,#d4a64a);border-left:3px solid var(--gold);padding-left:10px;}
.team-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;}
.team-person{display:flex;flex-direction:column;gap:12px;padding:15px;border:1px solid var(--line);border-radius:14px;background:rgba(255,255,255,.04);}
.team-person-top{display:flex;gap:12px;align-items:flex-start;}
.team-av{width:48px;height:48px;flex:0 0 48px;border-radius:50%;object-fit:cover;background:#1a1535;border:2px solid rgba(247,185,69,.3);}
.team-av--ph{display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#fff;background:linear-gradient(135deg,#4a4178,#322a5a);}
.team-person-meta{flex:1;min-width:0;}
.team-person-name{font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.team-person-tag{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--violet-deep);background:var(--gold-lt);border-radius:999px;padding:2px 7px;}
.team-person-role{font-size:12px;color:var(--gold-lt);margin-top:2px;}
.team-person-sub{font-size:12px;color:var(--text-mute);margin-top:3px;word-break:break-all;}
.team-person-sub i{width:14px;color:var(--gold-lt);}
.team-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:auto;}
.team-actions .btn-sm{flex:1;justify-content:center;min-width:84px;}
.team-nolink{opacity:.5;cursor:not-allowed;}
@media (max-width:680px){ .team-grid{grid-template-columns:1fr;} }
</style>
