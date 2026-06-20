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

/* Each role: slug, icon, name, blurb, feature?, tag, 3 proof points (real, qualitative). */
$medical = [
  ['medical-biller', 'fa-file-invoice-dollar', 'Medical Biller',
   'Clean claims, worked denials, and unpaid claims chased until you get paid.',
   true, 'Specialist role',
   ['Clean claims out the door', 'Every denial worked', 'Aging A/R chased to paid']],
  ['medical-receptionist', 'fa-headset', 'Medical Receptionist',
   'Answers the phones, books the schedule, and greets every patient — virtually.', false, '', []],
  ['medical-administrative-support', 'fa-clipboard-list', 'Medical Administrative Support',
   'Charts, intake, records and the day-to-day admin that keeps the office moving.', false, '', []],
  ['medical-scribe', 'fa-pen-clip', 'Medical Scribe',
   'Real-time notes in your EHR so you can focus on the patient, not the keyboard.', false, '', []],
  ['medical-assistant', 'fa-user-nurse', 'Medical Assistant',
   'Clinical and administrative support that takes the busywork off your team.', false, '', []],
];
$dental = [
  ['dental-biller', 'fa-file-invoice-dollar', 'Dental Biller',
   'Insurance billing, claims and posting that keep production landing on time.',
   true, 'Specialist role',
   ['Claims sent with narratives & X-rays', 'Denials appealed', 'Production paid faster']],
  ['dental-receptionist', 'fa-headset', 'Dental Receptionist',
   'Live front-desk calls, scheduling and recall so no patient slips away.', false, '', []],
  ['dental-admin', 'fa-clipboard-list', 'Dental Administrative Support',
   'Records, verifications and treatment-plan prep, handled before the chair is filled.', false, '', []],
  ['dental-scribe', 'fa-pen-clip', 'Dental Scribe',
   'Accurate clinical and perio charting captured as you work.', false, '', []],
  ['dental-coordinator', 'fa-handshake-angle', 'Dental Coordinator',
   'Case acceptance, recall and scheduling that turn yeses into booked visits.', false, '', []],
];

/* Feature card spans two columns and carries a tag + proof points.
   Standard cards are compact. Both share .svh-card so hover/focus stay consistent. */
$card = static function (array $s) use ($home_base): void {
    [$slug, $icon, $name, $blurb, $feature, $tag, $points] = $s;
    $cls = 'svh-card reveal' . ($feature ? ' is-feature' : '');
    echo '<a class="' . $cls . '" href="' . $home_base . 'services/' . $slug . '/">';
    echo '<span class="svh-top">';
    echo '<span class="ico-circle lg"><i class="fa-solid ' . $icon . '"></i></span>';
    if ($feature && $tag !== '') {
        echo '<span class="svh-badge"><i class="fa-solid fa-star"></i> ' . htmlspecialchars($tag) . '</span>';
    }
    echo '</span>';
    echo '<h3>' . htmlspecialchars($name) . '</h3>';
    echo '<p>' . htmlspecialchars($blurb) . '</p>';
    if ($feature && $points) {
        echo '<ul class="svh-points">';
        foreach ($points as $p) {
            echo '<li><i class="fa-solid fa-check"></i> ' . htmlspecialchars($p) . '</li>';
        }
        echo '</ul>';
    }
    echo '<span class="svh-link">Learn more <i class="fa-solid fa-arrow-right"></i></span>';
    echo '</a>';
};
?>
<style>
/* ── Services hub: bento grids, hero trust strip, "where to start" band ──
   Reuses the site tokens (--gold, --glass-bg) and the .svc-* component family
   so the hub matches every service sub-page. */
.svh-trust{display:flex;flex-wrap:wrap;gap:10px 22px;margin-top:24px;}
.svh-trust .trust-item{font-size:13.5px;}

.svh-cat{display:flex;align-items:baseline;gap:14px;flex-wrap:wrap;margin:0;}
.svh-cat .svc-count{font-size:13px;font-weight:700;color:var(--gold,#dfa949);
  border:1px solid rgba(223,169,73,.4);border-radius:999px;padding:3px 11px;letter-spacing:.3px;}

.svh-bento{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-top:26px;}
.svh-bento + .svh-cat{margin-top:58px;}

.svh-card{display:flex;flex-direction:column;align-items:flex-start;gap:12px;text-decoration:none;
  background:var(--glass-bg,rgba(255,255,255,.04));border:1px solid rgba(255,255,255,.09);border-radius:20px;
  padding:24px 22px;transition:transform .22s ease,border-color .22s ease,box-shadow .22s ease,background .22s ease;}
.svh-card:hover{transform:translateY(-5px);border-color:rgba(223,169,73,.5);
  box-shadow:0 22px 55px -24px rgba(223,169,73,.4);}
.svh-card:focus-visible{outline:2px solid var(--gold,#dfa949);outline-offset:3px;}
.svh-top{display:flex;align-items:center;justify-content:space-between;width:100%;gap:12px;}
.svh-card h3{font-size:18px;font-weight:700;color:#fff;margin:0;letter-spacing:-.2px;}
.svh-card p{font-size:14px;line-height:1.55;color:var(--text-soft,#c9c8e2);margin:0;}
.svh-link{margin-top:auto;color:var(--gold,#dfa949);font-weight:700;font-size:14px;
  display:inline-flex;align-items:center;gap:8px;transition:gap .2s ease;}
.svh-card:hover .svh-link{gap:12px;}

/* Feature card: wider, gold-tinted, with proof points. */
.svh-card.is-feature{grid-column:span 2;gap:14px;
  background:linear-gradient(150deg,rgba(223,169,73,.12),rgba(255,255,255,.04) 60%);
  border-color:rgba(223,169,73,.32);}
.svh-card.is-feature h3{font-size:22px;}
.svh-card.is-feature p{font-size:15px;max-width:46ch;}
.svh-badge{display:inline-flex;align-items:center;gap:6px;font-size:11.5px;font-weight:800;
  text-transform:uppercase;letter-spacing:.6px;color:var(--gold,#dfa949);
  background:rgba(223,169,73,.14);border:1px solid rgba(223,169,73,.4);border-radius:999px;padding:5px 11px;}
.svh-points{list-style:none;margin:2px 0 0;padding:0;display:grid;grid-template-columns:1fr 1fr;gap:8px 20px;width:100%;}
.svh-points li{display:flex;align-items:flex-start;gap:8px;font-size:13.5px;color:var(--text-soft,#d6d5ec);line-height:1.4;}
.svh-points li i{color:var(--gold,#dfa949);margin-top:3px;font-size:11px;}

/* "Where to start" helper band. */
.svh-help{display:flex;align-items:center;justify-content:space-between;gap:28px;flex-wrap:wrap;
  margin-top:18px;padding:26px 30px;border-radius:22px;
  background:var(--glass-bg,rgba(255,255,255,.04));border:1px solid rgba(255,255,255,.09);}
.svh-help-t h3{font-size:21px;font-weight:800;color:#fff;margin:0 0 6px;letter-spacing:-.3px;}
.svh-help-t p{font-size:14.5px;color:var(--text-soft,#c9c8e2);margin:0;max-width:60ch;line-height:1.55;}

@media (max-width:980px){
  .svh-bento{grid-template-columns:1fr 1fr;}
  .svh-card.is-feature{grid-column:span 2;}
  .svh-points{grid-template-columns:1fr 1fr;}
}
@media (max-width:620px){
  .svh-bento{grid-template-columns:1fr;}
  .svh-card.is-feature{grid-column:span 1;}
  .svh-points{grid-template-columns:1fr;}
  .svh-help{padding:22px;}
}
</style>
<main>
<header class="svc-hero">
  <div class="orb orb1"></div><div class="orb orb2"></div>
  <div class="svc-hero-inner reveal" style="max-width:860px;">
    <div class="svc-eyebrow"><i class="fa-solid fa-stethoscope"></i> Roles we staff</div>
    <h1 class="svc-h1">Every role your practice needs, <em>handled by a teammate</em></h1>
    <p class="svc-lead">HIPAA-compliant medical and dental virtual assistants, matched to your software and your time zone. Pick the role you&rsquo;re missing &mdash; or book a quick audit and we&rsquo;ll tell you what to delegate first.</p>
    <div class="svh-trust">
      <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> HIPAA Compliant</div>
      <div class="trust-item"><i class="fa-solid fa-bolt"></i> Live in 1&ndash;2 Weeks</div>
      <div class="trust-item"><i class="fa-solid fa-star"></i> 4.9&#9733; Avg Google Rating</div>
      <div class="trust-item"><i class="fa-solid fa-rotate"></i> 30-Day Right-Fit Promise</div>
    </div>
    <div class="svc-cta-row" style="margin-top:26px;">
      <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#cta-buyers-checklist" class="btn-glass" data-cta-intent="buyers-checklist">Get the HIPAA VA buyer&rsquo;s checklist <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>
</header>

<section class="sec" style="padding-top:54px;">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-user-doctor"></i> Medical practices</div>
    <div class="svh-cat"><h2 class="svc-h2" style="margin-bottom:0;">Medical virtual assistants</h2><span class="svc-count"><?= count($medical) ?> roles</span></div>
  </div>
  <div class="svh-bento"><?php foreach ($medical as $s) { $card($s); } ?></div>

  <div class="reveal" style="margin-top:58px;">
    <div class="sec-lbl"><i class="fa-solid fa-tooth"></i> Dental practices</div>
    <div class="svh-cat"><h2 class="svc-h2" style="margin-bottom:0;">Dental virtual assistants</h2><span class="svc-count"><?= count($dental) ?> roles</span></div>
  </div>
  <div class="svh-bento"><?php foreach ($dental as $s) { $card($s); } ?></div>

  <div class="svh-help reveal" style="margin-top:58px;">
    <div class="svh-help-t">
      <h3>Not sure which role to start with?</h3>
      <p>Most practices begin with whatever&rsquo;s leaking time or money first &mdash; usually a biller or a front-desk receptionist. Book a 20-minute audit and we&rsquo;ll tell you exactly what to delegate first.</p>
    </div>
    <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit" style="flex:none;">Book my practice audit <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</section>

<?php include __DIR__ . '/../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
