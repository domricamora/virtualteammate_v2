/* VT Portal service worker.
 *
 * Caching strategy is deliberately conservative because the portal serves
 * per-user, authenticated HTML:
 *   - Navigations / HTML  -> network-first, response NEVER cached
 *                            (falls back to offline.html only when offline).
 *   - JSON / AJAX endpoints -> network-only (must always be fresh).
 *   - Shared static assets  -> stale-while-revalidate (css/js/icons/fonts).
 *
 * Bump CACHE_VERSION to force-evict old caches on the next activate.
 */
'use strict';

const CACHE_VERSION = 'vtportal-v2';
const STATIC_CACHE  = CACHE_VERSION + '-static';
const OFFLINE_URL   = 'offline.html';

// Stable URLs worth precaching so the app shell works on first offline load.
const PRECACHE = [
  OFFLINE_URL,
  'assets/icons/icon-192.png',
  'assets/icons/icon-512.png',
  'manifest.webmanifest',
];

// Same-origin static asset file types we may cache.
const STATIC_RE = /\.(css|js|png|jpe?g|webp|svg|gif|ico|woff2?|ttf|eot)$/i;

// Cross-origin hosts whose assets are safe to cache (fonts + icon CDN).
const CACHEABLE_HOSTS = [
  'fonts.googleapis.com',
  'fonts.gstatic.com',
  'cdnjs.cloudflare.com',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => cache.addAll(PRECACHE.map((u) => new Request(u, { cache: 'reload' }))))
      .catch(() => { /* a missing precache item must not block install */ })
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(
        keys.filter((k) => k !== STATIC_CACHE).map((k) => caches.delete(k))
      ))
      .then(() => self.clients.claim())
  );
});

// Allow the page to trigger an immediate update.
self.addEventListener('message', (event) => {
  if (event.data === 'skipWaiting') { self.skipWaiting(); }
});

function isStaticAsset(url) {
  if (url.origin === self.location.origin) {
    return STATIC_RE.test(url.pathname);
  }
  return CACHEABLE_HOSTS.includes(url.hostname);
}

// Stale-while-revalidate for shared static assets.
async function staleWhileRevalidate(request) {
  const cache = await caches.open(STATIC_CACHE);
  const cached = await cache.match(request);
  const network = fetch(request).then((res) => {
    // Only cache complete (or opaque CDN) responses.
    if (res && (res.ok || res.type === 'opaque')) {
      cache.put(request, res.clone()).catch(() => {});
    }
    return res;
  }).catch(() => null);
  return cached || network || fetch(request);
}

// Network-first for navigations; offline.html only when the network fails.
async function navigationFallback(request) {
  try {
    return await fetch(request);
  } catch (e) {
    const cache = await caches.open(STATIC_CACHE);
    const offline = await cache.match(OFFLINE_URL);
    return offline || new Response(
      '<h1>Offline</h1><p>You are offline and this page has not been cached.</p>',
      { status: 503, headers: { 'Content-Type': 'text/html' } }
    );
  }
}

self.addEventListener('fetch', (event) => {
  const { request } = event;

  // Never interfere with non-GET (logins, form POSTs, deletes, CSRF flows).
  if (request.method !== 'GET') { return; }

  const url = new URL(request.url);

  // Navigations / top-level HTML -> network-first, never cached.
  if (request.mode === 'navigate') {
    event.respondWith(navigationFallback(request));
    return;
  }

  // Shared static assets -> stale-while-revalidate.
  if (isStaticAsset(url)) {
    event.respondWith(staleWhileRevalidate(request));
    return;
  }

  // Everything else (JSON/AJAX: messages.fetch, notifications.*, *_json, media)
  // -> network-only. Do not call respondWith so the browser handles it normally.
});

/* ───────────── Web Push ───────────── */
self.addEventListener('push', function (event) {
  var data = {};
  try { data = event.data ? event.data.json() : {}; }
  catch (e) { data = { title: 'VT Portal', body: event.data ? event.data.text() : '' }; }

  var title = data.title || 'VT Portal';
  var options = {
    body:  data.body || '',
    icon:  'assets/icons/icon-192.png',
    badge: 'assets/icons/icon-192.png',
    tag:   data.kind || 'vtportal',
    data:  { link: data.link || 'index.php?p=dashboard' }
  };
  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  var link = (event.notification.data && event.notification.data.link) || 'index.php?p=dashboard';
  var url  = new URL(link, self.registration.scope).href;
  event.waitUntil(
    self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (wins) {
      for (var i = 0; i < wins.length; i++) {
        var w = wins[i];
        if (w.url.indexOf(self.registration.scope) === 0 && 'focus' in w) {
          if ('navigate' in w) { w.navigate(url); }
          return w.focus();
        }
      }
      if (self.clients.openWindow) { return self.clients.openWindow(url); }
    })
  );
});
