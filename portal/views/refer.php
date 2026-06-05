<?php
/** @var array $user @var array $errors @var string $friend_name @var string $friend_email @var string $note */
$pageTitle = 'Refer A Friend';
$subtitle  = 'Know someone great? Refer them to join Virtual Teammate.';
?>
<div class="card refer-card">
  <div class="refer-intro">
    <span class="refer-ico"><i class="fa-solid fa-gift"></i></span>
    <div>
      <h3 style="margin:0 0 4px;color:#fff;">Refer a friend to the team</h3>
      <p class="muted" style="margin:0;font-size:13.5px;line-height:1.5;">Share their name and email and we'll reach out. Your referral goes straight to the Virtual Teammate team.</p>
    </div>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="refer-errors">
      <?php foreach ($errors as $err): ?><div><i class="fa-solid fa-circle-exclamation"></i> <?= e($err) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form class="refer-form" method="post" action="<?= e(portal_url('refer')) ?>">
    <?= csrf_field() ?>
    <label class="refer-f">
      <span>Friend's name <em>*</em></span>
      <input type="text" name="friend_name" value="<?= e($friend_name) ?>" placeholder="e.g. Maria Santos" required>
    </label>
    <label class="refer-f">
      <span>Friend's email <em>*</em></span>
      <input type="email" name="friend_email" value="<?= e($friend_email) ?>" placeholder="name@example.com" required>
    </label>
    <label class="refer-f">
      <span>Note <small class="muted">(optional)</small></span>
      <textarea name="note" rows="3" placeholder="Tell us a bit about them — their skills, experience, or why they'd be a great fit."><?= e($note) ?></textarea>
    </label>
    <div class="refer-foot">
      <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-paper-plane"></i> Send referral</button>
    </div>
  </form>
</div>

<style>
.refer-card{max-width:620px;}
.refer-intro{display:flex;gap:14px;align-items:center;margin-bottom:18px;padding-bottom:16px;border-bottom:1px solid var(--line);}
.refer-ico{flex:0 0 52px;width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;background:linear-gradient(135deg,var(--violet-dk),#7c3aed);box-shadow:0 8px 22px -10px rgba(124,58,237,.7);}
.refer-form{display:flex;flex-direction:column;gap:14px;}
.refer-f{display:flex;flex-direction:column;gap:6px;font-size:12.5px;font-weight:700;color:var(--gold-lt);}
.refer-f em{color:#e76d6d;font-style:normal;}
.refer-f input,.refer-f textarea{font-family:inherit;font-size:14px;color:#fff;background:rgba(255,255,255,.05);border:1px solid var(--line-2);border-radius:10px;padding:11px 13px;font-weight:500;}
.refer-f input:focus,.refer-f textarea:focus{outline:none;border-color:var(--gold);background:rgba(255,255,255,.08);}
.refer-f textarea{resize:vertical;}
.refer-foot{display:flex;justify-content:flex-end;}
.refer-errors{margin-bottom:16px;display:flex;flex-direction:column;gap:6px;padding:12px 14px;border:1px solid rgba(231,109,109,.4);background:rgba(231,109,109,.12);border-radius:10px;color:#ffd7d7;font-size:13px;}
.refer-errors i{color:#e76d6d;margin-right:4px;}
</style>
