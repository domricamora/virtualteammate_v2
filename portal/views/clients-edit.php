<?php /** @var ?array $client @var array $client_users */
$isNew = $client === null;
$pageTitle = $isNew ? 'New client' : 'Edit client — ' . $client['company_name'];
?>
<div class="card">
  <form method="post" action="<?= e(portal_url('clients.edit', $isNew ? [] : ['id'=>$client['id']])) ?>" class="form-grid">
    <?= csrf_field() ?>

    <label class="span-2">Company name *
      <input type="text" name="company_name" required value="<?= e($client['company_name'] ?? '') ?>">
    </label>

    <label>Company email
      <input type="email" name="company_email" value="<?= e($client['company_email'] ?? '') ?>">
    </label>

    <label>Company domain
      <input type="text" name="company_domain" value="<?= e($client['company_domain'] ?? '') ?>" placeholder="example.com">
    </label>

    <label>Billing contact email
      <input type="email" name="billing_contact_email" value="<?= e($client['billing_contact_email'] ?? '') ?>">
    </label>

    <label>Contract status
      <?php $cs = $client['contract_status'] ?? 'active'; ?>
      <select name="contract_status">
        <?php foreach (['active','paused','churned','prospect'] as $s): ?>
          <option value="<?= e($s) ?>"<?= $s === $cs ? ' selected' : '' ?>><?= e(ucfirst($s)) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label class="span-2">Workday tracker link
      <input type="url" name="workday_link" value="<?= e($client['workday_link'] ?? '') ?>" placeholder="https://…">
    </label>

    <label class="span-2">Login user (client role)
      <select name="user_id">
        <option value="0">— Unlinked —</option>
        <?php foreach ($client_users as $cu): ?>
          <option value="<?= (int) $cu['id'] ?>"<?= (int) ($client['user_id'] ?? 0) === (int) $cu['id'] ? ' selected' : '' ?>>
            <?= e(trim(($cu['first_name'] ?? '') . ' ' . ($cu['last_name'] ?? ''))) ?> &middot; <?= e($cu['email']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label class="span-2">Notes
      <textarea name="notes" rows="4"><?= e($client['notes'] ?? '') ?></textarea>
    </label>

    <div class="form-actions span-2">
      <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
      <a class="btn-portal-secondary" href="<?= e(portal_url('clients')) ?>">Cancel</a>
    </div>
  </form>
</div>

<?php if (!$isNew): ?>
  <div class="card">
    <h3>Danger zone</h3>
    <form method="post" action="<?= e(portal_url('clients.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this client?');">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $client['id'] ?>">
      <button class="btn-portal-danger" type="submit"><i class="fa-solid fa-trash"></i> Delete client</button>
    </form>
  </div>
<?php endif; ?>
