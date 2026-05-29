<?php /** @var array $clients @var string $q */
$pageTitle = 'Clients';
$subtitle  = 'Companies on contract with VT.';
$totalAll = count($clients);
?>

<div class="card" data-list>
  <div class="card-h">
    <div class="list-toolbar">
      <input type="search" data-list-search placeholder="Search company or email…" value="<?= e($q) ?>" autocomplete="off">
      <select data-list-pagesize>
        <option value="25" selected>25 / page</option>
        <option value="50">50 / page</option>
        <option value="100">100 / page</option>
        <option value="0">All</option>
      </select>
      <span class="list-counter">Total <strong><?= (int) $totalAll ?></strong> clients &middot; <span data-list-counter>—</span></span>
    </div>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('clients.edit')) ?>"><i class="fa-solid fa-plus"></i> New client</a>
  </div>

  <table class="data-table" data-paginate>
    <thead><tr><th>Company</th><th>Email</th><th>Login user</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php if (empty($clients)): ?>
        <tr data-empty><td colspan="5" class="muted">No clients yet.</td></tr>
      <?php else: foreach ($clients as $c): ?>
        <tr>
          <td><?= e($c['company_name']) ?></td>
          <td><?= e($c['company_email']) ?></td>
          <td class="muted"><?= e($c['user_email'] ?? '—') ?></td>
          <td><span class="pill pill-<?= e($c['contract_status']) ?>"><?= e($c['contract_status']) ?></span></td>
          <td class="row-actions">
            <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('clients.view', ['id'=>$c['id']])) ?>" title="View"><i class="fa-solid fa-eye"></i></a>
            <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('clients.edit', ['id'=>$c['id']])) ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
            <form method="post" action="<?= e(portal_url('clients.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this client?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
              <button class="btn-portal-danger btn-sm" type="submit"><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  <div class="list-pager" data-list-pager></div>
</div>
