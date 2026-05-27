<?php /** @var array $clients @var string $q */
$pageTitle = 'Clients';
$subtitle  = 'Companies on contract with VT.';
?>

<div class="card">
  <div class="card-h">
    <form method="get" class="inline-filter">
      <input type="hidden" name="p" value="clients">
      <input type="search" name="q" placeholder="Search company or email…" value="<?= e($q) ?>">
      <button class="btn-portal-secondary btn-sm" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
    </form>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('clients.edit')) ?>"><i class="fa-solid fa-plus"></i> New client</a>
  </div>

  <table class="data-table">
    <thead><tr><th>Company</th><th>Email</th><th>Login user</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php if (empty($clients)): ?>
        <tr><td colspan="5" class="muted">No clients yet.</td></tr>
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
</div>
