<?php
/**
 * LeadDyno affiliate / referral tracking.
 *
 * Loaded after first paint (on the window 'load' event) so it never blocks
 * render, but NOT gated behind a user interaction — recordVisit() must run on
 * every visit so affiliate attribution (the ?aff= code captured on landing) is
 * never missed. Included by includes/footer.php on production hosts only.
 *
 * Guarded so it is emitted at most once per page.
 */
if (defined('VT_LEADDYNO_RENDERED')) { return; }
define('VT_LEADDYNO_RENDERED', true);
?>
<!-- LeadDyno (deferred) -->
<script>
(function () {
  function loadLeadDyno() {
    var s = document.createElement('script');
    s.src = 'https://static.leaddyno.com/js';
    s.async = true;
    s.onload = function () {
      try {
        LeadDyno.key = '9b8c25853a83e2fecaa89421b4c281ffa0bced35';
        LeadDyno.recordVisit();
        LeadDyno.autoWatch();
      } catch (e) {}
    };
    document.body.appendChild(s);
  }
  if (document.readyState === 'complete') { loadLeadDyno(); }
  else { window.addEventListener('load', loadLeadDyno); }
})();
</script>
