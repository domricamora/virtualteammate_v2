/* VT Portal web-push client. Powers two optional controls:
 *   - the "Push on this device" toggle on the notifications page (any user)
 *   - the "Send test notification" button on the super-admin dashboard
 * "Send test" auto-subscribes this device first if needed. No dependencies. */
(function () {
  'use strict';

  var toggle   = document.querySelector('[data-noti-push-toggle]');
  var testBtns = Array.prototype.slice.call(document.querySelectorAll('[data-push-test]'));
  if (!toggle && !testBtns.length) { return; }

  var wrap = document.querySelector('[data-push-wrap]');
  var supported = ('serviceWorker' in navigator) && ('PushManager' in window) && ('Notification' in window);
  if (!supported || !window.isSecureContext || window.Notification.permission === 'denied') {
    if (wrap) { wrap.hidden = true; }            // unusable here — keep controls hidden
    return;
  }
  if (wrap) { wrap.hidden = false; }
  testBtns.forEach(function (b) {
    b.hidden = false;
    var card = b.closest('[data-push-test-card]');
    if (card) { card.hidden = false; }
  });

  var cfg = { key: null, csrf: null };

  function b64ToUint8(b64) {
    var pad = '='.repeat((4 - (b64.length % 4)) % 4);
    var base = (b64 + pad).replace(/-/g, '+').replace(/_/g, '/');
    var raw = atob(base);
    var arr = new Uint8Array(raw.length);
    for (var i = 0; i < raw.length; i++) { arr[i] = raw.charCodeAt(i); }
    return arr;
  }

  function post(action, params) {
    var fd = new FormData();
    fd.append('_csrf', cfg.csrf);
    fd.append('_ajax', '1');
    Object.keys(params || {}).forEach(function (k) { fd.append(k, params[k]); });
    return fetch('index.php?p=' + action, { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { if (!r.ok) { throw new Error('HTTP ' + r.status); } return r.json(); });
  }

  // Load VAPID key + CSRF + current subscription state.
  fetch('index.php?p=push.key', { credentials: 'same-origin' })
    .then(function (r) { return r.json(); })
    .then(function (d) {
      cfg.key = d.key; cfg.csrf = d.csrf;
      return navigator.serviceWorker.ready;
    })
    .then(function (reg) { return reg.pushManager.getSubscription(); })
    .then(function (sub) {
      if (toggle) { toggle.checked = !!sub && window.Notification.permission === 'granted'; }
    })
    .catch(function () { /* leave defaults */ });

  function subscribe() {
    return window.Notification.requestPermission().then(function (perm) {
      if (perm !== 'granted') { return false; }
      return navigator.serviceWorker.ready
        .then(function (reg) {
          return reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: b64ToUint8(cfg.key)
          });
        })
        .then(function (sub) {
          var j = sub.toJSON();
          return post('push.subscribe', { endpoint: sub.endpoint, p256dh: j.keys.p256dh, auth: j.keys.auth });
        })
        .then(function () { return true; });
    });
  }

  function unsubscribe() {
    return navigator.serviceWorker.ready
      .then(function (reg) { return reg.pushManager.getSubscription(); })
      .then(function (sub) {
        if (!sub) { return; }
        var endpoint = sub.endpoint;
        return sub.unsubscribe().then(function () { return post('push.unsubscribe', { endpoint: endpoint }); });
      });
  }

  function ensureSubscribed() {
    return navigator.serviceWorker.ready
      .then(function (reg) { return reg.pushManager.getSubscription(); })
      .then(function (sub) {
        if (sub && window.Notification.permission === 'granted') { return true; }
        return subscribe();
      });
  }

  // Notifications-page opt-in toggle (all users).
  if (toggle) {
    toggle.addEventListener('change', function () {
      toggle.disabled = true;
      var done = function () { toggle.disabled = false; };
      if (toggle.checked) {
        subscribe().then(function (ok) { if (!ok) { toggle.checked = false; } }).then(done, done);
      } else {
        unsubscribe().then(done, done);
      }
    });
  }

  // "Send test" button(s) — super-admin dashboard. Subscribes first if needed.
  testBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (!cfg.csrf) { alert('Still loading — try again in a second.'); return; }
      btn.disabled = true;
      ensureSubscribed()
        .then(function (ok) {
          if (!ok) { alert('Push permission was not granted on this device.'); return; }
          if (toggle) { toggle.checked = true; }
          return post('push.test', {}).then(function (r) {
            if (r.sent > 0) { alert('Sent to ' + r.sent + ' device(s). Check your notifications.'); }
            else { alert('Could not deliver to ' + (r.devices || 0) + ' device(s). ' + ((r.errors || []).join('; '))); }
          });
        })
        .catch(function () { alert('Test failed. Please try again.'); })
        .then(function () { btn.disabled = false; });
    });
  });
})();
