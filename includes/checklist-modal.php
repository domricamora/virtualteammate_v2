<?php
/**
 * Reusable "HIPAA VA buyer's checklist" modal (#cta-buyers-checklist).
 *
 * Opens on the SAME page via the #cta-buyers-checklist hash (CSS :target). The
 * form is the embedded HubSpot form (GUID 237eccba… / portal 46221241), so
 * submissions go straight to HubSpot (form submission + contact). The embed JS is
 * lazy-loaded only when the modal is opened, so it adds no request to the initial
 * page load.
 *
 * Self-contained & idempotent (guard). Set $home_base before include.
 */
if (defined('VT_CHECKLIST_MODAL_RENDERED')) { return; }
define('VT_CHECKLIST_MODAL_RENDERED', true);
$home_base = $home_base ?? './';
?>
<div class="cta-modal" id="cta-buyers-checklist" role="dialog" aria-modal="true" aria-labelledby="cbm-bc-h">
  <a class="cta-modal-scrim" href="#" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card">
    <a class="cta-modal-x" href="#" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-file-lines"></i> Just exploring</div>
    <h2 class="cta-modal-h" id="cbm-bc-h">Grab the HIPAA VA buyer&rsquo;s checklist</h2>
    <p class="cta-modal-sub">Unlock new levels of productivity and patient care. Enter your email to receive our HIPAA VA buyer&rsquo;s checklist and learn how to choose the right virtual staffing partner for long-term success.</p>
    <!-- HubSpot embedded form (lazy-rendered on open) -->
    <div id="hs-checklist-form" class="hs-embed"></div>
  </div>
</div>
<style>
/* Make the embedded HubSpot form legible on the dark modal. */
.hs-embed .hs-form-field > label, .hs-embed legend.hs-field-desc, .hs-embed .hs-field-desc{color:rgba(255,255,255,.85);font-size:13px;}
.hs-embed input[type=email], .hs-embed input[type=text], .hs-embed input[type=tel], .hs-embed textarea, .hs-embed select{
  width:100%;padding:12px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.2);
  background:rgba(255,255,255,.06);color:#fff;font-family:inherit;font-size:15px;margin-top:6px;}
.hs-embed ::placeholder{color:rgba(255,255,255,.45);}
.hs-embed .hs-button, .hs-embed input[type=submit]{
  background:var(--violet-dk,#3919ba);color:#fff;border:0;padding:13px 24px;border-radius:8px;
  font-weight:700;font-size:15px;cursor:pointer;margin-top:14px;font-family:inherit;}
.hs-embed .hs-button:hover{filter:brightness(1.08);}
.hs-embed .hs-error-msg, .hs-embed .hs-error-msgs label{color:#ffb4b4;font-size:12.5px;}
.hs-embed .hs-form-field{margin-bottom:14px;}
.hs-embed .submitted-message{color:#fff;font-size:15px;}
</style>
<script>
(function () {
  var made = false, loading = false;
  function createForm() {
    if (made || !window.hbspt || !window.hbspt.forms) { return; }
    made = true;
    window.hbspt.forms.create({
      portalId: "46221241",
      formId: "237eccba-6bc3-42bc-960a-a1588703e03d",
      region: "na1",
      target: "#hs-checklist-form"
    });
  }
  function loadEmbed() {
    if (made) { return; }
    if (window.hbspt && window.hbspt.forms) { createForm(); return; }
    if (loading) { return; }
    loading = true;
    var s = document.createElement('script');
    s.src = '//js.hsforms.net/forms/embed/v2.js';
    s.charset = 'utf-8';
    s.async = true;
    s.onload = createForm;
    document.body.appendChild(s);
  }
  // Load when the checklist modal is opened (hash or a CTA click).
  function maybeHash() { if (location.hash === '#cta-buyers-checklist') { loadEmbed(); } }
  window.addEventListener('hashchange', maybeHash);
  document.addEventListener('click', function (e) {
    var t = e.target.closest('a[href$="#cta-buyers-checklist"], [data-cta-intent="buyers-checklist"]');
    if (t) { loadEmbed(); }
  });
  maybeHash();
})();
</script>
