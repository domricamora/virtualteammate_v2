<?php
/** @var array $user @var array $contacts @var ?array $partner @var array $messages @var array $unread */
$pageTitle = 'Messages';
$subtitle  = 'Chat with your assigned team — Virtual Teammates and your Client Success Manager.';

$nameOf = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    return $n !== '' ? $n : (string) ($u['email'] ?? '—');
};
$initial = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    if ($n !== '') { return strtoupper(mb_substr($n, 0, 1)); }
    return strtoupper(mb_substr((string) ($u['email'] ?? '?'), 0, 1));
};
$roleLabel = static function (string $role): string {
    return match ($role) {
        'vt_hired'   => 'Virtual Teammate',
        'vt_onpool'  => 'On talent pool',
        'csm'        => 'Client Success Mgr',
        'client'     => 'Client',
        'super_admin'=> 'Super admin',
        default      => $role,
    };
};
?>
<div class="msg-shell">
  <!-- Sidebar: contacts list + search -->
  <aside class="msg-side">
    <div class="msg-side-h">
      <strong>Conversations</strong>
      <span class="muted small">(<span data-msg-count><?= count($contacts) ?></span>)</span>
    </div>
    <div class="msg-side-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="search" data-msg-search placeholder="Search name, email, role…" autocomplete="off">
    </div>
    <div class="msg-side-list">
    <?php if (empty($contacts)): ?>
      <p class="muted" style="padding:18px;">No people you can chat with yet.</p>
    <?php else: foreach ($contacts as $c):
      $isActive = $partner && (int) $partner['id'] === (int) $c['id'];
      $cu = $unread[(int) $c['id']] ?? 0;
      $searchBlob = strtolower(trim(
        $nameOf($c) . ' ' . (string) ($c['email'] ?? '') . ' ' . $roleLabel($c['role'] ?? '')
      ));
    ?>
      <a class="msg-contact <?= $isActive ? 'is-active' : '' ?>"
         href="<?= e(portal_url('messages', ['with' => (int) $c['id']])) ?>"
         data-msg-blob="<?= e($searchBlob) ?>"
         data-msg-unread="<?= (int) $cu ?>">
        <?php if (!empty($c['photo_url'])): ?>
          <img class="msg-contact-photo" src="<?= e($c['photo_url']) ?>" alt="" loading="lazy"
               onerror="this.onerror=null;this.src='assets/placeholder-avatar.svg';">
        <?php else: ?>
          <div class="msg-contact-photo placeholder"><?= e($initial($c)) ?></div>
        <?php endif; ?>
        <div class="msg-contact-meta">
          <div class="msg-contact-name"><?= e($nameOf($c)) ?></div>
          <div class="muted small"><?= e($roleLabel($c['role'])) ?></div>
        </div>
        <?php if ($cu > 0): ?><span class="msg-unread"><?= (int) $cu ?></span><?php endif; ?>
      </a>
    <?php endforeach; endif; ?>
    <p class="muted small" data-msg-empty style="display:none;padding:14px;text-align:center;">No matches.</p>
    </div>
  </aside>

  <!-- Thread / empty state -->
  <section class="msg-main">
    <?php if (!$partner): ?>
      <div class="msg-empty">
        <i class="fa-solid fa-comments"></i>
        <h3>Pick a conversation</h3>
        <p class="muted">Select someone from the left to start chatting.</p>
      </div>
    <?php else: ?>
      <div class="msg-head">
        <?php if (!empty($partner['photo_url'])): ?>
          <img class="msg-head-photo" src="<?= e($partner['photo_url']) ?>" alt="" loading="lazy"
               onerror="this.onerror=null;this.src='assets/placeholder-avatar.svg';">
        <?php else: ?>
          <div class="msg-head-photo placeholder"><?= e($initial($partner)) ?></div>
        <?php endif; ?>
        <div>
          <div class="msg-head-name"><?= e($nameOf($partner)) ?></div>
          <div class="muted small"><?= e($roleLabel($partner['role'])) ?></div>
        </div>
      </div>

      <div class="msg-thread" id="msgThread">
        <?php if (empty($messages)): ?>
          <p class="muted" style="text-align:center;padding:40px 20px;">No messages yet. Send the first one below.</p>
        <?php else: $lastDay = ''; foreach ($messages as $m):
          $isMe   = (int) $m['sender_user_id'] === (int) $user['id'];
          $thisDay = substr($m['created_at'], 0, 10);
          if ($thisDay !== $lastDay):
        ?>
            <div class="msg-day"><span><?= e($thisDay) ?></span></div>
        <?php $lastDay = $thisDay; endif; ?>
          <div class="msg-row <?= $isMe ? 'me' : 'them' ?>">
            <div class="msg-bubble">
              <?= nl2br(e($m['body'])) ?>
              <div class="msg-time muted"><?= e(substr($m['created_at'], 11, 5)) ?></div>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>

      <form class="msg-compose" method="post" action="<?= e(portal_url('messages.send')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="with" value="<?= (int) $partner['id'] ?>">
        <textarea name="body" rows="2" placeholder="Type a message…" required maxlength="4000"
                  onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit();}"></textarea>
        <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-paper-plane"></i> Send</button>
      </form>
      <p class="muted small" style="text-align:right;margin:6px 6px 0;">Press Enter to send &middot; Shift+Enter for new line</p>
    <?php endif; ?>
  </section>
</div>

<style>
.msg-shell{display:grid;grid-template-columns:280px 1fr;gap:14px;min-height:560px;}
@media (max-width:880px){.msg-shell{grid-template-columns:1fr;}}
.msg-side{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;max-height:640px;}
.msg-side-h{padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);font-size:13px;color:rgba(255,255,255,.85);}
.msg-side-search{display:flex;align-items:center;gap:8px;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.04);background:rgba(255,255,255,.02);}
.msg-side-search i{color:rgba(255,255,255,.45);font-size:12px;}
.msg-side-search input{flex:1;background:transparent;border:0;color:#fff;font-family:inherit;font-size:13px;outline:none;padding:2px 0;}
.msg-side-search input::placeholder{color:rgba(255,255,255,.35);}
.msg-side-list{flex:1;overflow-y:auto;}
.msg-contact{display:flex;gap:12px;align-items:center;padding:12px 14px;border-bottom:1px solid rgba(255,255,255,.04);text-decoration:none;color:inherit;transition:background .15s;}
.msg-contact:hover{background:rgba(247,185,69,.06);}
.msg-contact.is-active{background:rgba(247,185,69,.12);border-left:3px solid var(--gold,#d4a64a);padding-left:11px;}
.msg-contact-photo{width:38px;height:38px;border-radius:50%;object-fit:cover;flex:0 0 38px;background:#1a1535;}
.msg-contact-photo.placeholder{display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:rgba(255,255,255,.85);background:linear-gradient(135deg,#4a4178,#322a5a);}
.msg-contact-meta{flex:1;min-width:0;}
.msg-contact-name{font-size:13.5px;font-weight:600;color:#fff;}
.msg-unread{background:#e53e3e;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:30px;}
.msg-main{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;}
.msg-empty{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;color:rgba(255,255,255,.55);text-align:center;}
.msg-empty i{font-size:46px;color:rgba(247,185,69,.4);margin-bottom:14px;}
.msg-empty h3{margin:0 0 6px;color:#fff;font-size:18px;}
.msg-head{display:flex;gap:14px;align-items:center;padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.06);}
.msg-head-photo{width:42px;height:42px;border-radius:50%;object-fit:cover;flex:0 0 42px;background:#1a1535;}
.msg-head-photo.placeholder{display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:rgba(255,255,255,.85);background:linear-gradient(135deg,#4a4178,#322a5a);}
.msg-head-name{font-size:15px;font-weight:700;color:#fff;}
.msg-thread{flex:1;overflow-y:auto;padding:18px;display:flex;flex-direction:column;gap:8px;max-height:520px;}
.msg-day{display:flex;align-items:center;justify-content:center;margin:8px 0;color:rgba(255,255,255,.4);font-size:11px;}
.msg-day span{padding:2px 12px;background:rgba(255,255,255,.04);border-radius:30px;}
.msg-row{display:flex;}
.msg-row.me{justify-content:flex-end;}
.msg-bubble{max-width:70%;padding:10px 14px;border-radius:14px;background:rgba(255,255,255,.06);color:#fff;font-size:14px;line-height:1.45;}
.msg-row.me .msg-bubble{background:linear-gradient(135deg,rgba(247,185,69,.25),rgba(247,185,69,.15));border-bottom-right-radius:2px;}
.msg-row.them .msg-bubble{border-bottom-left-radius:2px;}
.msg-time{font-size:10.5px;margin-top:4px;text-align:right;color:rgba(255,255,255,.5);}
.msg-compose{display:flex;gap:8px;padding:12px;border-top:1px solid rgba(255,255,255,.06);background:rgba(255,255,255,.02);}
.msg-compose textarea{flex:1;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:10px;padding:10px 12px;font-family:inherit;font-size:14px;resize:vertical;}
.msg-compose textarea:focus{outline:none;border-color:var(--gold,#d4a64a);}
</style>
<script>
(function(){
  var t = document.getElementById('msgThread'); if (t) t.scrollTop = t.scrollHeight;
})();
</script>

<script>
(function(){
  var search    = document.querySelector('[data-msg-search]');
  var list      = document.querySelector('.msg-side-list');
  var emptyEl   = document.querySelector('[data-msg-empty]');
  var countEl   = document.querySelector('[data-msg-count]');
  if (!search || !list) return;
  var contacts  = Array.prototype.slice.call(list.querySelectorAll('.msg-contact'));
  var total     = contacts.length;
  var t = null;
  search.addEventListener('input', function(){
    clearTimeout(t);
    t = setTimeout(function(){
      var q = search.value.trim().toLowerCase();
      var shown = 0;
      contacts.forEach(function(c){
        var blob = c.getAttribute('data-msg-blob') || '';
        var match = q === '' || blob.indexOf(q) !== -1;
        c.style.display = match ? '' : 'none';
        if (match) shown++;
      });
      if (emptyEl) emptyEl.style.display = shown === 0 ? '' : 'none';
      if (countEl) countEl.textContent = q === '' ? total : shown;
    }, 80);
  });
})();
</script>
