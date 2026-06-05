<?php
/**
 * @var string $error
 * @var array  $payslips      Matched rows (assoc by header), newest first
 * @var array  $header        Raw header row
 * @var array  $visibleCols   Non-empty header columns
 * @var array  $dateHeaders, $periodHeaders, $amountFields
 * @var string $accountName
 * @var array  $vtNames
 */
$pageTitle = 'Payslips';
$subtitle  = 'Your pay history, synced from payroll.';

$recordCount = count($payslips);
$primaryCol  = $visibleCols[0] ?? '';
$latestRow   = $payslips[0] ?? [];
$latestPeriod   = payslip_row_value($latestRow, $periodHeaders);
$latestPayDate  = payslip_format_date(payslip_row_value($latestRow, $dateHeaders), 'Not available');
$latestAmount   = payslip_parse_amount(payslip_row_value($latestRow, $amountFields));
if ($latestPeriod === '') { $latestPeriod = $latestPayDate; }

$trackedTotal = 0.0; $trackedExists = false;
foreach ($payslips as $p) {
    $a = payslip_parse_amount(payslip_row_value($p, $amountFields));
    if ($a !== null) { $trackedTotal += $a; $trackedExists = true; }
}
?>

<div class="pay">
  <section class="pay-hero">
    <div class="pay-hero-l">
      <div class="pay-eyebrow">Payslips</div>
      <h1 class="pay-title">Your pay history</h1>
      <p class="pay-copy">Review every uploaded payslip in a clean ledger view with quick payout highlights.</p>
    </div>
    <div class="pay-hero-badge">
      <span class="pay-hero-badge-l">Account</span>
      <strong><?= e($accountName) ?></strong>
      <small><?= (int) $recordCount ?> payslip<?= $recordCount === 1 ? '' : 's' ?> synced</small>
    </div>
  </section>

  <?php if ($error !== ''): ?>
    <div class="card pay-msg"><i class="fa-solid fa-triangle-exclamation"></i> <?= e($error) ?></div>
  <?php else: ?>

    <div class="pay-stats">
      <div class="pay-stat">
        <span class="pay-stat-l">Records</span>
        <strong><?= (int) $recordCount ?></strong>
        <small>Available for your dashboard</small>
      </div>
      <div class="pay-stat">
        <span class="pay-stat-l">Latest period</span>
        <strong><?= e($latestPeriod !== '' ? $latestPeriod : '--') ?></strong>
        <small>Most recent payroll entry</small>
      </div>
      <div class="pay-stat">
        <span class="pay-stat-l"><?= $trackedExists ? 'Tracked payout total' : 'Latest payout' ?></span>
        <strong><?= e($trackedExists ? payslip_format_amount($trackedTotal) : payslip_format_amount($latestAmount)) ?></strong>
        <small><?= e($trackedExists ? 'Combined from detected pay amounts' : $latestPayDate) ?></small>
      </div>
    </div>

    <section class="card pay-panel">
      <div class="card-h">
        <div>
          <div class="pay-panel-eyebrow">Payout ledger</div>
          <h3 style="margin:0;">Recent payslips</h3>
        </div>
        <span class="pay-panel-tag">Updated from payroll sheet</span>
      </div>

      <?php if (empty($payslips)): ?>
        <div class="pay-empty">
          <p>No payslips found for your account.</p>
          <p class="muted small">Matched profile names: <?= e(implode(', ', $vtNames)) ?></p>
          <p class="muted small">If you believe this is an error, please contact HR.</p>
        </div>
      <?php else: ?>
        <div class="pay-table-wrap">
          <table class="pay-table">
            <thead>
              <tr>
                <?php foreach ($visibleCols as $col): ?><th><?= e($col) ?></th><?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($payslips as $p):
                $rowPeriod  = payslip_row_value($p, $periodHeaders);
                $rowPayDate = payslip_format_date(payslip_row_value($p, $dateHeaders), '--'); ?>
                <tr>
                  <?php foreach ($visibleCols as $col):
                    $raw = (string) ($p[$col] ?? '');
                    $val = $raw; $cls = '';
                    if (in_array($col, $amountFields, true)) {
                        $amt = payslip_parse_amount($raw);
                        if ($amt !== null) { $val = payslip_format_amount($amt); $cls = 'pay-amount'; }
                    } elseif (in_array($col, $dateHeaders, true)) {
                        $val = payslip_format_date($raw, '--');
                    }
                    ?>
                    <td data-label="<?= e($col) ?>" class="<?= e($cls) ?>">
                      <?php if ($col === $primaryCol): ?>
                        <div class="pay-primary"><?= e($val !== '' ? $val : '--') ?></div>
                        <?php if ($rowPeriod !== '' && $rowPeriod !== $val): ?>
                          <div class="pay-sub"><?= e($rowPeriod) ?></div>
                        <?php elseif ($rowPayDate !== '--' && $rowPayDate !== $val): ?>
                          <div class="pay-sub"><?= e($rowPayDate) ?></div>
                        <?php endif; ?>
                      <?php else: ?>
                        <?= e($val !== '' ? $val : '--') ?>
                      <?php endif; ?>
                    </td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

  <?php endif; ?>
</div>
