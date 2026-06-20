<?php
/**
 * HubSpot tracking + Conversations (chat) embed — Hub ID 46221241.
 *
 * The standard HubSpot embed code: async + defer so it never blocks render, but
 * loads on page load so visitor tracking/analytics fire reliably (and the chat
 * launcher appears). Included by includes/footer.php on production hosts only
 * (see the $__vt_nonprod gate there). Guarded so it is emitted at most once.
 */
if (defined('VT_HS_CHAT_RENDERED')) { return; }
define('VT_HS_CHAT_RENDERED', true);
?>
<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/46221241.js"></script>
<!-- End of HubSpot Embed Code -->
