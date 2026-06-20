<?php
/**
 * /services/ — healthcare services hub. Lists every service sub-page.
 *
 * Published on LOCALHOST ONLY (work-in-progress). On any other host it bounces to
 * the homepage #specialties section, exactly as before — so it stays unpublished
 * in production/staging until you're ready to launch it.
 */
$__host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$__isLocal = str_contains($__host, 'localhost') || str_starts_with($__host, '127.0.0.1') || str_contains($__host, '::1');
if (!$__isLocal) {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/services/index.php'), '/\\');
    $base = ($base === '' || $base === '.') ? '' : $base;
    $home = preg_replace('#/services$#', '/', $base) ?: '/';
    header('Location: ' . $home . '#specialties', true, 302);
    exit;
}

$page_title       = 'Healthcare & Dental VA Services | Virtual Teammate';
$page_description = 'Browse every Virtual Teammate role for medical and dental practices — billing, reception, scribing, admin support and more.';
$canonical        = 'https://virtualteammate.com/services/';
$home_base        = '../';
$robots           = 'noindex,nofollow';   // localhost-only WIP
$breadcrumbs      = [
  ['name' => 'Home',     'url' => '/'],
  ['name' => 'Services', 'url' => '/services/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';

$medical = [
  ['medical-administrative-support', 'fa-clipboard-list', 'Medical Administrative Support', 'Charts, intake, records and the day-to-day admin that keeps the office moving.'],
  ['medical-receptionist',           'fa-headset',        'Medical Receptionist',          'Answers the phones, books the schedule, and greets every patient — virtually.'],
  ['medical-biller',                 'fa-file-invoice-dollar', 'Medical Biller',           'Clean claims, worked denials, and unpaid claims chased until you get paid.'],
  ['medical-scribe',                 'fa-pen-clip',       'Medical Scribe',                'Real-time notes in your EHR so you can focus on the patient, not the keyboard.'],
  ['medical-assistant',              'fa-user-nurse',     'Medical Assistant',             'Clinical and administrative support that takes the busywork off your team.'],
];
$dental = [
  ['dental-admin',        'fa-clipboard-list', 'Dental Administrative Support', 'Records, verifications and treatment-plan prep, handled before the chair is filled.'],
  ['dental-receptionist', 'fa-headset',        'Dental Receptionist',           'Live front-desk calls, scheduling and recall so no patient slips away.'],
  ['dental-biller',       'fa-file-invoice-dollar', 'Dental Biller',            'Insurance billing, claims and posting that keep production landing on time.'],
  ['dental-scribe',       'fa-pen-clip',       'Dental Scribe',                 'Accurate clinical and perio charting captured as you work.'],
  ['dental-coordinator',  'fa-handshake-angle','Dental Coordinator',            'Case acceptance, recall and scheduling that turn yeses into booked visits.'],
];
$card = static function (array $s) use ($home_base): void {
    [$slug, $icon, $name, $blurb] = $s;
    echo '<a class="svh-card reveal" href="' . $home_base . 'services/' . $slug . '/">'
       . '<span class="ico-circle lg"><i class="fa-solid ' . $icon . '"></i></span>'
       . '<h3>' . htmlspecialchars($name) . '</h3>'
       . '<p>' . htmlspecialchars($blurb) . '</p>'
       . '<span class="svh-link">Learn more <i class="fa-solid fa-arrow-right"></i></span>'
       . '</a>';
};
?>
<style>
.svh-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-top:28px;}
@media (max-width:980px){.svh-grid{grid-template-columns:1fr 1fr;}}
@media (max-width:620px){.svh-grid{grid-template-columns:1fr;}}
.svh-card{display:flex;flex-direction:column;align-items:flex-start;gap:12px;text-decoration:none;
  background:var(--glass-bg,rgba(255,255,255,.04));border:1px solid rgba(255,255,255,.08);border-radius:18px;
  padding:26px 24px;transition:transform .25s ease,border-color .25s ease,box-shadow .25s ease;}
.svh-card:hover{transform:translateY(-4px);border-color:rgba(223,169,73,.45);box-shadow:0 18px 50px -22px rgba(223,169,73,.35);}
.svh-card h3{font-size:18px;font-weight:700;color:#fff;margin:0;letter-spacing:-.2px;}
.svh-card p{font-size:14px;line-height:1.55;color:var(--text-soft,#c9c8e2);margin:0;}
.svh-link{margin-top:auto;color:var(--gold,#dfa949);font-weight:700;font-size:14px;display:inline-flex;align-items:center;gap:8px;}
.svh-group-h{margin:46px 0 0;}
</style>
<main>
<header class="svc-hero">
  <div class="orb orb1"></div><div class="orb orb2"></div>
  <div class="svc-hero-inner reveal" style="max-width:820px;">
    <div class="svc-eyebrow"><i class="fa-solid fa-stethoscope"></i> Roles we staff</div>
    <h1 class="svc-h1">Every role your practice needs, <em>handled by a teammate</em></h1>
    <p class="svc-lead">HIPAA-compliant medical and dental virtual assistants, matched to your software and your time zone. Pick the role you're missing — or book a quick audit and we'll tell you what to delegate first.</p>
    <div class="svc-cta-row">
      <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#cta-buyers-checklist" class="btn-glass" data-cta-intent="buyers-checklist">Get the HIPAA VA buyer&rsquo;s checklist <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>
</header>

<section class="sec" style="padding-top:50px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-user-doctor"></i> Medical practices</div><h2 class="svc-h2" style="margin-bottom:0;">Medical virtual assistants</h2></div>
  <div class="svh-grid"><?php foreach ($medical as $s) { $card($s); } ?></div>

  <div class="reveal svh-group-h"><div class="sec-lbl"><i class="fa-solid fa-tooth"></i> Dental practices</div><h2 class="svc-h2" style="margin-bottom:0;">Dental virtual assistants</h2></div>
  <div class="svh-grid"><?php foreach ($dental as $s) { $card($s); } ?></div>
</section>

<?php include __DIR__ . '/../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
