<?php
/** @var array $user @var string $company @var array $rows @var array $totals @var string $error */
$pageTitle = 'My Invoices';
$subtitle  = $company !== '' ? ('Billing records for ' . $company) : 'Your current and past billing records.';

$money = static fn (float $n): string => '$' . number_format($n, 2);
$date  = static fn (int $ts): string => $ts > 0 ? date('M j, Y', $ts) : '—';
$pill  = static function (string $key, string $label): string {
    $cls = ['paid' => 'inv-pill--paid', 'late' => 'inv-pill--late', 'pending' => 'inv-pill--pending'][$key] ?? 'inv-pill--pending';
    return '<span class="inv-pill ' . $cls . '">' . e($label !== '' ? $label : ucfirst($key)) . '</span>';
};

// Current (outstanding) first, then paid — each already ordered by due desc.
usort($rows, static function ($a, $b) {
    $oa = $a['status_key'] === 'paid' ? 1 : 0;
    $ob = $b['status_key'] === 'paid' ? 1 : 0;
    if ($oa !== $ob) { return $oa <=> $ob; }
    return $b['due_ts'] <=> $a['due_ts'];
});
$total = count($rows);
?>

<?php if ($error !== '' && empty($rows)): ?>
  <div class="card inv-empty">
    <i class="fa-solid fa-file-invoice-dollar"></i>
    <h3>Billing records</h3>
    <p class="muted"><?= e($error) ?></p>
  </div>
<?php else: ?>

  <!-- Summary -->
  <div class="stat-grid inv-stats">
    <div class="stat-card">
      <div class="stat-num"><?= e($money((float) $totals['billed'])) ?></div>
      <div class="stat-lbl">Total billed</div>
    </div>
    <div class="stat-card">
      <div class="stat-num" style="color:<?= $totals['outstanding'] > 0 ? '#ffd9a0' : 'var(--gold-lt)' ?>;"><?= e($money((float) $totals['outstanding'])) ?></div>
      <div class="stat-lbl">Outstanding</div>
    </div>
    <div class="stat-card">
      <div class="stat-num"><?= (int) $totals['open'] ?></div>
      <div class="stat-lbl">Open invoices</div>
    </div>
    <div class="stat-card">
      <div class="stat-num"><?= (int) $totals['paid'] ?></div>
      <div class="stat-lbl">Paid invoices</div>
    </div>
  </div>

  <div class="card" data-list>
    <div class="card-h">
      <div class="list-toolbar">
        <input type="search" data-list-search placeholder="Search invoice #, status, amount…" autocomplete="off">
        <select data-list-pagesize>
          <option value="10" selected>10 / page</option>
          <option value="25">25 / page</option>
          <option value="50">50 / page</option>
          <option value="0">All</option>
        </select>
        <span class="list-counter">Total <strong><?= (int) $total ?></strong> invoice<?= $total === 1 ? '' : 's' ?> &middot; <span data-list-counter>—</span></span>
      </div>
    </div>

    <div class="inv-table-wrap">
      <table class="data-table compact inv-table" data-paginate>
        <thead>
          <tr><th>Invoice</th><th>Amount</th><th>Status</th><th>Due</th><th>Paid</th><th></th></tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr data-empty><td colspan="6" class="muted" style="text-align:center;padding:24px;">No invoices found.</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td>
                <div class="inv-num"><?= e($r['number']) ?></div>
                <?php if (!empty($r['title']) && $r['title'] !== 'Billing record'): ?>
                  <div class="muted small"><?= e($r['title']) ?></div>
                <?php endif; ?>
              </td>
              <td class="inv-amt"><?= e($money((float) $r['amount'])) ?></td>
              <td><?= $pill($r['status_key'], $r['status_label']) ?></td>
              <td class="muted small" style="white-space:nowrap;"><?= e($date((int) $r['due_ts'])) ?></td>
              <td class="muted small" style="white-space:nowrap;"><?= e($date((int) $r['paid_ts'])) ?></td>
              <td class="row-actions">
                <?php if (!empty($r['link'])): ?>
                  <a class="btn-portal-secondary btn-sm" href="<?= e($r['link']) ?>" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <?= $r['status_key'] === 'paid' ? 'View' : 'Pay / View' ?>
                  </a>
                <?php else: ?>
                  <span class="muted small">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
    <div class="list-pager" data-list-pager></div>
  </div>

  <p class="muted small" style="text-align:center;margin-top:4px;">
    <i class="fa-solid fa-shield-halved"></i> Billing is managed securely in HubSpot. Questions? Message your Client Success Manager.
  </p>
<?php endif; ?>

<style>
.inv-stats{margin-bottom:4px;}
.inv-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.inv-table{min-width:640px;}
.inv-num{font-weight:700;color:#fff;}
.inv-amt{font-weight:800;color:var(--gold-lt);white-space:nowrap;}
.inv-pill{display:inline-flex;align-items:center;padding:4px 11px;border-radius:30px;font-size:11px;font-weight:800;
  text-transform:uppercase;letter-spacing:.5px;border:1px solid transparent;white-space:nowrap;}
.inv-pill--paid{background:rgba(78,196,126,.16);color:#bcf0d2;border-color:rgba(78,196,126,.4);}
.inv-pill--pending{background:rgba(247,185,69,.16);color:#ffe2a8;border-color:rgba(247,185,69,.4);}
.inv-pill--late{background:rgba(225,87,87,.18);color:#f4baba;border-color:rgba(225,87,87,.45);}
.inv-empty{text-align:center;padding:42px 22px;}
.inv-empty i{font-size:30px;color:var(--gold);margin-bottom:12px;}
.inv-empty h3{margin:0 0 8px;color:#fff;}
@media (max-width:560px){ .inv-table-wrap{margin:0 -6px;} }
</style>
