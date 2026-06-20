<?php
/**
 * Lazy loader for the HubSpot Meetings embed script.
 *
 * Instead of an external request on every page load, the script is fetched only
 * when it's actually needed: on the first user interaction, when a booking modal
 * is opened via #cta-* hash, or (for always-visible inline embeds like /business)
 * shortly after load. It then renders any .meetings-iframe-container[data-src].
 *
 * Guarded so it is emitted at most once per page.
 */
if (defined('VT_HS_LOADER_RENDERED')) { return; }
define('VT_HS_LOADER_RENDERED', true);
?>
<script>
(function () {
  var loaded = false;
  function loadHS() {
    if (loaded) { return; }
    loaded = true;
    var s = document.createElement('script');
    s.src = 'https://static.hsappstatic.net/MeetingsEmbed/ex/MeetingsEmbedCode.js';
    s.async = true;
    s.addEventListener('load', function () {
      setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 120);
    });
    document.body.appendChild(s);
  }
  // Load on the first real interaction (covers every booking CTA / modal open).
  ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach(function (ev) {
    window.addEventListener(ev, loadHS, { once: true, passive: true });
  });
  // Or when a booking modal is opened via the URL hash — load (once) and nudge a
  // resize so the embedded scheduler sizes correctly each time a modal shows.
  window.addEventListener('hashchange', function () {
    if (/^#cta-/.test(location.hash)) {
      loadHS();
      setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 140);
    }
  });
  if (/^#cta-/.test(location.hash)) { loadHS(); }
  // Always-visible inline embeds (not inside a .cta-modal) must populate on their
  // own — load a touch after first paint on those pages only.
  document.addEventListener('DOMContentLoaded', function () {
    var inline = Array.prototype.some.call(
      document.querySelectorAll('.meetings-iframe-container'),
      function (el) { return !el.closest('.cta-modal'); }
    );
    if (inline) { setTimeout(loadHS, 2500); }
  });
})();
</script>
