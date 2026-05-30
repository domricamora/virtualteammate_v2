<?php
/**
 * Public "Virtual Teammates" talent directory — lead-generation funnel.
 *
 * Reads real VT data from the portal SQLite DB (same source talent-photo.php
 * uses). Anonymous visitors see a teaser only and are funnelled into the
 * on-page lead form. Logged-in portal users (vtportal session) see the full
 * profile (intro video, résumé, scores) in the modal.
 *
 * PRIVACY: full names, video/résumé URLs and contact data are emitted ONLY
 * when $isMember is true. Anonymous output contains no PII.
 */
declare(strict_types=1);

/* ── Member detection (read-only; only if a portal cookie is already set) ── */
$isMember = false;
if (!empty($_COOKIE['vtportal'])) {
    @ini_set('session.use_strict_mode', '1');
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.use_only_cookies', '1');
    @ini_set('session.cookie_samesite', 'Lax');
    if (session_status() === PHP_SESSION_NONE) {
        session_name('vtportal');
        @session_start();
    }
}

/* ── Load talent from the portal DB (graceful no-op if absent) ── */
$dbPath = __DIR__ . '/../data/portal.sqlite';
$rows   = [];
$pdo    = null;
if (is_file($dbPath)) {
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rows = $pdo->query(
            "SELECT u.id, u.first_name, u.last_name, u.country, u.role,
                    p.department, p.role_title, p.primary_skills, p.experience_years,
                    p.predictive_index, p.quiz_tier, p.engagement_score,
                    p.english_level, p.iq_band, p.technical_band, p.disc_profile,
                    p.personality_profile, p.ci_role, p.hipaa_certified,
                    p.summary, p.experience_text, p.video_url, p.resume_url
             FROM vt_profiles p
             JOIN users u ON u.id = p.user_id
             WHERE u.role IN ('vt_hired','vt_onpool') AND u.active = 1
               AND u.email NOT LIKE 'demo-%'
             ORDER BY p.department, u.first_name"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $_) {
        $rows = [];
    }
}

/* Validate the session user is real + active before unlocking full profiles. */
if (!empty($_SESSION['uid']) && $pdo instanceof PDO) {
    try {
        $chk = $pdo->prepare('SELECT 1 FROM users WHERE id = :u AND active = 1');
        $chk->execute([':u' => (int) $_SESSION['uid']]);
        $isMember = (bool) $chk->fetchColumn();
    } catch (Throwable $_) { $isMember = false; }
}

/* ── Helpers ── */
$h = static fn($s): string => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
/** Split a free-text primary_skills blob into individual skills. */
$splitSkills = static function (string $s): array {
    $parts = preg_split('/[\r\n;,|•·]+/u', $s) ?: [];
    $out = [];
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p !== '') { $out[$p] = true; }
    }
    return array_keys($out);
};
/** Build an absolute, member-only media URL from the stored value. */
$mediaUrl = static function (string $u): string {
    $u = trim($u);
    if ($u === '') { return ''; }
    if (preg_match('#^https?://#i', $u)) { return $u; }
    if (str_starts_with($u, 'index.php?p=media')) { return '/portal/' . $u; }
    return $u;
};

/* ── Shape the data: always-public teaser + member-only full payload ── */
$vts        = [];
$deptSkills = [];          // department => [skill, ...] for the dependent filter
foreach ($rows as $r) {
    $first   = trim((string) $r['first_name']);
    $lastIni = ($ln = trim((string) $r['last_name'])) !== '' ? mb_strtoupper(mb_substr($ln, 0, 1)) . '.' : '';
    $dept    = trim((string) $r['department']);
    $role    = trim((string) $r['role_title']);
    $skills  = $splitSkills((string) ($r['primary_skills'] ?? ''));
    $isHired = $r['role'] === 'vt_hired';

    if ($dept !== '') {
        $deptSkills[$dept] = $deptSkills[$dept] ?? [];
        foreach ($skills as $sk) { $deptSkills[$dept][$sk] = true; }
    }

    $scores = array_values(array_filter([
        trim((string) ($r['predictive_index'] ?? '')),
        trim((string) ($r['quiz_tier'] ?? '')),
        trim((string) ($r['engagement_score'] ?? '')),
    ], static fn($v) => $v !== ''));

    $card = [
        'id'         => (int) $r['id'],
        'public_name'=> trim($first . ' ' . $lastIni) ?: 'Virtual Teammate',
        'dept'       => $dept,
        'role'       => $role ?: ($dept ?: 'Virtual Assistant'),
        'country'    => trim((string) $r['country']),
        'years'      => (int) ($r['experience_years'] ?? 0),
        'scores'     => $scores,
        'skills'     => $skills,
        'status'     => $isHired ? 'Engaged' : 'Available',
        'is_hired'   => $isHired,
    ];

    // Full payload (PII / media) — ONLY for authenticated members.
    if ($isMember) {
        $card['full'] = [
            'name'      => trim($first . ' ' . $ln) ?: $card['public_name'],
            'videoUrl'  => $mediaUrl((string) ($r['video_url'] ?? '')),
            'resumeUrl' => $mediaUrl((string) ($r['resume_url'] ?? '')),
            'summary'   => trim((string) ($r['summary'] ?? '')) ?: trim((string) ($r['experience_text'] ?? '')),
            'english'   => trim((string) ($r['english_level'] ?? '')),
            'iq'        => trim((string) ($r['iq_band'] ?? '')),
            'technical' => trim((string) ($r['technical_band'] ?? '')),
            'disc'      => trim((string) ($r['disc_profile'] ?? '')),
            'persona'   => trim((string) ($r['personality_profile'] ?? '')),
            'ci'        => trim((string) ($r['ci_role'] ?? '')),
            'hipaa'     => trim((string) ($r['hipaa_certified'] ?? '')),
        ];
    }
    $vts[] = $card;
}
// Sort skills within each department, then sort the department list.
foreach ($deptSkills as $d => $set) { $deptSkills[$d] = array_keys($set); sort($deptSkills[$d]); }
ksort($deptSkills);
$totalVts = count($vts);

/* ── SEO header vars ── */
$page_title       = 'Virtual Teammates — Hire Vetted Medical, Dental & Business Virtual Assistants';
$page_description = 'Browse Virtual Teammate\'s bench of HIPAA-certified, pre-vetted virtual assistants for medical, dental and business teams. Filter by department and skill, then get matched in days.';
$og_title         = 'Meet Our Virtual Teammates — Vetted VAs Ready to Join Your Team';
$og_description   = 'Search and filter our roster of HIPAA-certified medical, dental and business virtual assistants. See skills, experience and credentials, then book a free matching call.';
$canonical        = 'https://virtualteammate.com/virtual-teammates/';
$home_base        = '../';
$breadcrumbs      = [
    ['name' => 'Home',              'url' => '/'],
    ['name' => 'Virtual Teammates', 'url' => '/virtual-teammates/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';

/* JSON-LD: CollectionPage + ItemList (no PII) — cap to keep it lean. */
$ld = [];
foreach (array_slice($vts, 0, 25) as $i => $v) {
    $ld[] = [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'item'     => array_filter([
            '@type'       => 'Person',
            'jobTitle'    => $v['role'],
            'description' => $v['dept'] !== '' ? ($v['dept'] . ' Virtual Assistant') : 'Virtual Assistant',
            'knowsAbout'  => array_slice($v['skills'], 0, 8) ?: null,
        ]),
    ];
}
?>
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'CollectionPage',
            '@id'         => 'https://virtualteammate.com/virtual-teammates/#page',
            'url'         => 'https://virtualteammate.com/virtual-teammates/',
            'name'        => 'Virtual Teammates — Vetted Medical, Dental & Business Virtual Assistants',
            'description' => $page_description,
            'isPartOf'    => ['@id' => 'https://virtualteammate.com/#website'],
            'about'       => ['@id' => 'https://virtualteammate.com/#org'],
        ],
        ['@type' => 'ItemList', 'name' => 'Virtual Teammate talent roster', 'numberOfItems' => $totalVts, 'itemListElement' => $ld],
        [
            '@type'      => 'FAQPage',
            'mainEntity' => [
                ['@type' => 'Question', 'name' => 'How much does a virtual assistant cost?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Virtual Teammate uses transparent flat-rate pricing — typically 60–78% less than an equivalent in-house hire once you factor in salary, benefits, payroll tax and overhead. Full-time VAs start at $1,625/month.']],
                ['@type' => 'Question', 'name' => 'Are your virtual assistants HIPAA certified?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes. Every healthcare and dental virtual assistant completes HIPAA compliance training and certification before placement.']],
                ['@type' => 'Question', 'name' => 'How fast can I hire a virtual assistant?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Most clients receive a curated shortlist within days and complete onboarding within one to two weeks.']],
                ['@type' => 'Question', 'name' => 'Can I interview candidates before hiring?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Absolutely. We hand-match candidates to your specialty, software and time zone, and you interview the shortlist before deciding.']],
                ['@type' => 'Question', 'name' => 'Where are Virtual Teammate VAs based?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'We source talent from a global network spanning the Philippines, Latin America, Africa and South Asia, and match every VA to your US time zone.']],
                ['@type' => 'Question', 'name' => 'What if the virtual assistant is not the right fit?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Our 30-Day Right-Fit Promise means we will re-match you at no extra cost if your VA is not the right fit in the first 30 days.']],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
</script>

<main>
  <!-- ── HERO ── -->
  <section class="sec vtd-hero" aria-labelledby="vtd-h1">
    <div class="vtd-hero-inner reveal">
      <div class="vtd-hero-text">
        <div class="sec-lbl"><i class="fa-solid fa-users"></i> Our Virtual Teammates</div>
        <h1 class="sec-h2" id="vtd-h1" style="max-width:18ch;">Hire a Vetted Medical, Dental &amp; Business Virtual Assistant</h1>
        <p class="vtd-lede">
          Meet the Virtual Teammate bench — HIPAA-certified, pre-screened virtual assistants ready to support
          your practice or business. Browse real teammates by department and skill, then get matched to the
          right candidate in days. Every VA is interviewed, background-checked and time-zone aligned to your team.
        </p>
        <div class="vtd-hero-cta">
          <a href="#vtd-match" class="btn-primary">Get Matched Free <i class="fa-solid fa-arrow-right"></i></a>
          <span class="vtd-hero-stat"><strong><?= (int) $totalVts ?>+</strong> teammates on the bench</span>
        </div>
      </div>
      <?php $heroIds = array_slice(array_column($vts, 'id'), 0, 6); if ($heroIds): ?>
      <div class="vtd-hero-visual" aria-hidden="true">
        <div class="vtd-hero-collage">
          <?php foreach ($heroIds as $hi): ?>
            <span class="vtd-hero-pic"><img src="<?= $home_base ?>talent-photo.php?id=<?= (int) $hi ?>" alt="" loading="lazy" width="150" height="150"></span>
          <?php endforeach; ?>
        </div>
        <span class="vtd-hero-badge"><i class="fa-solid fa-circle-check"></i> <?= (int) $totalVts ?>+ vetted teammates</span>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ── LEAD CAPTURE (primary funnel) ── -->
  <section class="sec vtd-match" id="vtd-match" aria-labelledby="vtd-match-h">
    <div class="vtd-match-card reveal">
      <div class="vtd-match-l">
        <div class="sec-lbl"><i class="fa-solid fa-wand-magic-sparkles"></i> Free Matching</div>
        <h2 class="sec-h2" id="vtd-match-h" style="margin-bottom:10px;">Get matched with your Virtual Teammate</h2>
        <p class="vtd-lede" style="margin:0 0 16px;">
          Tell us what you need and we'll hand-pick candidates by specialty, software and time zone — free, no obligation.
          You'll also unlock full profiles with intro videos and résumés.
        </p>
        <ul class="vtd-match-points">
          <li><i class="fa-solid fa-check"></i> Curated shortlist within days</li>
          <li><i class="fa-solid fa-check"></i> Interview before you decide</li>
          <li><i class="fa-solid fa-check"></i> 30-Day Right-Fit Promise</li>
        </ul>
      </div>
      <form class="vtd-form" id="vtdLeadForm" novalidate>
        <div class="vtd-form-row">
          <input class="vtd-field" type="text" name="first_name" placeholder="First name" required autocomplete="given-name">
          <input class="vtd-field" type="text" name="last_name" placeholder="Last name" autocomplete="family-name">
        </div>
        <input class="vtd-field" type="email" name="email" placeholder="Work email" required autocomplete="email">
        <div class="vtd-form-row">
          <input class="vtd-field" type="tel" name="phone" placeholder="Phone (optional)" autocomplete="tel">
          <input class="vtd-field" type="text" name="role" placeholder="Practice / company" autocomplete="organization">
        </div>
        <textarea class="vtd-field" name="message" rows="2" placeholder="What do you need help with?"></textarea>
        <input type="hidden" name="vt_id" id="vtdLeadVtId" value="">
        <input type="hidden" name="vt_interest" id="vtdLeadVtName" value="">
        <input type="hidden" name="source" value="virtual-teammates">
        <input type="hidden" name="form" value="virtual-teammates">
        <!-- honeypot -->
        <input type="text" name="company_site" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
        <button type="submit" class="btn-primary vtd-form-submit">Request my shortlist <i class="fa-solid fa-arrow-right"></i></button>
        <p class="vtd-form-note" id="vtdLeadNote" role="status" aria-live="polite">We reply within 1 business day. No spam, ever.</p>
      </form>
    </div>
  </section>

  <!-- ── DIRECTORY ── -->
  <section class="sec vtd-directory" id="roster" aria-labelledby="vtd-roster-h">
    <div class="reveal">
      <div class="sec-lbl"><i class="fa-solid fa-id-badge"></i> The Bench</div>
      <h2 class="sec-h2" id="vtd-roster-h">Browse our Virtual Teammates</h2>
    </div>

    <!-- Filters -->
    <div class="vtd-filters reveal" role="search">
      <div class="vtd-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" id="vtdSearch" placeholder="Search by name, role or skill…" aria-label="Search virtual teammates">
      </div>
      <select id="vtdDept" class="vtd-select" aria-label="Filter by department">
        <option value="">All departments</option>
        <?php foreach (array_keys($deptSkills) as $d): ?>
          <option value="<?= $h($d) ?>"><?= $h($d) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="vtdSkill" class="vtd-select" aria-label="Filter by skill" disabled>
        <option value="">All skills</option>
      </select>
      <button type="button" id="vtdReset" class="vtd-reset">Reset</button>
    </div>

    <p class="vtd-count" id="vtdCount" aria-live="polite"></p>

    <?php if ($totalVts === 0): ?>
      <div class="prof-empty card">
        <i class="fa-solid fa-user-doctor" style="color:var(--gold);font-size:24px;margin-bottom:10px;"></i>
        <p>Our bench is reviewed and matched manually for every engagement. <a href="#vtd-match">Request a shortlist</a> tailored to your specialty, software and time-zone preferences.</p>
      </div>
    <?php else: ?>
      <div class="vtd-grid" id="vtdGrid">
        <?php foreach ($vts as $i => $v):
            $searchBlob = strtolower(implode(' ', array_filter([
                $v['public_name'], $v['role'], $v['dept'], $v['country'],
                implode(' ', $v['skills']), implode(' ', $v['scores']),
            ])));
            $delay = 'd' . (($i % 3) + 1);
        ?>
        <article class="vtd-card reveal <?= $delay ?>"
                 data-id="<?= (int) $v['id'] ?>"
                 data-dept="<?= $h($v['dept']) ?>"
                 data-skills="<?= $h(strtolower(implode('||', $v['skills']))) ?>"
                 data-search="<?= $h($searchBlob) ?>"
                 data-name="<?= $h($v['public_name']) ?>"
                 data-role="<?= $h($v['role']) ?>"
                 data-country="<?= $h($v['country']) ?>"
                 data-status="<?= $h($v['status']) ?>"
                 data-hired="<?= $v['is_hired'] ? '1' : '0' ?>"
                 data-scores="<?= $h(implode('||', $v['scores'])) ?>">
          <span class="vtd-status <?= $v['is_hired'] ? 'is-engaged' : 'is-avail' ?>"><?= $h($v['status']) ?></span>
          <div class="vtd-photo">
            <img src="<?= $home_base ?>talent-photo.php?id=<?= (int) $v['id'] ?>" alt="<?= $h($v['public_name'] . ' — ' . $v['role']) ?>" loading="lazy" width="96" height="96">
          </div>
          <div class="vtd-name"><?= $h($v['public_name']) ?></div>
          <?php if ($v['dept'] !== ''): ?><div class="vtd-dept"><?= $h($v['dept']) ?></div><?php endif; ?>
          <div class="vtd-role"><?= $h($v['role']) ?></div>
          <?php if ($v['country'] !== ''): ?><div class="vtd-loc"><i class="fa-solid fa-location-dot"></i> <?= $h($v['country']) ?></div><?php endif; ?>
          <?php if ($v['scores']): ?>
            <div class="vtd-scores">
              <?php foreach ($v['scores'] as $sc): ?><span class="vtd-score"><?= $h($sc) ?></span><?php endforeach; ?>
            </div>
          <?php endif; ?>
          <?php if ($v['skills']): ?>
            <div class="vtd-skills-h">Skills</div>
            <div class="vtd-skills">
              <?php foreach (array_slice($v['skills'], 0, 5) as $sk): ?><span class="vtd-skill"><?= $h($sk) ?></span><?php endforeach; ?>
            </div>
          <?php endif; ?>
          <button type="button" class="vtd-view" data-view="<?= (int) $v['id'] ?>">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> View profile
          </button>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="vtd-noresults" id="vtdNoResults" hidden>No teammates match your filters. <a href="#vtd-match">Tell us what you need</a> and we'll find them.</p>
      <div class="vtd-more-wrap"><button type="button" id="vtdMore" class="btn-primary vtd-more">Load more teammates</button></div>
    <?php endif; ?>
  </section>

  <div class="divider"></div>

  <!-- ── SEO CONTENT ── -->
  <section class="sec vtd-seo" aria-labelledby="vtd-why-h">
    <div class="reveal">
      <div class="sec-lbl"><i class="fa-solid fa-shield-heart"></i> Why Virtual Teammate</div>
      <h2 class="sec-h2" id="vtd-why-h">Virtual assistants who are vetted, certified and ready</h2>
      <p class="vtd-lede">
        Every Virtual Teammate is hand-screened for skills, communication and reliability before they ever reach
        your shortlist. Healthcare and dental VAs are HIPAA-certified; business and administrative VAs are tested
        on the exact tools your team runs. You get a teammate, not a temp — matched to your time zone and ramped
        with a dedicated success manager.
      </p>
      <div class="vtd-why-grid">
        <div class="vtd-why-card"><i class="fa-solid fa-user-shield"></i><h3>HIPAA-certified</h3><p>Healthcare &amp; dental VAs complete HIPAA compliance training before placement.</p></div>
        <div class="vtd-why-card"><i class="fa-solid fa-clipboard-check"></i><h3>Rigorously vetted</h3><p>Skills tests, background checks and live interviews — only the top few percent make the bench.</p></div>
        <div class="vtd-why-card"><i class="fa-solid fa-clock"></i><h3>Your time zone</h3><p>Global talent matched to overlap your working hours, so work happens in real time.</p></div>
        <div class="vtd-why-card"><i class="fa-solid fa-handshake-angle"></i><h3>30-day right-fit</h3><p>Not the right match? We re-match at no extra cost in the first 30 days. <a href="<?= $home_base ?>guarantee/">See the promise</a>.</p></div>
      </div>
    </div>
  </section>

  <section class="sec vtd-seo" aria-labelledby="vtd-spec-h">
    <div class="reveal">
      <h2 class="sec-h2" id="vtd-spec-h">Virtual assistants for every team</h2>
      <div class="vtd-spec-grid">
        <div class="vtd-spec">
          <h3><i class="fa-solid fa-stethoscope"></i> Medical virtual assistants</h3>
          <p>Medical billing, scribing, patient scheduling, insurance verification and prior authorization — HIPAA-certified support that keeps your practice running. <a href="<?= $home_base ?>services/medical-administrative-support/">Explore medical VAs</a>.</p>
        </div>
        <div class="vtd-spec">
          <h3><i class="fa-solid fa-tooth"></i> Dental virtual assistants</h3>
          <p>Dental front-desk, billing, insurance and treatment-plan coordination so your chairside team can focus on patients. <a href="<?= $home_base ?>services/dental-administrative-support/">Explore dental VAs</a>.</p>
        </div>
        <div class="vtd-spec">
          <h3><i class="fa-solid fa-briefcase"></i> Business &amp; admin virtual assistants</h3>
          <p>Executive support, CRM and Salesforce administration, data entry, calendar and email management for growing teams. <a href="<?= $home_base ?>business/">Explore business VAs</a>.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="sec vtd-seo" aria-labelledby="vtd-how-h">
    <div class="reveal">
      <div class="sec-lbl"><i class="fa-solid fa-route"></i> How matching works</div>
      <h2 class="sec-h2" id="vtd-how-h">From search to teammate in three steps</h2>
      <ol class="vtd-steps">
        <li><span class="vtd-step-n">1</span><h3>Tell us your needs</h3><p>Share your specialty, software and the hours you need covered.</p></li>
        <li><span class="vtd-step-n">2</span><h3>Meet your shortlist</h3><p>We hand-match vetted candidates and you interview the best fits.</p></li>
        <li><span class="vtd-step-n">3</span><h3>Onboard your teammate</h3><p>Your VA ramps with a dedicated success manager — backed by our 30-day promise.</p></li>
      </ol>
      <div style="text-align:center;margin-top:24px;"><a href="#vtd-match" class="btn-primary">Start matching <i class="fa-solid fa-arrow-right"></i></a></div>
    </div>
  </section>

  <!-- ── FAQ (matches FAQPage JSON-LD above) ── -->
  <section class="sec vtd-seo" id="vtd-faq" aria-labelledby="vtd-faq-h">
    <div class="reveal">
      <div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div>
      <h2 class="sec-h2" id="vtd-faq-h">Virtual assistant hiring questions</h2>
    </div>
    <div class="vtd-faq-grid">
      <div class="vtd-faq"><h3>How much does a virtual assistant cost?</h3><p>Transparent flat-rate pricing — typically 60–78% less than an in-house hire once salary, benefits, payroll tax and overhead are factored in. Full-time VAs start at $1,625/month.</p></div>
      <div class="vtd-faq"><h3>Are your virtual assistants HIPAA certified?</h3><p>Yes. Every healthcare and dental VA completes HIPAA compliance training and certification before placement.</p></div>
      <div class="vtd-faq"><h3>How fast can I hire?</h3><p>Most clients get a curated shortlist within days and finish onboarding within one to two weeks.</p></div>
      <div class="vtd-faq"><h3>Can I interview candidates first?</h3><p>Always. We hand-match candidates to your specialty, software and time zone, and you interview the shortlist before deciding.</p></div>
      <div class="vtd-faq"><h3>Where are your VAs based?</h3><p>We source globally — the Philippines, Latin America, Africa and South Asia — and match every VA to your US time zone.</p></div>
      <div class="vtd-faq"><h3>What if it's not the right fit?</h3><p>Our 30-Day Right-Fit Promise re-matches you at no extra cost in the first 30 days.</p></div>
    </div>
  </section>

  <!-- ── CLOSING CTA ── -->
  <section class="sec vtd-closing" aria-label="Get matched">
    <div class="vtd-closing-card reveal">
      <h2 class="sec-h2" style="margin-bottom:8px;">Ready to meet your Virtual Teammate?</h2>
      <p class="vtd-lede" style="margin:0 auto 18px;max-width:60ch;">Get a free, no-obligation shortlist matched to your specialty and time zone.</p>
      <a href="#vtd-match" class="btn-primary">Get matched free <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </section>
</main>

<!-- ── PROFILE MODAL ── -->
<div class="vtd-modal" id="vtdModal" hidden>
  <div class="vtd-modal-backdrop" data-close></div>
  <div class="vtd-modal-card" role="dialog" aria-modal="true" aria-labelledby="vtdModalName" tabindex="-1">
    <button type="button" class="vtd-modal-x" data-close aria-label="Close">&times;</button>
    <div class="vtd-modal-body" id="vtdModalBody"><!-- filled by JS --></div>
  </div>
</div>

<script>
(function(){
  var IS_MEMBER = <?= $isMember ? 'true' : 'false' ?>;
  var DEPT_SKILLS = <?= json_encode($deptSkills, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  var FULL = <?= $isMember ? json_encode(array_column($vts, 'full', 'id'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '{}' ?>;
  var PAGE = 12;

  var grid    = document.getElementById('vtdGrid');
  if (!grid) return;
  var cards   = Array.prototype.slice.call(grid.querySelectorAll('.vtd-card'));
  var search  = document.getElementById('vtdSearch');
  var deptSel = document.getElementById('vtdDept');
  var skillSel= document.getElementById('vtdSkill');
  var resetBtn= document.getElementById('vtdReset');
  var moreBtn = document.getElementById('vtdMore');
  var countEl = document.getElementById('vtdCount');
  var noRes   = document.getElementById('vtdNoResults');
  var shown   = PAGE;

  function esc(s){ return (s==null?'':String(s)).replace(/[&<>"]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];}); }

  function matches(card){
    var q = (search.value||'').trim().toLowerCase();
    var d = deptSel.value, sk = (skillSel.value||'').toLowerCase();
    if (q && card.getAttribute('data-search').indexOf(q) === -1) return false;
    if (d && card.getAttribute('data-dept') !== d) return false;
    if (sk && ('||'+card.getAttribute('data-skills')+'||').indexOf('||'+sk+'||') === -1) return false;
    return true;
  }

  function apply(){
    var n = 0, visibleMatched = 0;
    cards.forEach(function(c){
      var m = matches(c);
      if (m){
        visibleMatched++;
        if (visibleMatched <= shown){ c.style.display=''; n++; }
        else { c.style.display='none'; }
      } else { c.style.display='none'; }
    });
    if (noRes) noRes.hidden = visibleMatched !== 0;
    if (moreBtn) moreBtn.style.display = visibleMatched > shown ? '' : 'none';
    if (countEl) countEl.textContent = visibleMatched + (visibleMatched===1?' teammate':' teammates');
  }

  // Dependent skill dropdown.
  function populateSkills(){
    var d = deptSel.value;
    skillSel.innerHTML = '<option value="">All skills</option>';
    var list = (d && DEPT_SKILLS[d]) ? DEPT_SKILLS[d] : [];
    list.forEach(function(s){ var o=document.createElement('option'); o.value=s; o.textContent=s; skillSel.appendChild(o); });
    skillSel.disabled = list.length === 0;
  }

  search.addEventListener('input', function(){ shown=PAGE; apply(); });
  deptSel.addEventListener('change', function(){ populateSkills(); shown=PAGE; apply(); });
  skillSel.addEventListener('change', function(){ shown=PAGE; apply(); });
  if (moreBtn) moreBtn.addEventListener('click', function(){ shown += PAGE; apply(); });
  if (resetBtn) resetBtn.addEventListener('click', function(){ search.value=''; deptSel.value=''; populateSkills(); shown=PAGE; apply(); });
  apply();

  // ── Modal ──
  var modal = document.getElementById('vtdModal');
  var body  = document.getElementById('vtdModalBody');
  var nameForm = document.getElementById('vtdLeadVtName');
  var idForm   = document.getElementById('vtdLeadVtId');
  var lastFocus = null;

  function openModal(card){
    var id = card.getAttribute('data-id');
    var name = card.getAttribute('data-name');
    var html = '';
    if (IS_MEMBER && FULL[id]){
      var f = FULL[id];
      html += '<h2 class="vtd-m-name" id="vtdModalName">'+esc(f.name)+'</h2>';
      html += '<div class="vtd-m-sub">'+esc(card.getAttribute('data-role'))+(card.getAttribute('data-country')?' · '+esc(card.getAttribute('data-country')):'')+'</div>';
      if (f.videoUrl){ html += '<div class="vtd-m-media"><div class="vtd-m-h"><i class="fa-solid fa-video"></i> Intro video</div><video controls preload="metadata" playsinline src="'+esc(f.videoUrl)+'"></video></div>'; }
      if (f.resumeUrl){ html += '<div class="vtd-m-media"><div class="vtd-m-h"><i class="fa-solid fa-file-pdf"></i> Résumé</div><embed src="'+esc(f.resumeUrl)+'#toolbar=1&navpanes=0" type="application/pdf"><div class="vtd-m-actions"><a class="vtd-view" href="'+esc(f.resumeUrl)+'" target="_blank" rel="noopener">Open résumé</a></div></div>'; }
      if (f.summary){ html += '<div class="vtd-m-sec"><h3>Summary</h3><p>'+esc(f.summary)+'</p></div>'; }
      var kv = [['English',f.english],['IQ band',f.iq],['Technical',f.technical],['DISC',f.disc],['Personality',f.persona],['CI role',f.ci],['HIPAA',f.hipaa]].filter(function(r){return r[1];});
      if (kv.length){ html += '<div class="vtd-m-sec"><h3>Assessment</h3><dl class="vtd-m-dl">'+kv.map(function(r){return '<dt>'+esc(r[0])+'</dt><dd>'+esc(r[1])+'</dd>';}).join('')+'</dl></div>'; }
    } else {
      // Teaser → funnel to lead form.
      html += '<h2 class="vtd-m-name" id="vtdModalName">'+esc(name)+'</h2>';
      html += '<div class="vtd-m-sub">'+esc(card.getAttribute('data-role'))+(card.getAttribute('data-country')?' · '+esc(card.getAttribute('data-country')):'')+'</div>';
      var scores = (card.getAttribute('data-scores')||'').split('||').filter(Boolean);
      if (scores.length){ html += '<div class="vtd-scores">'+scores.map(function(s){return '<span class="vtd-score">'+esc(s)+'</span>';}).join('')+'</div>'; }
      var skills = (card.getAttribute('data-skills')||'').split('||').filter(Boolean);
      if (skills.length){ html += '<div class="vtd-skills-h">Skills</div><div class="vtd-skills">'+skills.slice(0,8).map(function(s){return '<span class="vtd-skill">'+esc(s)+'</span>';}).join('')+'</div>'; }
      html += '<div class="vtd-m-lock"><i class="fa-solid fa-lock"></i> Intro video, full résumé and assessment scores are available to members.</div>';
      html += '<button type="button" class="btn-primary vtd-m-cta" data-match="'+esc(id)+'" data-matchname="'+esc(name)+'">Get matched with '+esc(name)+' <i class="fa-solid fa-arrow-right"></i></button>';
    }
    body.innerHTML = html;
    lastFocus = document.activeElement;
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
    modal.querySelector('.vtd-modal-card').focus();
  }
  function closeModal(){
    modal.hidden = true;
    document.body.style.overflow = '';
    if (lastFocus) lastFocus.focus();
  }

  grid.addEventListener('click', function(e){
    var btn = e.target.closest('[data-view]');
    if (!btn) return;
    openModal(btn.closest('.vtd-card'));
  });
  modal.addEventListener('click', function(e){
    if (e.target.closest('[data-close]')) { closeModal(); return; }
    var cta = e.target.closest('[data-match]');
    if (cta){
      if (idForm)   idForm.value = cta.getAttribute('data-match');
      if (nameForm) nameForm.value = cta.getAttribute('data-matchname');
      closeModal();
      var form = document.getElementById('vtd-match');
      if (form){ form.scrollIntoView({behavior:'smooth', block:'start'}); var fn=document.querySelector('#vtdLeadForm [name=first_name]'); if(fn) setTimeout(function(){fn.focus();},400); }
    }
  });
  document.addEventListener('keydown', function(e){ if (e.key==='Escape' && !modal.hidden) closeModal(); });

  // ── Lead form submit ──
  var leadForm = document.getElementById('vtdLeadForm');
  if (leadForm){
    leadForm.addEventListener('submit', function(e){
      e.preventDefault();
      var note = document.getElementById('vtdLeadNote');
      var btn  = leadForm.querySelector('.vtd-form-submit');
      if (btn){ btn.disabled = true; }
      fetch('<?= $home_base ?>lead.php', { method:'POST', body: new FormData(leadForm), credentials:'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(res){
          if (res && res.ok){
            leadForm.innerHTML = '<div class="vtd-thanks"><i class="fa-solid fa-circle-check"></i><h3>Thank you!</h3><p>We\'ve received your request and will reach out within 1 business day with your matched shortlist.</p></div>';
          } else {
            if (note){ note.textContent = (res && res.error) ? res.error : 'Something went wrong — please try again.'; note.classList.add('is-err'); }
            if (btn){ btn.disabled = false; }
          }
        })
        .catch(function(){ if (note){ note.textContent='Network error — please try again.'; note.classList.add('is-err'); } if (btn){ btn.disabled=false; } });
    });
  }
})();
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
