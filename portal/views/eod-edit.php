<?php /** @var ?array $report @var array $user @var array $vt_users @var bool $is_vt */
$isNew = $report === null;
$pageTitle = $isNew ? 'New EOD report' : 'EOD report — ' . ($report['report_date'] ?? '');
?>
<div class="card">
  <form method="post" action="<?= e(portal_url('eod.edit', $isNew ? [] : ['id'=>$report['id']])) ?>" class="form-grid">
    <?= csrf_field() ?>

    <?php if (!$is_vt): ?>
      <label>VT *
        <select name="vt_user_id" required>
          <?php if ($isNew): ?><option value="">— choose —</option><?php endif; ?>
          <?php foreach ($vt_users as $u): ?>
            <option value="<?= (int) $u['id'] ?>"<?= (int) ($report['vt_user_id'] ?? 0) === (int) $u['id'] ? ' selected' : '' ?>>
              <?= e(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?> &middot; <?= e($u['email']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    <?php endif; ?>

    <label>Report date *
      <input type="date" name="report_date" required value="<?= e($report['report_date'] ?? date('Y-m-d')) ?>">
    </label>

    <label class="span-2">Best work today
      <textarea name="best_work" rows="3"><?= e($report['best_work'] ?? '') ?></textarea>
    </label>

    <label class="span-2">Help / blockers
      <textarea name="help_needed" rows="3"><?= e($report['help_needed'] ?? '') ?></textarea>
    </label>

    <label class="span-2">Focus for next day
      <textarea name="focus_next" rows="3"><?= e($report['focus_next'] ?? '') ?></textarea>
    </label>

    <label class="span-2">Pending / waiting on
      <textarea name="pending_waiting_on" rows="2"><?= e($report['pending_waiting_on'] ?? '') ?></textarea>
    </label>

    <label>KPI name
      <input type="text" name="kpi_name" value="<?= e($report['kpi_name'] ?? '') ?>">
    </label>

    <label>KPI target
      <input type="text" name="kpi_target" value="<?= e($report['kpi_target'] ?? '') ?>">
    </label>

    <label>KPI achieved
      <input type="text" name="kpi_achieved" value="<?= e($report['kpi_achieved'] ?? '') ?>">
    </label>

    <div class="form-actions span-2">
      <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
      <a class="btn-portal-secondary" href="<?= e(portal_url('eod')) ?>">Cancel</a>
    </div>
  </form>
</div>
