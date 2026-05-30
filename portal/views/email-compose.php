<?php
/** @var array $user @var ?array $result @var array $draft */
$to      = (string) ($draft['to']      ?? $user['email'] ?? '');
$subject = (string) ($draft['subject'] ?? '');
$message = (string) ($draft['message'] ?? '');
?>
<?php if ($result): ?>
  <div class="portal-flash <?= !empty($result['ok']) ? 'flash-success' : 'flash-error' ?>"><?= e($result['msg']) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-h">
    <h3 style="margin:0;"><i class="fa-solid fa-paper-plane"></i> Compose email</h3>
    <span class="muted small">From <strong>support@virtualteammate.com</strong></span>
  </div>

  <form method="post" action="<?= e(portal_url('email.send')) ?>" class="form-grid" style="margin-top:6px;">
    <?= csrf_field() ?>

    <label class="span-2">Recipient
      <input type="email" name="to" value="<?= e($to) ?>" placeholder="name@example.com" required autocomplete="off">
    </label>

    <label class="span-2">Subject
      <input type="text" name="subject" value="<?= e($subject) ?>" placeholder="Subject line" required>
    </label>

    <label class="span-2">Message
      <textarea name="message" rows="10" placeholder="Write your message…" required><?= e($message) ?></textarea>
    </label>

    <div class="span-2 form-actions">
      <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-paper-plane"></i> Send email</button>
      <button type="button" class="btn-portal-secondary" data-fill-test>
        <i class="fa-solid fa-flask"></i> Fill test message
      </button>
    </div>
  </form>

  <p class="muted small" style="margin:16px 0 0;">
    <i class="fa-solid fa-circle-info"></i>
    Plain text is sent inside the branded Virtual Teammate template. Line breaks are preserved.
    Email won't deliver from localhost (no mail server) — it works once running on the production host.
  </p>
</div>

<div class="card" style="margin-top:18px;">
  <div class="card-h">
    <h3 style="margin:0;"><i class="fa-solid fa-bullseye"></i> Lead notifications</h3>
    <span class="muted small">Where website lead-form submissions are sent</span>
  </div>
  <form method="post" action="<?= e(portal_url('email.settings')) ?>" class="form-grid" style="margin-top:6px;">
    <?= csrf_field() ?>
    <label class="span-2">Send lead-generation emails to
      <input type="email" name="lead_notify_email" value="<?= e($lead_email ?? '') ?>" placeholder="name@virtualteammate.com" required>
    </label>
    <div class="span-2 form-actions">
      <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-floppy-disk"></i> Save recipient</button>
    </div>
  </form>
  <p class="muted small" style="margin:14px 0 0;">
    <i class="fa-solid fa-circle-info"></i>
    Every website form (homepage CTA, ROI reach-out, Virtual Teammates funnel) emails this address and is logged on the
    <a href="<?= e(portal_url('leads')) ?>" style="color:#3919BA;font-weight:700;text-decoration:none;">Leads</a> page.
  </p>
</div>

<script>
(function(){
  var btn = document.querySelector('[data-fill-test]');
  if (!btn) return;
  btn.addEventListener('click', function(){
    var form = btn.closest('form');
    if (!form) return;
    var subj = form.querySelector('[name="subject"]');
    var msg  = form.querySelector('[name="message"]');
    if (subj && !subj.value) subj.value = 'Virtual Teammate — portal email test';
    if (msg && !msg.value) {
      msg.value = 'This is a test email from the Virtual Teammate portal.\n\n'
        + 'If you received it, mail delivery is working on this server.';
    }
    if (msg) msg.focus();
  });
})();
</script>
