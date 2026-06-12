<?php
$page_title       = 'Privacy Policy | Virtual Teammate';
$page_description = 'How Virtual Teammate collects, uses, and protects the information you share through our website. No patient or protected health information is collected through public forms.';
$canonical        = 'https://virtualteammate.com/privacy-policy/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',    'url' => '/'],
  ['name' => 'Privacy', 'url' => '/privacy-policy/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>
<main>
<article class="legal">
  <div class="legal-head">
    <div class="sec-lbl"><i class="fa-solid fa-user-shield"></i> Legal</div>
    <h1>Privacy Policy</h1>
    <div class="legal-updated">Last updated: June 13, 2026</div>
  </div>

  <p class="legal-intro">This Privacy Policy explains how Virtual Teammate (&ldquo;VT,&rdquo; &ldquo;we,&rdquo; &ldquo;us&rdquo;) collects, uses, and protects information when you visit virtualteammate.com (the &ldquo;Site&rdquo;) or contact us through it. We&rsquo;ve written it in plain language so you know exactly what happens to the details you share.</p>

  <p><strong>No patient data here.</strong> This Site is a marketing website. We do not request, and you should not submit, any patient or protected health information (PHI) through its public forms. PHI is handled only inside active client engagements, under a signed Business Associate Agreement and the safeguards described in our <a href="<?= $home_base ?>#security">Security &amp; Compliance</a> section.</p>

  <h2>1. Information we collect</h2>
  <h3>Information you give us</h3>
  <p>When you complete a form &mdash; for example, requesting the Buyer&rsquo;s Checklist, booking a Practice Staffing Audit, requesting a teammate, or contacting us &mdash; we collect the details you choose to provide, such as your name, work email, phone number, practice or company, role, and anything you write in a message field.</p>
  <h3>Information we collect automatically</h3>
  <p>Like most websites, we automatically collect limited technical data when you browse &mdash; such as your IP address, browser type, device, pages viewed, and referring page &mdash; through server logs and cookies or similar technologies. This helps us keep the Site secure and understand how it&rsquo;s used.</p>

  <h2>2. How we use your information</h2>
  <ul>
    <li>to respond to your inquiry and send the materials, audit, or follow-up you requested;</li>
    <li>to contact you about our virtual-assistant services and relevant updates (you can opt out at any time);</li>
    <li>to operate, secure, maintain, and improve the Site;</li>
    <li>to comply with our legal obligations and enforce our <a href="<?= $home_base ?>terms/">Terms of Service</a>.</li>
  </ul>

  <h2>3. How we share information</h2>
  <p>We do not sell your personal information. We share it only:</p>
  <ul>
    <li><strong>With service providers</strong> who help us run our business &mdash; for example, our CRM and email-delivery providers &mdash; under agreements that limit their use of your information to providing services to us;</li>
    <li><strong>For legal reasons</strong> &mdash; when required by law, to respond to lawful requests, or to protect our rights, users, or the public;</li>
    <li><strong>In a business transfer</strong> &mdash; if VT is involved in a merger, acquisition, or sale of assets, information may transfer as part of that transaction.</li>
  </ul>

  <h2>4. Cookies and analytics</h2>
  <p>We use cookies and similar technologies to keep the Site working, remember preferences, and measure traffic and performance. You can control cookies through your browser settings; disabling some cookies may affect how parts of the Site function.</p>

  <h2>5. Data retention</h2>
  <p>We keep the information you submit for as long as needed to respond to you, manage our relationship, and meet legal or operational requirements, after which we delete or de-identify it.</p>

  <h2>6. How we protect your information</h2>
  <p>We maintain administrative, technical, and physical safeguards designed to protect the information you share through the Site. No method of transmission or storage is completely secure, however, so we cannot guarantee absolute security.</p>

  <h2>7. Your choices and rights</h2>
  <p>You can ask us to access, correct, or delete the personal information you&rsquo;ve provided, and you can unsubscribe from marketing emails using the link in any message or by emailing us. Depending on where you live (for example, under the California Consumer Privacy Act or the EU/UK GDPR), you may have additional rights &mdash; including the right to know what we hold, to request deletion, and to opt out of certain processing. To exercise any right, contact us using the details below; we will respond as required by applicable law.</p>

  <h2>8. Children&rsquo;s privacy</h2>
  <p>The Site is intended for businesses and is not directed to children under 16. We do not knowingly collect personal information from children. If you believe a child has provided us information, please contact us and we will delete it.</p>

  <h2>9. Third-party links</h2>
  <p>The Site may link to third-party websites and scheduling tools we do not control. Their privacy practices are governed by their own policies, and we encourage you to review them.</p>

  <h2>10. Changes to this policy</h2>
  <p>We may update this Privacy Policy from time to time. The &ldquo;Last updated&rdquo; date above reflects the most recent revision, and material changes take effect when posted on this page.</p>

  <h2>11. Contact us</h2>
  <p>Questions, or want to exercise a privacy right? Email <a href="mailto:support@virtualteammate.com">support@virtualteammate.com</a> or visit our <a href="<?= $home_base ?>contact/">contact page</a>.</p>
</article>
</main>
<?php $hide_lead_band = true; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
