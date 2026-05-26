<?php
/**
 * Site footer + scroll-to-top button + main JS load.
 * Set $hide_footer = true before include to suppress the footer (rarely needed).
 */
$home_base = './';
$hide_footer = $hide_footer ?? false;
?>
<?php if (!$hide_footer): ?>
<!-- FOOTER -->
<footer class="footer" role="contentinfo">
  <div class="ft-grid">
    <div>
      <div class="ft-logo">
        <img src="images/logo.webp" alt="Virtual Teammate" width="180" height="60" loading="lazy"/>
      </div>
      <p class="ft-about">The leading HIPAA-certified virtual staffing solution for medical practices and dental clinics. Global talent network, US time zones, built to scale.</p>
      <address class="ft-contact" style="font-style:normal;">
        <i class="fa-solid fa-location-dot" aria-hidden="true"></i> 20118 N 67th Ave, Suite 300-523, Glendale, AZ 85308<br>
        <i class="fa-solid fa-phone" aria-hidden="true"></i> <a href="tel:+14808472498">(480) 847-2498</a><br>
        <i class="fa-solid fa-envelope" aria-hidden="true"></i> <a href="mailto:clientsuccess@virtualteammate.com">clientsuccess@virtualteammate.com</a>
      </address>
    </div>
    <nav aria-label="Healthcare services">
      <div class="ft-h">Healthcare</div>
      <ul class="ft-links">
        <li><a href="<?= $home_base ?>#specialties">Medical Virtual Assistants</a></li>
        <li><a href="<?= $home_base ?>#specialties">Dental Virtual Assistants</a></li>
        <li><a href="<?= $home_base ?>#specialties">Medical Billing VAs</a></li>
        <li><a href="<?= $home_base ?>#specialties">Medical Scribing VAs</a></li>
        <li><a href="<?= $home_base ?>#specialties">Patient Scheduling VAs</a></li>
        <li><a href="<?= $home_base ?>#specialties">Prior Authorization VAs</a></li>
      </ul>
    </nav>
    <nav aria-label="Resources">
      <div class="ft-h">Resources</div>
      <ul class="ft-links">
        <li><a href="<?= $home_base ?>#calculator">ROI Calculator</a></li>
        <li><a href="<?= $home_base ?>#global">Global Network</a></li>
        <li><a href="<?= $home_base ?>#profiles">VA Profiles</a></li>
        <li><a href="<?= $home_base ?>#faq">FAQs</a></li>
        <li><a href="<?= $home_base ?>#testimonials">Case Studies</a></li>
        <li><a href="<?= $home_base ?>#">Blog</a></li>
      </ul>
    </nav>
    <nav aria-label="Company">
      <div class="ft-h">Company</div>
      <ul class="ft-links">
        <li><a href="<?= $home_base ?>#">About Us</a></li>
        <li><a href="<?= $home_base ?>#business">Business Services</a></li>
        <li><a href="<?= $home_base ?>#testimonials">Testimonials</a></li>
        <li><a href="<?= $home_base ?>#">Careers</a></li>
        <li><a href="<?= $home_base ?>#cta">Contact</a></li>
      </ul>
    </nav>
  </div>
  <div class="ft-bottom">
    <div class="ft-copy">&copy; <?= date('Y') ?> Virtual Teammate &middot; <a href="#">Terms</a> &amp; <a href="#">Privacy Policy</a></div>
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

<script src="js/main.js" defer></script>
</body>
</html>
