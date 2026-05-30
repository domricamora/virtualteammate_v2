<?php
/** @var array $user @var array $notifications */
$pageTitle = 'Notifications';
$subtitle  = 'Updates from your portal activity, tasks, meetings and team.';
$unread    = array_filter($notifications, static fn($n) => empty($n['read_at']));
$totalAll  = count($notifications);
$emailOn   = !empty($user['notify_by_email']);

$kindIcon = static function (string $k): string {
    return match ($k) {
        'task'    => 'list-check',
        'meeting' => 'calendar-day',
        'message' => 'envelope',
        'sync'    => 'cloud-arrow-down',
        default   => 'info-circle',
    };
};
?>
<div class="card" data-list id="notiPage">
  <div class="card-h notif-toolbar">
    <div class="notif-meta">
      <h3 style="margin:0;"><i class="fa-solid fa-bell"></i> Inbox
        <span class="muted small" data-noti-total>(<?= (int) $totalAll ?>)</span>
      </h3>
      <span class="notif-unread-pill" data-noti-unread-pill <?= empty($unread) ? 'hidden' : '' ?>>
        <span data-noti-unread><?= count($unread) ?></span> unread
      </span>
    </div>
    <div class="notif-actions">
      <label class="notif-toggle" title="Send an email copy of every notification">
        <input type="checkbox" data-noti-email-toggle <?= $emailOn ? 'checked' : '' ?>>
        <span class="notif-toggle-track"></span>
        <span class="notif-toggle-label"><i class="fa-solid fa-envelope"></i> Email notifications</span>
      </label>
      <button class="btn-portal-secondary btn-sm" type="button" data-noti-mark-all-read <?= empty($unread) ? 'disabled' : '' ?>>
        <i class="fa-solid fa-check-double"></i> Mark all read
      </button>
      <button class="btn-portal-danger btn-sm" type="button" data-noti-delete-all <?= $totalAll === 0 ? 'disabled' : '' ?>>
        <i class="fa-solid fa-trash"></i> Delete all
      </button>
    </div>
  </div>

  <ul class="cd-noti-list" data-noti-list>
    <?php if (empty($notifications)): ?>
      <li data-empty class="muted" style="text-align:center;padding:30px;">No notifications yet. We'll let you know when something happens.</li>
    <?php else: foreach ($notifications as $n): ?>
      <li class="cd-noti <?= empty($n['read_at']) ? 'unread' : '' ?>" data-noti-id="<?= (int) $n['id'] ?>">
        <div class="cd-noti-ico cd-noti-<?= e($n['kind']) ?>"><i class="fa-solid fa-<?= e($kindIcon((string) $n['kind'])) ?>"></i></div>
        <div class="cd-noti-body">
          <div class="cd-noti-title"><?= e($n['title']) ?></div>
          <?php if (!empty($n['body'])): ?><div class="muted small"><?= e($n['body']) ?></div><?php endif; ?>
          <div class="muted small"><?= e(fmt_dt($n['created_at'])) ?></div>
        </div>
        <div class="cd-noti-actions">
          <?php if (!empty($n['link'])): ?>
            <a class="btn-portal-secondary btn-sm" href="<?= e($n['link']) ?>" data-noti-open><i class="fa-solid fa-arrow-right"></i> Open</a>
          <?php endif; ?>
          <button class="btn-portal-secondary btn-sm" type="button" data-noti-read title="Mark as read" <?= !empty($n['read_at']) ? 'hidden' : '' ?>>
            <i class="fa-solid fa-check"></i>
          </button>
          <button class="btn-portal-danger btn-sm" type="button" data-noti-delete title="Delete">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </li>
    <?php endforeach; endif; ?>
  </ul>
</div>

<style>
.notif-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;}
.notif-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
.notif-unread-pill{
  background:#e53e3e;color:#fff;font-size:11px;font-weight:800;
  padding:3px 10px;border-radius:30px;line-height:1.4;
}
.notif-unread-pill[hidden]{display:none;}
.notif-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}

/* Pill toggle for the email-notifications switch */
.notif-toggle{display:inline-flex;align-items:center;gap:10px;cursor:pointer;user-select:none;padding:4px 4px 4px 0;}
.notif-toggle input{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;}
.notif-toggle-track{
  position:relative;width:38px;height:22px;border-radius:30px;flex:0 0 38px;
  background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.15);
  transition:background .2s,border-color .2s;
}
.notif-toggle-track::after{
  content:'';position:absolute;top:2px;left:2px;width:16px;height:16px;border-radius:50%;
  background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.4);
  transition:transform .2s;
}
.notif-toggle input:checked + .notif-toggle-track{background:var(--gold,#d4a64a);border-color:var(--gold,#d4a64a);}
.notif-toggle input:checked + .notif-toggle-track::after{transform:translateX(16px);}
.notif-toggle input:focus-visible + .notif-toggle-track{box-shadow:0 0 0 3px rgba(247,185,69,.25);}
.notif-toggle-label{font-size:12.5px;color:rgba(255,255,255,.85);font-weight:600;}
.notif-toggle-label i{color:var(--gold,#d4a64a);margin-right:4px;}

.cd-noti.is-removing{opacity:0;transform:translateX(20px);transition:opacity .2s,transform .2s;}
</style>

<script>
(function(){
  var card = document.getElementById('notiPage');
  if (!card) return;
  var csrf = <?= json_encode(csrf_token()) ?>;

  var list           = card.querySelector('[data-noti-list]');
  var unreadPill     = card.querySelector('[data-noti-unread-pill]');
  var unreadCountEl  = card.querySelector('[data-noti-unread]');
  var totalEl        = card.querySelector('[data-noti-total]');
  var markAllBtn     = card.querySelector('[data-noti-mark-all-read]');
  var deleteAllBtn   = card.querySelector('[data-noti-delete-all]');
  var emailToggle    = card.querySelector('[data-noti-email-toggle]');
  var topBellBadge   = document.querySelector('.portal-top-bell-badge');
  var topBellLink    = document.querySelector('.portal-top-bell');
  var navLink        = document.querySelector('.portal-nav-link[href*="p=notifications"]');
  var navBadge       = navLink ? navLink.querySelector('.portal-nav-badge') : null;

  function post(action, params){
    var fd = new FormData();
    fd.append('_csrf', csrf);
    fd.append('_ajax', '1');
    Object.keys(params || {}).forEach(function(k){ fd.append(k, params[k]); });
    return fetch('index.php?p=' + action, { method:'POST', body: fd, credentials:'same-origin' })
      .then(function(r){ if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); });
  }

  function updateUnread(n){
    if (unreadCountEl) unreadCountEl.textContent = n;
    if (unreadPill)   unreadPill.hidden = n === 0;
    if (markAllBtn)   markAllBtn.disabled = n === 0;
    // Mirror to the top-bar bell. Create the badge node if it wasn't there.
    if (topBellLink){
      if (n > 0){
        if (!topBellBadge){
          topBellBadge = document.createElement('span');
          topBellBadge.className = 'portal-top-bell-badge';
          topBellLink.appendChild(topBellBadge);
        }
        topBellBadge.textContent = n > 99 ? '99+' : n;
      } else if (topBellBadge){
        topBellBadge.remove();
        topBellBadge = null;
      }
    }
    // Mirror to the left-nav badge too. Create/remove it the same way so
    // clearing or deleting all notifications zeroes it out without a reload.
    if (navLink){
      if (n > 0){
        if (!navBadge){
          navBadge = document.createElement('span');
          navBadge.className = 'portal-nav-badge';
          navLink.appendChild(navBadge);
        }
        navBadge.textContent = n > 99 ? '99+' : n;
      } else if (navBadge){
        navBadge.remove();
        navBadge = null;
      }
    }
  }

  function updateTotal(){
    var rows = list.querySelectorAll('li[data-noti-id]');
    if (totalEl)       totalEl.textContent = '(' + rows.length + ')';
    if (deleteAllBtn)  deleteAllBtn.disabled = rows.length === 0;
    var existingEmpty = list.querySelector('li[data-empty]');
    if (rows.length === 0 && !existingEmpty){
      var li = document.createElement('li');
      li.setAttribute('data-empty', '');
      li.className = 'muted';
      li.style.textAlign = 'center';
      li.style.padding   = '30px';
      li.textContent = "No notifications yet. We'll let you know when something happens.";
      list.appendChild(li);
    } else if (rows.length > 0 && existingEmpty) {
      existingEmpty.remove();
    }
  }

  // ── Per-row interactions ─────────────────────────────────────────────
  list.addEventListener('click', function(e){
    var readBtn = e.target.closest('[data-noti-read]');
    var delBtn  = e.target.closest('[data-noti-delete]');
    var openLnk = e.target.closest('[data-noti-open]');
    if (!readBtn && !delBtn && !openLnk) return;
    var row = e.target.closest('li[data-noti-id]');
    if (!row) return;
    var id = row.getAttribute('data-noti-id');

    if (readBtn){
      e.preventDefault();
      post('notifications.read', { id: id }).then(function(r){
        row.classList.remove('unread');
        readBtn.hidden = true;
        updateUnread(r.unread || 0);
      }).catch(function(){});
    }
    if (delBtn){
      e.preventDefault();
      row.classList.add('is-removing');
      post('notifications.delete', { id: id }).then(function(r){
        setTimeout(function(){
          row.remove();
          updateTotal();
          updateUnread(r.unread || 0);
        }, 200);
      }).catch(function(){ row.classList.remove('is-removing'); });
    }
    if (openLnk){
      // Auto-mark-read on open — fire-and-forget.
      if (row.classList.contains('unread')){
        post('notifications.read', { id: id })
          .then(function(r){ updateUnread(r.unread || 0); })
          .catch(function(){});
        row.classList.remove('unread');
        var rb = row.querySelector('[data-noti-read]'); if (rb) rb.hidden = true;
      }
    }
  });

  // ── Mark-all-read ────────────────────────────────────────────────────
  if (markAllBtn){
    markAllBtn.addEventListener('click', function(){
      post('notifications.read', { id: 0 }).then(function(r){
        list.querySelectorAll('li.unread').forEach(function(li){
          li.classList.remove('unread');
          var rb = li.querySelector('[data-noti-read]'); if (rb) rb.hidden = true;
        });
        updateUnread(r.unread || 0);
      }).catch(function(){});
    });
  }

  // ── Delete-all ───────────────────────────────────────────────────────
  if (deleteAllBtn){
    deleteAllBtn.addEventListener('click', function(){
      if (!confirm('Delete all notifications? This cannot be undone.')) return;
      post('notifications.delete_all', {}).then(function(r){
        list.querySelectorAll('li[data-noti-id]').forEach(function(li){ li.remove(); });
        updateUnread(r.unread || 0);
        updateTotal();
      }).catch(function(){});
    });
  }

  // ── Email-notification toggle ────────────────────────────────────────
  if (emailToggle){
    emailToggle.addEventListener('change', function(){
      var on = emailToggle.checked ? '1' : '';
      // Optimistic UI; revert on error.
      post('notifications.toggle_email', on ? { on: on } : {})
        .then(function(){})
        .catch(function(){
          emailToggle.checked = !emailToggle.checked;
          alert('Could not update email setting. Please try again.');
        });
    });
  }
})();
</script>
