/* VT Portal PWA bootstrap: service-worker registration + install affordance.
 * Loaded on every portal page (including the login screen) so the app can be
 * installed before or after authentication. No framework, no dependencies. */
(function () {
  'use strict';

  // Portal directory base, derived from the current URL so it works whether
  // the portal lives at /vtnew/portal/ (local) or /portal/ (staging).
  var BASE = location.pathname.replace(/[^/]*$/, '');

  /* ---- Service worker ---- */
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register(BASE + 'sw.js', { scope: BASE })
        .then(function (reg) {
          // When a new SW is waiting, activate it immediately on next load.
          if (reg.waiting) { reg.waiting.postMessage('skipWaiting'); }
          reg.addEventListener('updatefound', function () {
            var sw = reg.installing;
            if (!sw) { return; }
            sw.addEventListener('statechange', function () {
              if (sw.state === 'installed' && navigator.serviceWorker.controller) {
                sw.postMessage('skipWaiting');
              }
            });
          });
        })
        .catch(function () { /* SW is an enhancement; ignore failures */ });
    });
  }

  /* ---- Install affordance ---- */
  var DISMISS_KEY = 'vt_pwa_install_dismissed';
  var DISMISS_DAYS = 14;

  function isStandalone() {
    return (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) ||
           window.navigator.standalone === true;
  }

  function recentlyDismissed() {
    try {
      var ts = parseInt(localStorage.getItem(DISMISS_KEY) || '0', 10);
      return ts && (Date.now() - ts) < DISMISS_DAYS * 86400000;
    } catch (e) { return false; }
  }

  function rememberDismiss() {
    try { localStorage.setItem(DISMISS_KEY, String(Date.now())); } catch (e) {}
  }

  function buildBanner(opts) {
    var bar = document.createElement('div');
    bar.className = 'pwa-install-banner';
    bar.setAttribute('role', 'dialog');
    bar.setAttribute('aria-label', 'Install VT Portal app');

    var icon = document.createElement('img');
    icon.className = 'pwa-install-icon';
    icon.src = BASE + 'assets/icons/icon-192.png';
    icon.alt = '';
    bar.appendChild(icon);

    var txt = document.createElement('div');
    txt.className = 'pwa-install-text';
    txt.innerHTML = '<strong>Install VT Portal</strong><span>' + opts.subtitle + '</span>';
    bar.appendChild(txt);

    var actions = document.createElement('div');
    actions.className = 'pwa-install-actions';

    if (opts.actionLabel) {
      var act = document.createElement('button');
      act.type = 'button';
      act.className = 'pwa-install-btn';
      act.textContent = opts.actionLabel;
      act.addEventListener('click', opts.onAction);
      actions.appendChild(act);
    }

    var close = document.createElement('button');
    close.type = 'button';
    close.className = 'pwa-install-close';
    close.setAttribute('aria-label', 'Dismiss');
    close.innerHTML = '&times;';
    close.addEventListener('click', function () {
      rememberDismiss();
      bar.remove();
    });
    actions.appendChild(close);

    bar.appendChild(actions);
    document.body.appendChild(bar);
    // Trigger the slide-in transition.
    requestAnimationFrame(function () { bar.classList.add('is-visible'); });
    return bar;
  }

  var ua = window.navigator.userAgent || '';
  var isIOS = /iphone|ipad|ipod/i.test(ua) && !window.MSStream;
  var isIOSSafari = isIOS && /safari/i.test(ua) && !/crios|fxios|edgios/i.test(ua);

  var deferredPrompt = null;
  var banner = null;

  function doPrompt() {
    if (!deferredPrompt) { return; }
    deferredPrompt.prompt();
    deferredPrompt.userChoice.finally(function () {
      deferredPrompt = null;
      if (banner) { banner.remove(); banner = null; }
    });
  }

  function hideCtas() {
    document.querySelectorAll('.pwa-install-cta').forEach(function (el) { el.style.display = 'none'; });
  }

  // Chromium fires this when the app is installable — keep the prompt for the
  // explicit "Install app" buttons, and gently surface a banner too.
  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;
    if (!banner && !isStandalone() && !recentlyDismissed()) {
      banner = buildBanner({
        subtitle: 'Add it to your home screen for one-tap access.',
        actionLabel: 'Install',
        onAction: doPrompt
      });
    }
  });

  window.addEventListener('appinstalled', function () {
    rememberDismiss();
    if (banner) { banner.remove(); banner = null; }
    hideCtas();
  });

  // Public entry point for the "Install app" links on the dashboard + login.
  window.vtPromptInstall = function () {
    if (deferredPrompt) { doPrompt(); return; }
    if (banner) { return; }
    banner = buildBanner({
      subtitle: isIOSSafari
        ? 'Tap the Share icon, then “Add to Home Screen”.'
        : 'Open your browser menu and choose “Install app” / “Add to Home screen”.',
      actionLabel: null,
      onAction: null
    });
  };

  // Wire up the explicit install buttons (hidden once running as an app).
  function bindCtas() {
    if (isStandalone()) { hideCtas(); return; }
    document.querySelectorAll('.pwa-install-cta').forEach(function (el) {
      el.addEventListener('click', function (ev) { ev.preventDefault(); window.vtPromptInstall(); });
    });
  }
  if (document.readyState !== 'loading') { bindCtas(); }
  else { document.addEventListener('DOMContentLoaded', bindCtas); }

  // iOS Safari never fires beforeinstallprompt — auto-hint once (unless dismissed).
  if (isIOSSafari && !isStandalone() && !recentlyDismissed()) {
    window.addEventListener('load', function () {
      if (banner) { return; }
      banner = buildBanner({
        subtitle: 'Tap the Share icon, then “Add to Home Screen”.',
        actionLabel: null,
        onAction: null
      });
    });
  }
})();
