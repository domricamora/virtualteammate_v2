<?php
/**
 * Site footer + scroll-to-top button + main JS load.
 * Set $hide_footer = true before include to suppress the footer (rarely needed).
 * Pages may set $home_base before include (defaults to './' for the homepage).
 */
$home_base = $home_base ?? './';
$hide_footer = $hide_footer ?? false;
?>
<?php if (!$hide_footer): ?>
<!-- FOOTER -->
<footer class="footer" role="contentinfo">
  <div class="ft-grid">
    <div>
      <div class="ft-logo">
        <img src="<?= $home_base ?>images/logo.webp" alt="Virtual Teammate" width="180" height="60" loading="lazy"/>
      </div>
      <p class="ft-about">The leading HIPAA-certified virtual staffing solution for medical practices and dental clinics. Global talent network, US time zones, built to scale.</p>
      <address class="ft-contact" style="font-style:normal;">
        <i class="fa-solid fa-location-dot" aria-hidden="true"></i> 2425 East Camelback Road, Phoenix, AZ 85016<br>
        <i class="fa-solid fa-phone" aria-hidden="true"></i> <a href="tel:+14808472498">(480) 847-2498</a><br>
        <i class="fa-solid fa-envelope" aria-hidden="true"></i> <a href="mailto:clientsuccess@virtualteammate.com">clientsuccess@virtualteammate.com</a>
      </address>
    </div>
    <nav aria-label="Medical services">
      <div class="ft-h">Medical VAs</div>
      <ul class="ft-links">
        <li><a href="<?= $home_base ?>services/medical-administrative-support/">Medical Administrative Support</a></li>
        <li><a href="<?= $home_base ?>services/medical-receptionist/">Medical Receptionist</a></li>
        <li><a href="<?= $home_base ?>services/medical-biller/">Medical Biller</a></li>
        <li><a href="<?= $home_base ?>services/medical-scribe/">Medical Scribe</a></li>
        <li><a href="<?= $home_base ?>services/medical-assistant/">Medical Assistant</a></li>
      </ul>
    </nav>
    <nav aria-label="Dental services">
      <div class="ft-h">Dental VAs</div>
      <ul class="ft-links">
        <li><a href="<?= $home_base ?>services/dental-admin/">Dental Administrative Support</a></li>
        <li><a href="<?= $home_base ?>services/dental-receptionist/">Dental Receptionist</a></li>
        <li><a href="<?= $home_base ?>services/dental-biller/">Dental Biller</a></li>
        <li><a href="<?= $home_base ?>services/dental-scribe/">Dental Scribe</a></li>
        <li><a href="<?= $home_base ?>services/dental-coordinator/">Dental Coordinator</a></li>
      </ul>
    </nav>
    <nav aria-label="Company">
      <div class="ft-h">Company</div>
      <ul class="ft-links">
        <li><a href="<?= $home_base ?>about/">About Us</a></li>
        <li><a href="<?= $home_base ?>virtual-teammates/">Virtual Teammates</a></li>
        <li><a href="<?= $home_base ?>business/">Business &amp; Non-Profit VAs</a></li>
        <li><a href="<?= $home_base ?>guarantee/">30-Day Right-Fit Promise</a></li>
        <li><a href="<?= $home_base ?>case-studies/">Case Studies</a></li>
        <li><a href="<?= $home_base ?>careers/">Careers</a></li>
        <li><a href="<?= $home_base ?>contact/">Contact</a></li>
      </ul>
    </nav>
  </div>
  <div class="ft-bottom">
    <div class="ft-copy">&copy; <?= date('Y') ?> Virtual Teammate &middot; <a href="#">Terms</a> &amp; <a href="#">Privacy Policy</a> &middot; <a href="https://virtualteammate.com/login-page/" class="ft-portal-link"><i class="fa-solid fa-lock" aria-hidden="true"></i> Portal Login</a></div>
    <div class="ft-seo" aria-hidden="true">
      <span>Medical Virtual Assistant</span>
      <span>Dental Virtual Assistant</span>
      <span>HIPAA Virtual Staffing</span>
      <span>Global Healthcare VAs</span>
      <span>Medical Billing VA</span>
      <span>Medical Scribe VA</span>
    </div>
  </div>
</footer>
<?php endif; ?>

<!-- SCROLL TO TOP -->
<button class="scroll-top" id="scrollTop" aria-label="Scroll to top" type="button">
  <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
</button>

<script src="<?= $home_base ?>js/main.js?v=<?= @filemtime(__DIR__ . '/../js/main.js') ?: time() ?>" defer></script>
<!-- Traffic beacon — fires once after load so it never blocks render. -->
<script>
(function(){
  function vtTrack(){
    try{
      var qs = 'p=' + encodeURIComponent(location.pathname) +
               '&r=' + encodeURIComponent(document.referrer || '');
      var url = '<?= $home_base ?>track.php?' + qs;
      if (navigator.sendBeacon){
        navigator.sendBeacon(url);
      } else {
        var img = new Image(); img.src = url + '&t=' + Date.now();
      }
    }catch(e){}
  }
  if (document.readyState === 'complete'){ vtTrack(); }
  else { window.addEventListener('load', vtTrack); }
})();
</script>
</body>
</html>
