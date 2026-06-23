/* VT Portal web-push opt-in. Drives the "Push on this device" toggle on the
 * notifications page: requests permission, subscribes via PushManager, and
 * registers/removes the subscription server-side. No dependencies. */
(function () {
  'use strict';

  var toggle = document.querySelector('[data-noti-push-toggle]');
  if (!toggle) { return; }
  var wrap = document.querySelector('[data-push-wrap]');

  var supported = ('serviceWorker' in navigator) && ('PushManager' in window) && ('Notification' in window);
  if (!supported || window.Notification.permission === 'denied') {
    return; // leave the control hidden — not usable on this browser
  }
  if (wrap) { wrap.hidden = false; }

  var testBtn = document.querySelector('[data-noti-push-test]');
  if (testBtn) { testBtn.hidden = false; }

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

  // Load VAPID key + CSRF + current state, then reflect it in the toggle.
  fetch('index.php?p=push.key', { credentials: 'same-origin' })
    .then(function (r) { return r.json(); })
    .then(function (d) {
      cfg.key = d.key; cfg.csrf = d.csrf;
      return navigator.serviceWorker.ready;
    })
    .then(function (reg) { return reg.pushManager.getSubscription(); })
    .then(function (sub) {
      toggle.checked = !!sub && window.Notification.permission === 'granted';
    })
    .catch(function () { /* leave unchecked */ });

  toggle.addEventListener('change', function () {
    toggle.disabled = true;
    var done = function () { toggle.disabled = false; };
    if (toggle.checked) { enable().then(done, done); }
    else { disable().then(done, done); }
  });

  // "Send test" — fire a push to this user's subscribed devices.
  if (testBtn) {
    testBtn.addEventListener('click', function () {
      if (!cfg.csrf) { alert('Still loading — try again in a second.'); return; }
      testBtn.disabled = true;
      post('push.test', {})
        .then(function (r) {
          if (r.sent > 0) { alert('Sent to ' + r.sent + ' device(s). Check your notifications.'); }
          else if (r.devices === 0) { alert('No devices subscribed yet — turn on “Push on this device” first.'); }
          else { alert('Could not deliver to ' + r.devices + ' device(s). ' + ((r.errors || []).join('; '))); }
        })
        .catch(function () { alert('Test failed. Please try again.'); })
        .then(function () { testBtn.disabled = false; });
    });
  }

  function enable() {
    return window.Notification.requestPermission().then(function (perm) {
      if (perm !== 'granted') { toggle.checked = false; return; }
      return navigator.serviceWorker.ready
        .then(function (reg) {
          return reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: b64ToUint8(cfg.key)
          });
        })
        .then(function (sub) {
          var j = sub.toJSON();
          return post('push.subscribe', {
            endpoint: sub.endpoint,
            p256dh: j.keys.p256dh,
            auth: j.keys.auth
          });
        })
        .catch(function () {
          toggle.checked = false;
          alert('Could not enable push notifications on this device.');
        });
    });
  }

  function disable() {
    return navigator.serviceWorker.ready
      .then(function (reg) { return reg.pushManager.getSubscription(); })
      .then(function (sub) {
        if (!sub) { return; }
        var endpoint = sub.endpoint;
        return sub.unsubscribe().then(function () {
          return post('push.unsubscribe', { endpoint: endpoint });
        });
      })
      .catch(function () { /* best-effort */ });
  }
})();
