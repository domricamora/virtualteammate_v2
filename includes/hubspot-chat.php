<?php
/**
 * HubSpot Conversations (live chat) widget — Hub ID 46221241 (region na1).
 *
 * Lazy-loaded so it isn't an external request on the initial page load: injected
 * on the first user interaction, or after a short idle fallback so the chat
 * launcher still appears for visitors who don't interact. Included by
 * includes/footer.php on production hosts only (see $__vt_nonprod gate there).
 *
 * Guarded so it is emitted at most once per page.
 */
if (defined('VT_HS_CHAT_RENDERED')) { return; }
define('VT_HS_CHAT_RENDERED', true);
?>
<!-- HubSpot Conversations (lazy) -->
<script>
(function () {
  var loaded = false;
  function loadChat() {
    if (loaded) { return; }
    loaded = true;
    var s = document.createElement('script');
    s.id = 'hs-script-loader';
    s.type = 'text/javascript';
    s.async = true;
    s.defer = true;
    s.src = '//js-na1.hs-scripts.com/46221241.js';
    document.body.appendChild(s);
  }
  ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach(function (ev) {
    window.addEventListener(ev, loadChat, { once: true, passive: true });
  });
  // Fallback so the chat launcher appears even for idle visitors.
  setTimeout(loadChat, 5000);
})();
</script>
