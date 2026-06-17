<?php
$page_title       = 'Terms of Service | Virtual Teammate';
$page_description = 'The terms governing your use of the Virtual Teammate website. Staffing engagements are covered by a separate signed service agreement.';
$canonical        = 'https://virtualteammate.com/terms/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',  'url' => '/'],
  ['name' => 'Terms', 'url' => '/terms/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>
<main>
<article class="legal">
  <div class="legal-head">
    <div class="sec-lbl"><i class="fa-solid fa-file-contract"></i> Legal</div>
    <h1>Terms of Service</h1>
    <div class="legal-updated">Last updated: June 13, 2026</div>
  </div>

  <p class="legal-intro">These Terms of Service (&ldquo;Terms&rdquo;) govern your access to and use of the Virtual Teammate website at virtualteammate.com (the &ldquo;Site&rdquo;). By using the Site you agree to these Terms. If you do not agree, please do not use the Site.</p>

  <p><strong>Important:</strong> these Terms cover your use of this <em>website</em> only. Any staffing or virtual-assistant engagement with Virtual Teammate is governed by a separate written service agreement (and, for engagements involving protected health information, a Business Associate Agreement). Where those agreements conflict with these Terms, the signed agreement controls for that engagement.</p>

  <h2>1. Who we are</h2>
  <p>&ldquo;Virtual Teammate,&rdquo; &ldquo;VT,&rdquo; &ldquo;we,&rdquo; &ldquo;us,&rdquo; and &ldquo;our&rdquo; refer to Virtual Teammate, a US-owned staffing agency headquartered in Arizona that places medical, dental, and business virtual assistants. You can reach us at <a href="mailto:support@virtualteammate.com">support@virtualteammate.com</a>.</p>

  <h2>2. Using the Site</h2>
  <p>You may use the Site only for lawful purposes and in accordance with these Terms. You agree not to:</p>
  <ul>
    <li>use the Site in any way that violates applicable law or regulation;</li>
    <li>attempt to gain unauthorized access to the Site, its servers, or any connected systems;</li>
    <li>interfere with or disrupt the Site, or introduce malware, scraping bots, or automated data-collection tools without our written permission;</li>
    <li>submit false information, impersonate another person, or use the contact forms to send spam or unsolicited marketing.</li>
  </ul>

  <h2>3. Forms and submissions</h2>
  <p>When you submit a contact form, request a checklist, book an audit, or otherwise share information through the Site, you confirm that the information you provide is accurate and that you are authorized to provide it. We use submissions to respond to your inquiry and to provide the materials or follow-up you requested, as described in our <a href="<?= $home_base ?>privacy-policy/">Privacy Policy</a>. Please do not submit any patient or protected health information through the Site&rsquo;s public forms.</p>

  <h2>4. Intellectual property</h2>
  <p>The Site and its content, including text, graphics, logos, photographs, and the &ldquo;Virtual Teammate&rdquo; name and marks, are owned by Virtual Teammate or its licensors and are protected by intellectual-property laws. You may view and print Site content for your own informational use, but you may not copy, reproduce, republish, or distribute it for commercial purposes without our prior written consent.</p>

  <h2>5. Third-party links and services</h2>
  <p>The Site may link to third-party websites, scheduling tools, or services we do not control. We provide these links for convenience and are not responsible for the content, accuracy, or practices of any third-party site. Your use of a third-party service is governed by that party&rsquo;s own terms and privacy policy.</p>

  <h2>6. No professional advice</h2>
  <p>Content on the Site is provided for general informational purposes only and does not constitute medical, legal, accounting, compliance, or other professional advice. References to HIPAA, SOC 2, BAAs, or other standards describe how we operate our service and are not a substitute for advice from your own qualified advisors. You are responsible for your practice&rsquo;s own compliance obligations.</p>

  <h2>7. Disclaimers</h2>
  <p>The Site is provided &ldquo;as is&rdquo; and &ldquo;as available,&rdquo; without warranties of any kind, whether express or implied, including implied warranties of merchantability, fitness for a particular purpose, and non-infringement. We do not warrant that the Site will be uninterrupted, error-free, or free of harmful components, or that any result described on the Site (including savings figures or performance metrics) will be achieved in your specific engagement.</p>

  <h2>8. Limitation of liability</h2>
  <p>To the fullest extent permitted by law, Virtual Teammate and its officers, employees, and agents will not be liable for any indirect, incidental, special, consequential, or punitive damages, or for any loss of profits, revenue, data, or goodwill, arising out of or related to your use of the Site, even if we have been advised of the possibility of such damages. Liability arising from a staffing engagement is addressed in the applicable service agreement.</p>

  <h2>9. Indemnification</h2>
  <p>You agree to indemnify and hold harmless Virtual Teammate from any claims, damages, liabilities, and expenses (including reasonable legal fees) arising out of your misuse of the Site or your violation of these Terms.</p>

  <h2>10. Changes to these Terms</h2>
  <p>We may update these Terms from time to time. The &ldquo;Last updated&rdquo; date above reflects the most recent revision. Material changes take effect when we post the revised Terms on this page; your continued use of the Site after that constitutes acceptance.</p>

  <h2>11. Governing law</h2>
  <p>These Terms are governed by the laws of the State of Arizona, USA, without regard to its conflict-of-laws rules. Any dispute relating to the Site will be subject to the exclusive jurisdiction of the state and federal courts located in Arizona.</p>

  <h2>12. Contact us</h2>
  <p>Questions about these Terms? Email <a href="mailto:support@virtualteammate.com">support@virtualteammate.com</a> or use our <a href="<?= $home_base ?>contact/">contact page</a>.</p>
</article>
</main>
<?php $hide_lead_band = true; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
