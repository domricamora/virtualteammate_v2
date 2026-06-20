<?php /** @var array $user @var int $leadTotal @var int $lead7 @var int $lead30
 * @var array $byForm @var array $recentLeads @var array $events @var array $stages
 * @var int $dealTotal @var string $pipelineError */
$pageTitle = $title ?? 'Client Funnel';
$subtitle  = $subtitle ?? '';
$stages    = $stages ?? []; $byForm = $byForm ?? []; $recentLeads = $recentLeads ?? [];
$events    = $events ?? [];
$maxStage  = 0; foreach ($stages as $s) { $maxStage = max($maxStage, (int) $s['count']); }
$maxForm   = 0; foreach ($byForm as $f) { $maxForm = max($maxForm, (int) $f['n']); }
$fmtWhen   = static fn($t) => $t ? date('M j, g:ia', strtotime((string) $t)) : '';
?>

<div class="stat-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
  <?php foreach ([['Leads (all time)', $leadTotal],['Leads · 7 days', $lead7],['Leads · 30 days', $lead30],['Deals in pipeline', $dealTotal ?? 0]] as $s): ?>
    <div class="card" style="padding:18px 20px;">
      <div style="font-size:30px;font-weight:800;color:var(--gold,#d4a64a);line-height:1;"><?= (int) $s[1] ?></div>
      <div class="muted small" style="margin-top:6px;text-transform:uppercase;letter-spacing:.6px;font-weight:700;"><?= e($s[0]) ?></div>
    </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-h"><h3><i class="fa-solid fa-filter" style="color:var(--gold);margin-right:8px;"></i> Deal pipeline — VT Client Onboarding</h3></div>
  <?php if ($pipelineError): ?>
    <p class="muted small" style="margin:0;">Couldn't read the live pipeline: <code><?= e($pipelineError) ?></code>. Check the HubSpot token/scopes.</p>
  <?php elseif (!$stages): ?>
    <p class="muted small" style="margin:0;">No pipeline stages found yet.</p>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <?php foreach ($stages as $s): $w = $maxStage > 0 ? round(($s['count'] / $maxStage) * 100) : 0; ?>
        <div style="display:flex;align-items:center;gap:14px;">
          <div style="flex:0 0 190px;font-size:13.5px;color:#fff;<?= $s['closed'] ? 'opacity:.7;' : '' ?>"><?= e($s['label']) ?></div>
          <div style="flex:1;height:22px;background:rgba(255,255,255,.06);border-radius:8px;overflow:hidden;">
            <div style="height:100%;width:<?= max($w, ($s['count'] > 0 ? 6 : 0)) ?>%;background:linear-gradient(90deg,var(--gold,#dfa949),#f5d27a);border-radius:8px;"></div>
          </div>
          <div style="flex:0 0 36px;text-align:right;font-weight:800;color:#fff;"><?= (int) $s['count'] ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
  <div class="card">
    <div class="card-h"><h3><i class="fa-solid fa-list-ul" style="color:var(--gold);margin-right:8px;"></i> Lead capture by form</h3></div>
    <?php if (!$byForm): ?>
      <p class="muted small" style="margin:0;">No leads captured yet.</p>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:9px;">
        <?php foreach ($byForm as $f): $w = $maxForm > 0 ? round(($f['n'] / $maxForm) * 100) : 0; ?>
          <div style="display:flex;align-items:center;gap:12px;">
            <div style="flex:1;min-width:0;font-size:13px;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($f['k']) ?></div>
            <div style="flex:0 0 120px;height:8px;background:rgba(255,255,255,.06);border-radius:99px;overflow:hidden;">
              <div style="height:100%;width:<?= max($w, 6) ?>%;background:var(--gold,#dfa949);border-radius:99px;"></div>
            </div>
            <div style="flex:0 0 30px;text-align:right;font-weight:700;color:#fff;font-size:13px;"><?= (int) $f['n'] ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-h"><h3><i class="fa-solid fa-bolt" style="color:var(--gold);margin-right:8px;"></i> Recent HubSpot activity</h3></div>
    <?php if (!$events): ?>
      <p class="muted small" style="margin:0;">No webhook events yet. HubSpot posts to <code>/hubapi</code> when contacts/deals change.</p>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:8px;max-height:320px;overflow:auto;">
        <?php foreach ($events as $ev): ?>
          <div style="font-size:12.5px;color:var(--text-soft,#c9c8e2);border-bottom:1px solid rgba(255,255,255,.06);padding-bottom:6px;">
            <strong style="color:#fff;"><?= e($ev['subscription_type'] ?: 'event') ?></strong>
            <?php if ($ev['property_name']): ?> · <?= e($ev['property_name']) ?> → <code><?= e($ev['property_value']) ?></code><?php endif; ?>
            <?php if ($ev['object_id']): ?> · #<?= e($ev['object_id']) ?><?php endif; ?>
            <span class="muted" style="float:right;"><?= e($fmtWhen($ev['created_at'])) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-h"><h3><i class="fa-solid fa-bullseye" style="color:var(--gold);margin-right:8px;"></i> Latest leads</h3></div>
  <?php if (!$recentLeads): ?>
    <p class="muted small" style="margin:0;">No leads yet.</p>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
      <thead><tr style="text-align:left;color:#8a8aa0;">
        <th style="padding:6px 8px;">Name</th><th style="padding:6px 8px;">Email</th><th style="padding:6px 8px;">Form</th><th style="padding:6px 8px;">When</th>
      </tr></thead>
      <tbody>
      <?php foreach ($recentLeads as $l): ?>
        <tr style="border-top:1px solid rgba(255,255,255,.06);">
          <td style="padding:6px 8px;color:#fff;"><?= e($l['name'] ?: '—') ?></td>
          <td style="padding:6px 8px;color:var(--text-soft,#c9c8e2);"><?= e($l['email']) ?></td>
          <td style="padding:6px 8px;color:var(--text-mute,#a8a7c3);"><?= e($l['src']) ?></td>
          <td style="padding:6px 8px;color:var(--text-mute,#a8a7c3);white-space:nowrap;"><?= e($fmtWhen($l['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
