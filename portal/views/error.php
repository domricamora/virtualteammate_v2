<?php /** @var string $title @var string $message */
$pageTitle = $title ?? 'Error';
?>
<div class="card">
  <h2><?= e($pageTitle) ?></h2>
  <p><?= e($message ?? 'Something went wrong.') ?></p>
  <p><a class="btn-portal-secondary" href="<?= e(portal_url('dashboard')) ?>">&larr; Back to dashboard</a></p>
</div>
