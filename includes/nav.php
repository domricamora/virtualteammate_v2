<?php
/**
 * Topbar + main navigation.
 * Anchors point to homepage sections so they resolve correctly from any subpage.
 */
$home_base = './'; // root-relative; works from /, /about/, /services/, etc.
?>
<!-- TOPBAR -->
<div class="topbar" role="complementary">
  <i class="fa-solid fa-hospital" aria-hidden="true"></i> Specialized in Medical &amp; Dental Virtual Assistants &mdash; HIPAA-Certified &amp; Fully Vetted
  &nbsp;&bull;&nbsp;
  <a href="<?= $home_base ?>#cta">Book Your Free Strategy Call <i class="fa-solid fa-arrow-right" style="font-size:11px;" aria-hidden="true"></i></a>
</div>

<!-- NAV -->
<nav class="nav" aria-label="Primary">
  <a href="<?= $home_base ?>" class="logo" aria-label="Virtual Teammate home">
    <img src="https://staging.virtualteammate.com/wp-content/uploads/2025/10/BLACK-AND-VIOLET-LOGO-1.png" alt="Virtual Teammate" width="180" height="53"/>
  </a>
  <div class="nav-links">
    <a href="<?= $home_base ?>#specialties">Healthcare <span class="nav-badge">Medical &middot; Dental</span></a>
    <a href="<?= $home_base ?>#calculator">ROI Calculator</a>
    <a href="<?= $home_base ?>#global">Global Network</a>
    <a href="<?= $home_base ?>#profiles">VA Profiles</a>
    <a href="<?= $home_base ?>#faq">FAQ</a>
  </div>
  <div class="nav-right">
    <a href="tel:+14808472498" class="nav-phone" aria-label="Call (480) 847-2498">
      <i class="fa-solid fa-phone" aria-hidden="true"></i>(480) 847-2498
    </a>
    <a href="<?= $home_base ?>#cta" class="btn-nav">Book Free Session</a>
  </div>
</nav>
