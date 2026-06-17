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
$isMember      = false;
$viewerRole    = '';
$viewerCid     = 0;     // clients.id when the viewer is a client
$excludeIds    = [];    // a client's own VTs (current + previous) — hidden from the roster
$pendingReqIds = [];    // VT ids this client already has a pending request for
$csrfToken     = '';
if (!empty($_COOKIE['vtportal'])) {
    @ini_set('session.use_strict_mode', '1');
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.use_only_cookies', '1');
    @ini_set('session.cookie_samesite', 'Lax');
    // Read the SAME session store the portal writes to (bootstrap uses
    // data/sessions when writable) so member detection works and the CSRF token
    // we mint below is the one request.php will verify. Mirror bootstrap exactly
    // — including the mkdir — so both entry points make the identical decision.
    $vtSessDir = __DIR__ . '/../data/sessions';
    if (!is_dir($vtSessDir)) { @mkdir($vtSessDir, 0700, true); }
    if (is_dir($vtSessDir) && is_writable($vtSessDir)) { @session_save_path($vtSessDir); }
    if (session_status() === PHP_SESSION_NONE) {
        session_name('vtportal');
        @session_start();
    }
}

/* ── Open the portal DB (graceful no-op if absent) ── */
$dbPath = __DIR__ . '/../data/portal.sqlite';
$rows   = [];
$pdo    = null;
if (is_file($dbPath)) {
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Throwable $_) { $pdo = null; }
}

/* Validate the session user; unlock full profiles + (for clients) the request flow. */
if (!empty($_SESSION['uid']) && $pdo instanceof PDO) {
    try {
        $me = $pdo->prepare('SELECT id, role FROM users WHERE id = :u AND active = 1');
        $me->execute([':u' => (int) $_SESSION['uid']]);
        $meRow = $me->fetch(PDO::FETCH_ASSOC);
        if ($meRow) {
            $isMember   = true;
            $viewerRole = (string) $meRow['role'];
            if ($viewerRole === 'client') {
                $cs = $pdo->prepare('SELECT id FROM clients WHERE user_id = :u LIMIT 1');
                $cs->execute([':u' => (int) $meRow['id']]);
                $viewerCid = (int) ($cs->fetchColumn() ?: 0);
                if ($viewerCid > 0) {
                    // Hide VTs already on this client's team — current OR previous.
                    $ex = $pdo->prepare('SELECT DISTINCT vt_user_id FROM client_vts WHERE client_id = :c');
                    $ex->execute([':c' => $viewerCid]);
                    $excludeIds = array_map('intval', array_column($ex->fetchAll(PDO::FETCH_ASSOC), 'vt_user_id'));
                    // Pending requests → show a "Requested" state instead of a button.
                    try {
                        $pr = $pdo->prepare("SELECT DISTINCT vt_user_id FROM vt_requests WHERE client_id = :c AND status = 'pending'");
                        $pr->execute([':c' => $viewerCid]);
                        $pendingReqIds = array_map('intval', array_column($pr->fetchAll(PDO::FETCH_ASSOC), 'vt_user_id'));
                    } catch (Throwable $_) { $pendingReqIds = []; }
                    // Mint a CSRF token into the shared session for the request endpoint.
                    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
                    $csrfToken = (string) $_SESSION['csrf'];
                }
            }
        }
    } catch (Throwable $_) { $isMember = false; }
}
$canRequest = ($viewerRole === 'client' && $viewerCid > 0);

/* ── Load talent from the portal DB. A client never sees their own (current or
 *    previous) teammates — only the rest of the available bench. ── */
if ($pdo instanceof PDO) {
    try {
        $exclSql = $excludeIds ? (' AND u.id NOT IN (' . implode(',', $excludeIds) . ')') : '';
        $rows = $pdo->query(
            "SELECT u.id, u.first_name, u.last_name, u.country, u.role, u.photo_url,
                    p.department, p.role_title, p.primary_skills, p.experience_years,
                    p.ehr_software, p.predictive_index, p.quiz_tier, p.engagement_score,
                    p.english_level, p.iq_band, p.technical_band, p.disc_profile,
                    p.personality_profile, p.ci_role, p.hipaa_certified,
                    p.summary, p.experience_text, p.video_url, p.resume_url
             FROM vt_profiles p
             JOIN users u ON u.id = p.user_id
             WHERE u.role IN ('vt_hired','vt_onpool') AND u.active = 1
               AND u.email NOT LIKE 'demo-%'
               AND p.department IN ('Medical','Dental'){$exclSql}
             ORDER BY p.department, u.first_name"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $_) {
        $rows = [];
    }
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
/** Build a member-only média URL. Résumé/video stream from the gated, ANY-member
 *  endpoint ../talent-media.php (so clients can preview any VT on the bench, not
 *  only ones they're engaged with — unlike the portal's per-engagement endpoint).
 *  External (YouTube/Vimeo) links pass through for the modal's iframe embed. */
$mediaUrl = static function (string $u): string {
    $u = trim($u);
    if ($u === '') { return ''; }
    if (preg_match('#^https?://#i', $u)) { return $u; }
    if (preg_match('#index\.php\?p=media&e=vt&id=(\d+)&k=(\w+)#', $u, $m)) {
        return '../talent-media.php?id=' . $m[1] . '&k=' . $m[2];
    }
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

    // Talent-card content (mirrors the homepage "Meet the Team" cards).
    $hay      = mb_strtolower($dept . ' ' . $role);
    $isDental = str_contains($hay, 'dental');
    $isClin   = $isDental || str_contains($hay, 'medical');
    $cap = trim((string) ($r['summary'] ?? ''));
    if ($cap !== '') {
        $cap = preg_split('/(?<=[.!?])\s+/', $cap)[0];
    } elseif ($skills) {
        $cap = implode(', ', array_slice($skills, 0, 4));
    } else {
        $cap = 'Ready to support your team from day one.';
    }
    $cap = function_exists('mb_strimwidth')
        ? mb_strimwidth($cap, 0, 96, '…')
        : (strlen($cap) > 96 ? substr($cap, 0, 95) . '…' : $cap);

    $card = [
        'id'         => (int) $r['id'],
        'public_name'=> trim($first . ' ' . $lastIni) ?: 'Virtual Teammate',
        'dept'       => $dept,
        'role'       => $role ?: ($dept ?: 'Virtual Assistant'),
        'country'    => trim((string) $r['country']),
        'years'      => (int) ($r['experience_years'] ?? 0),
        'ehr'        => trim((string) ($r['ehr_software'] ?? '')),
        'hipaa'      => !empty($r['hipaa_certified']) || $isClin,
        'is_dental'  => $isDental,
        'tag'        => $dept !== '' ? $dept : ($isDental ? 'Dental VA' : ($isClin ? 'Medical VA' : 'Virtual Assistant')),
        'cap'        => $cap,
        'scores'     => $scores,
        'skills'     => $skills,
        'status'     => $isHired ? 'Engaged' : 'Available',
        'is_hired'   => $isHired,
        'has_photo'  => trim((string) ($r['photo_url'] ?? '')) !== '',
    ];

    // Full CV payload (PII / media) — ONLY for authenticated members.
    if ($isMember) {
        $card['full'] = [
            'name'       => trim($first . ' ' . $ln) ?: $card['public_name'],
            'role'       => $card['role'],
            'dept'       => $dept,
            'country'    => $card['country'],
            'years'      => $card['years'],
            'status'     => $card['status'],
            'is_hired'   => $isHired,
            'photo'      => '../talent-photo.php?id=' . $card['id'],   // full-size, site-hosted
            'scores'     => $scores,
            'skills'     => $skills,
            'summary'    => trim((string) ($r['summary'] ?? '')),
            'experience' => trim((string) ($r['experience_text'] ?? '')),
            'videoUrl'   => $mediaUrl((string) ($r['video_url'] ?? '')),
            'resumeUrl'  => $mediaUrl((string) ($r['resume_url'] ?? '')),
            'english'    => trim((string) ($r['english_level'] ?? '')),
            'iq'         => trim((string) ($r['iq_band'] ?? '')),
            'technical'  => trim((string) ($r['technical_band'] ?? '')),
            'disc'       => trim((string) ($r['disc_profile'] ?? '')),
            'persona'    => trim((string) ($r['personality_profile'] ?? '')),
            'ci'         => trim((string) ($r['ci_role'] ?? '')),
            'hipaa'      => trim((string) ($r['hipaa_certified'] ?? '')),
        ];
    }
    $vts[] = $card;
}
// Sort skills within each department, then sort the department list.
foreach ($deptSkills as $d => $set) { $deptSkills[$d] = array_keys($set); sort($deptSkills[$d]); }
ksort($deptSkills);
$totalVts = count($vts);

/* ── SEO header vars ── */
$page_title       = 'Virtual Teammates: Hire Vetted Medical & Dental Virtual Assistants';
$page_description = 'Browse Virtual Teammate\'s bench of HIPAA-certified, pre-vetted virtual assistants for medical, dental and business teams. Filter by department and skill, then get matched in days.';
$og_title         = 'Meet Our Virtual Teammates: Vetted VAs Ready to Join Your Team';
$og_description   = 'Search and filter our roster of HIPAA-certified medical, dental and business virtual assistants. See skills, experience and credentials, then book a value-matching call.';
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
            'name'        => 'Virtual Teammates: Vetted Medical & Dental Virtual Assistants',
            'description' => $page_description,
            'isPartOf'    => ['@id' => 'https://virtualteammate.com/#website'],
            'about'       => ['@id' => 'https://virtualteammate.com/#org'],
        ],
        ['@type' => 'ItemList', 'name' => 'Virtual Teammate talent roster', 'numberOfItems' => $totalVts, 'itemListElement' => $ld],
        [
            '@type'      => 'FAQPage',
            'mainEntity' => [
                ['@type' => 'Question', 'name' => 'How much does a virtual assistant cost?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Virtual Teammate uses transparent flat-rate pricing: typically 60–73% less than an equivalent in-house hire once you factor in salary, benefits, payroll tax and overhead. Full-time VAs start at $1,625/month.']],
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
        <h1 class="sec-h2" id="vtd-h1" style="max-width:18ch;">Hire a vetted medical &amp; dental virtual assistant</h1>
        <p class="vtd-lede">
          Meet the Virtual Teammate bench: HIPAA-certified, pre-screened virtual assistants ready to support
          your practice or business. Browse real teammates by department and skill, then get matched to the
          right candidate in days. Every VA is interviewed, background-checked and time-zone aligned to your team.
        </p>
        <div class="vtd-hero-cta">
          <a href="#vtd-match" class="btn-primary">Get Your Value Match <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>
      <?php
        // Fixed, curated collage — medical & dental teammates in white coats
        // (sourced from Unsplash, converted to WebP). No longer randomized.
        $heroPics = ['med-1', 'med-2', 'med-3', 'med-4', 'med-5', 'med-6'];
      ?>
      <div class="vtd-hero-visual" aria-hidden="true">
        <div class="vtd-hero-collage">
          <?php foreach ($heroPics as $pic): ?>
            <span class="vtd-hero-pic"><img src="<?= $home_base ?>images/talent-hero/<?= $pic ?>.webp" alt="" loading="lazy" width="150" height="150"></span>
          <?php endforeach; ?>
        </div>
        <span class="vtd-hero-badge"><i class="fa-solid fa-circle-check"></i> HIPAA certified teammates</span>
      </div>
    </div>
  </section>

  <!-- ── LEAD CAPTURE (primary funnel) ── -->
  <section class="sec vtd-match" id="vtd-match" aria-labelledby="vtd-match-h">
    <div class="vtd-match-card reveal">
      <div class="vtd-match-l">
        <div class="sec-lbl"><i class="fa-solid fa-wand-magic-sparkles"></i> Value Matching</div>
        <h2 class="sec-h2" id="vtd-match-h" style="margin-bottom:10px;">Get matched with your Virtual Teammate</h2>
        <p class="vtd-lede" style="margin:0 0 16px;">
          Tell us what you need and we'll hand-pick candidates by specialty, software and time zone, value-first, no obligation.
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
        <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
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
          <?php $vIni = $h(strtoupper(mb_substr($v['public_name'], 0, 1)) ?: '?'); ?>
          <div class="vtd-photo">
            <?php if (!empty($v['has_photo'])): ?>
              <img src="<?= $home_base ?>talent-photo.php?id=<?= (int) $v['id'] ?>&amp;thumb=1" alt="<?= $h($v['public_name'] . ', ' . $v['role']) ?>" decoding="async" width="96" height="96"
                   onerror="this.outerHTML='<span class=&quot;vtd-photo-ph&quot;><?= $vIni ?></span>';">
            <?php else: ?>
              <span class="vtd-photo-ph"><?= $vIni ?></span>
            <?php endif; ?>
          </div>
          <div class="vtd-name"><?= $h($v['public_name']) ?></div>
          <span class="prof-tag <?= $v['is_dental'] ? 'dent' : 'med' ?>"><?= $h($v['tag']) ?></span>
          <div class="vtd-role"><?= $h($v['role']) ?></div>
          <div class="prof-meta">
            <?php if ($v['ehr'] !== ''): ?><span class="prof-meta-pill"><i class="fa-solid fa-laptop-medical"></i> <?= $h($v['ehr']) ?></span><?php endif; ?>
            <span class="prof-meta-pill"><i class="fa-solid fa-clock"></i> <?= $v['years'] > 0 ? $h($v['years'] . '+ yrs') : 'Experienced' ?></span>
            <?php if ($v['hipaa']): ?><span class="prof-meta-pill"><i class="fa-solid fa-shield-halved"></i> HIPAA-Certified</span><?php endif; ?>
          </div>
          <div class="prof-cap"><?= $h($v['cap']) ?></div>
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
      <h2 class="sec-h2" id="vtd-why-h">Virtual assistants who are <em>vetted, certified and ready</em></h2>
      <p class="vtd-lede" style="max-width:none;">
        Every Virtual Teammate is hand-screened for skills, communication and reliability before they ever reach
        your shortlist. Healthcare and dental VAs are HIPAA-certified; business and administrative VAs are tested
        on the exact tools your team runs. You get a teammate, not a temp, matched to your time zone and ramped
        with a dedicated success manager.
      </p>
      <div class="vtd-why-grid">
        <div class="vtd-why-card"><i class="fa-solid fa-user-shield"></i><h3>HIPAA-certified</h3><p>Healthcare &amp; dental VAs complete HIPAA compliance training before placement.</p></div>
        <div class="vtd-why-card"><i class="fa-solid fa-clipboard-check"></i><h3>Rigorously vetted</h3><p>Skills tests, background checks and live interviews: only the top few percent make the bench.</p></div>
        <div class="vtd-why-card"><i class="fa-solid fa-clock"></i><h3>Your time zone</h3><p>Global talent matched to overlap your working hours, so work happens in real time.</p></div>
        <div class="vtd-why-card"><i class="fa-solid fa-handshake-angle"></i><h3>30-day right-fit</h3><p>Not the right match? We re-match at no extra cost in the first 30 days. <a href="<?= $home_base ?>guarantee/">See the promise</a>.</p></div>
      </div>
    </div>
  </section>

  <section class="sec vtd-seo" aria-labelledby="vtd-spec-h">
    <div class="reveal">
      <h2 class="sec-h2" id="vtd-spec-h">Virtual assistants for <em>every team</em></h2>
      <div class="vtd-spec-grid">
        <div class="vtd-spec">
          <h3><i class="fa-solid fa-stethoscope"></i> Medical virtual assistants</h3>
          <p>Medical billing, scribing, patient scheduling, insurance verification and prior authorization: HIPAA-certified support that keeps your practice running. <a href="<?= $home_base ?>services/medical-administrative-support/">Explore medical VAs</a>.</p>
        </div>
        <div class="vtd-spec">
          <h3><i class="fa-solid fa-tooth"></i> Dental virtual assistants</h3>
          <p>Dental front-desk, billing, insurance and treatment-plan coordination so your chairside team can focus on patients. <a href="<?= $home_base ?>services/dental-admin/">Explore dental VAs</a>.</p>
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
        <li><span class="vtd-step-n">3</span><h3>Onboard your teammate</h3><p>Your VA ramps with a dedicated success manager, backed by our 30-day promise.</p></li>
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
      <div class="vtd-faq"><h3>How much does a virtual assistant cost?</h3><p>Transparent flat-rate pricing: typically 60–73% less than an in-house hire once salary, benefits, payroll tax and overhead are factored in. Full-time VAs start at $1,625/month.</p></div>
      <div class="vtd-faq"><h3>Are your virtual assistants HIPAA certified?</h3><p>Yes. Every healthcare and dental VA completes HIPAA compliance training and certification before placement.</p></div>
      <div class="vtd-faq"><h3>How fast can I hire?</h3><p>Most clients get a curated shortlist within days and finish onboarding within one to two weeks.</p></div>
      <div class="vtd-faq"><h3>Can I interview candidates first?</h3><p>Always. We hand-match candidates to your specialty, software and time zone, and you interview the shortlist before deciding.</p></div>
      <div class="vtd-faq"><h3>Where are your VAs based?</h3><p>We source globally, the Philippines, Latin America, Africa and South Asia, and match every VA to your US time zone.</p></div>
      <div class="vtd-faq"><h3>What if it's not the right fit?</h3><p>Our 30-Day Right-Fit Promise re-matches you at no extra cost in the first 30 days.</p></div>
    </div>
  </section>

  <!-- ── CLOSING CTA ── -->
  <section class="sec vtd-closing" aria-label="Get matched">
    <div class="vtd-closing-card reveal">
      <h2 class="sec-h2" style="margin-bottom:8px;">Ready to meet your Virtual Teammate?</h2>
      <p class="vtd-lede" style="margin:0 auto 18px;max-width:60ch;">Get a no-obligation shortlist, value-matched to your specialty and time zone.</p>
      <a href="#vtd-match" class="btn-primary">Get Your Value Match <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </section>
</main>

<!-- Request-state styles (logged-in client requesting a VT from the modal) -->
<style>
.vtd-req-state{display:inline-flex;align-items:center;gap:8px;font-weight:800;font-size:14px;padding:11px 16px;border-radius:12px;}
.vtd-req-state.is-pending{background:rgba(247,185,69,.16);color:#ffe2a8;border:1px solid rgba(247,185,69,.4);}
.vtd-req-state.is-done{background:rgba(78,196,126,.16);color:#bcf0d2;border:1px solid rgba(78,196,126,.4);}
.vtd-req-note{margin:9px 0 0;font-size:12.5px;color:#f4baba;}
.vtd-req-note:empty{display:none;}
</style>

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
  var CAN_REQUEST = <?= $canRequest ? 'true' : 'false' ?>;        // only logged-in clients
  var CSRF = <?= json_encode($csrfToken) ?>;
  var REQ_URL = 'request.php';
  var PENDING = <?= json_encode(array_fill_keys(array_map('strval', $pendingReqIds), true), JSON_FORCE_OBJECT) ?>;
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

  // Embed an intro video inline — hosted file → native <video>; YouTube/Vimeo → iframe.
  // Extract a Google Drive file id — ONLY from a real drive.google.com URL.
  // (Must check the host first: our own talent-media.php URLs also have ?id=…,
  // and a loose /[?&]id=/ match would wrongly treat them as Drive files.)
  function driveId(u){
    u = u || '';
    if (!/drive\.google\.com/i.test(u)) return '';
    var m = u.match(/\/file\/d\/([\w-]+)/) || u.match(/[?&]id=([\w-]+)/);
    return m ? m[1] : '';
  }

  function videoEmbed(url, poster){
    var u = url || '';
    var yt = u.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{11})/);
    if (yt){ return '<div class="vtd-cv-video"><iframe src="https://www.youtube.com/embed/'+yt[1]+'" title="Intro video" allow="fullscreen; picture-in-picture" allowfullscreen></iframe></div>'; }
    var vm = u.match(/vimeo\.com\/(\d+)/);
    if (vm){ return '<div class="vtd-cv-video"><iframe src="https://player.vimeo.com/video/'+vm[1]+'" title="Intro video" allow="fullscreen; picture-in-picture" allowfullscreen></iframe></div>'; }
    var gd = driveId(u);
    if (gd){ return '<div class="vtd-cv-video"><iframe src="https://drive.google.com/file/d/'+gd+'/preview" title="Intro video" allow="autoplay; fullscreen" allowfullscreen></iframe></div>'; }
    // Hosted/direct file → native player with the VT photo as poster (mirrors the
    // working portal modal: <video><source> + poster, not a bare src).
    var p = poster ? ' poster="'+esc(poster)+'"' : '';
    return '<div class="vtd-cv-video"><video controls preload="metadata" playsinline'+p+'><source src="'+esc(u)+'">Your browser does not support inline video.</video></div>';
  }

  // Profile-modal CTA. Logged-in clients request the VT for real (→ request.php,
  // same workflow as the portal's "Request an Additional VT"); everyone else is
  // funnelled to the lead form. `full` is the display name, `fn` the first name.
  function ctaBlock(id, full, fn){
    var head = '<div class="vtd-cv-cta-t">Ready to add <strong>'+esc(fn)+'</strong> to your team?</div>';
    if (CAN_REQUEST){
      if (PENDING[id]){
        return head + '<div class="vtd-req-state is-pending"><i class="fa-solid fa-clock"></i> Requested: pending your CSM’s review</div>';
      }
      return head
        + '<button type="button" class="btn-primary vtd-req-btn" data-request="'+esc(id)+'" data-reqname="'+esc(full)+'">Request '+esc(fn)+' <i class="fa-solid fa-arrow-right"></i></button>'
        + '<p class="vtd-req-note" role="status" aria-live="polite"></p>';
    }
    return head + '<button type="button" class="btn-primary" data-match="'+esc(id)+'" data-matchname="'+esc(full)+'">Request '+esc(fn)+' <i class="fa-solid fa-arrow-right"></i></button>';
  }

  // Send a real VT request (client only). Swaps the CTA for a confirmation.
  function submitRequest(btn){
    var id   = btn.getAttribute('data-request');
    var wrap = btn.closest('.vtd-cv-cta');
    var note = wrap ? wrap.querySelector('.vtd-req-note') : null;
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="vtd-spinner" aria-hidden="true"></span> Sending…';
    if (note){ note.textContent = ''; note.classList.remove('is-err'); }
    var fd = new FormData();
    fd.append('_csrf', CSRF);
    fd.append('vt_id', id);
    fetch(REQ_URL, { method:'POST', body:fd, credentials:'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (res && res.ok){
          PENDING[id] = true;
          if (wrap){ wrap.innerHTML = '<div class="vtd-req-state is-done"><i class="fa-solid fa-circle-check"></i> '+esc(res.message || 'Request sent! Your CSM will follow up.')+'</div>'; }
        } else {
          if (note){ note.textContent = (res && res.error) ? res.error : 'Something went wrong, please try again.'; note.classList.add('is-err'); }
          btn.disabled = false; btn.innerHTML = orig;
        }
      })
      .catch(function(){ if (note){ note.textContent = 'Network error, please try again.'; note.classList.add('is-err'); } btn.disabled = false; btn.innerHTML = orig; });
  }

  function openModal(card){
    var id = card.getAttribute('data-id');
    var name = card.getAttribute('data-name');
    var html = '';
    if (IS_MEMBER && FULL[id]){
      var f = FULL[id];
      var ini = esc((f.name || '?').charAt(0).toUpperCase());
      var fn  = (f.name || '').split(' ')[0] || 'this teammate';   // raw — ctaBlock() escapes
      var nl  = function(s){ return esc(s).replace(/\n/g, '<br>'); };

      html += '<div class="vtd-cv">';
      // ── Header: photo + identity + score badges ──
      html += '<header class="vtd-cv-head">';
      html += '<img class="vtd-cv-photo" src="'+esc(f.photo)+'" alt="'+esc(f.name)+'" '
            + 'onerror="this.outerHTML=&quot;<span class=\'vtd-cv-photo vtd-cv-photo-ph\'>'+ini+'</span>&quot;;">';
      html += '<div class="vtd-cv-id">';
      html += '<span class="vtd-cv-status '+(f.is_hired?'is-engaged':'is-avail')+'">'+esc(f.status)+'</span>';
      html += '<h2 class="vtd-cv-name" id="vtdModalName">'+esc(f.name)+'</h2>';
      html += '<div class="vtd-cv-role">'+esc(f.role || 'Virtual Teammate')+'</div>';
      var meta = [];
      if (f.dept)    meta.push(esc(f.dept));
      if (f.country) meta.push('<i class="fa-solid fa-location-dot"></i> '+esc(f.country));
      if (f.years>0) meta.push(esc(f.years)+(f.years==1?' yr':' yrs')+' experience');
      if (meta.length) html += '<div class="vtd-cv-meta">'+meta.join('<span class="vtd-cv-dot">&bull;</span>')+'</div>';
      if (f.scores && f.scores.length){ html += '<div class="vtd-scores">'+f.scores.map(function(s){return '<span class="vtd-score">'+esc(s)+'</span>';}).join('')+'</div>'; }
      html += '</div></header>';

      // ── Two-column body: Details + Skills | Summary + Experience (media is below) ──
      html += '<div class="vtd-cv-body">';
      html += '<aside class="vtd-cv-side">';
      var kv = [['Role',f.role],['Department',f.dept],['Experience',f.years>0?(f.years+(f.years==1?' yr':' yrs')):''],['English',f.english],['IQ band',f.iq],['Technical',f.technical],['Cognitive role',f.ci],['DISC',f.disc],['Personality',f.persona],['HIPAA',f.hipaa]].filter(function(r){return r[1];});
      if (kv.length){ html += '<section class="vtd-cv-sec"><h4><i class="fa-solid fa-id-badge"></i> Details</h4><dl class="vtd-cv-dl">'+kv.map(function(r){return '<dt>'+esc(r[0])+'</dt><dd>'+esc(r[1])+'</dd>';}).join('')+'</dl></section>'; }
      if (f.skills && f.skills.length){ html += '<section class="vtd-cv-sec"><h4><i class="fa-solid fa-screwdriver-wrench"></i> Core Skills</h4><div class="vtd-skills">'+f.skills.map(function(s){return '<span class="vtd-skill">'+esc(s)+'</span>';}).join('')+'</div></section>'; }
      html += '</aside>';

      html += '<main class="vtd-cv-main">';
      if (f.summary){    html += '<section class="vtd-cv-sec"><h4><i class="fa-solid fa-user"></i> Professional Summary</h4><p class="vtd-cv-text">'+nl(f.summary)+'</p></section>'; }
      if (f.experience){ html += '<section class="vtd-cv-sec"><h4><i class="fa-solid fa-briefcase"></i> Experience</h4><p class="vtd-cv-text">'+nl(f.experience)+'</p></section>'; }
      if (!f.summary && !f.experience){ html += '<section class="vtd-cv-sec"><p class="vtd-cv-text">A detailed summary for this teammate is being finalized.</p></section>'; }
      html += '</main>';
      html += '</div>'; // body

      // ── Media (bottom): intro video + résumé side-by-side ──
      if (f.videoUrl || f.resumeUrl){
        var vBlock = f.videoUrl
          ? videoEmbed(f.videoUrl, f.photo)
          : '<div class="vtd-cv-media-empty"><i class="fa-solid fa-video-slash"></i><span>No intro video on file.</span></div>';
        var rid = driveId(f.resumeUrl);
        var rBlock;
        if (!f.resumeUrl){
          rBlock = '<div class="vtd-cv-media-empty"><i class="fa-solid fa-file-circle-xmark"></i><span>No résumé on file.</span></div>';
        } else if (rid){
          // Genuine Google Drive résumé → Drive preview iframe.
          rBlock = '<iframe class="vtd-cv-pdf" src="https://drive.google.com/file/d/'+rid+'/preview" title="Résumé" loading="lazy"></iframe>'
            + '<div class="vtd-cv-pdf-actions"><a class="vtd-cv-pdf-btn" href="'+esc(f.resumeUrl)+'" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> Open in new window</a></div>';
        } else {
          // Hosted/direct PDF → <embed> (same element the working portal modal uses).
          rBlock = '<embed class="vtd-cv-pdf" src="'+esc(f.resumeUrl)+'#toolbar=1&navpanes=0" type="application/pdf">'
            + '<div class="vtd-cv-pdf-actions">'
            + '<a class="vtd-cv-pdf-btn" href="'+esc(f.resumeUrl)+'" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> Open in new window</a>'
            + '<a class="vtd-cv-pdf-btn" href="'+esc(f.resumeUrl)+'&dl=1" download><i class="fa-solid fa-download"></i> Download résumé</a>'
            + '</div>';
        }
        html += '<div class="vtd-cv-media">';
        html += '<div class="vtd-cv-media-h"><i class="fa-solid fa-photo-film"></i> Media <span>Résumé + intro video imported from HubSpot.</span></div>';
        html += '<div class="vtd-cv-media-grid">';
        html += '<div class="vtd-cv-media-card"><div class="vtd-cv-media-lbl"><i class="fa-solid fa-video"></i> Intro video</div>'+vBlock+'</div>';
        html += '<div class="vtd-cv-media-card"><div class="vtd-cv-media-lbl"><i class="fa-solid fa-file-pdf"></i> Résumé</div>'+rBlock+'</div>';
        html += '</div></div>';
      }

      // ── Request / funnel CTA ──
      html += '<div class="vtd-cv-cta">' + ctaBlock(id, f.name, fn) + '</div>';
      html += '</div>'; // vtd-cv
    } else {
      // Teaser → funnel to lead form.
      html += '<div class="vtd-m-teaser">';
      html += '<h2 class="vtd-m-name" id="vtdModalName">'+esc(name)+'</h2>';
      html += '<div class="vtd-m-sub">'+esc(card.getAttribute('data-role'))+(card.getAttribute('data-country')?' · '+esc(card.getAttribute('data-country')):'')+'</div>';
      var scores = (card.getAttribute('data-scores')||'').split('||').filter(Boolean);
      if (scores.length){ html += '<div class="vtd-scores">'+scores.map(function(s){return '<span class="vtd-score">'+esc(s)+'</span>';}).join('')+'</div>'; }
      var skills = (card.getAttribute('data-skills')||'').split('||').filter(Boolean);
      if (skills.length){ html += '<div class="vtd-skills-h">Skills</div><div class="vtd-skills">'+skills.slice(0,8).map(function(s){return '<span class="vtd-skill">'+esc(s)+'</span>';}).join('')+'</div>'; }
      html += '<div class="vtd-m-lock"><i class="fa-solid fa-lock"></i> Intro video, full résumé and assessment scores are available to members.</div>';
      html += '<button type="button" class="btn-primary vtd-m-cta" data-match="'+esc(id)+'" data-matchname="'+esc(name)+'">Get matched with '+esc(name)+' <i class="fa-solid fa-arrow-right"></i></button>';
      html += '</div>';
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
    // Stop any media still playing: pause <video>, then clear the body so
    // embedded iframes (YouTube/Vimeo) and the PDF stop too.
    var vid = body ? body.querySelector('video') : null;
    if (vid){ try { vid.pause(); vid.removeAttribute('src'); vid.load(); } catch(e){} }
    if (body) body.innerHTML = '';
    if (lastFocus) lastFocus.focus();
  }

  grid.addEventListener('click', function(e){
    var btn = e.target.closest('[data-view]');
    if (!btn) return;
    openModal(btn.closest('.vtd-card'));
  });
  modal.addEventListener('click', function(e){
    if (e.target.closest('[data-close]')) { closeModal(); return; }
    var reqBtn = e.target.closest('[data-request]');
    if (reqBtn){ submitRequest(reqBtn); return; }
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
      function resetBtn(){ if (btn){ btn.disabled=false; btn.classList.remove('is-loading'); if(btn.dataset.orig!==undefined){ btn.innerHTML=btn.dataset.orig; } } }
      if (btn){
        btn.dataset.orig = btn.innerHTML;
        btn.disabled = true;
        btn.classList.add('is-loading');
        btn.innerHTML = '<span class="vtd-spinner" aria-hidden="true"></span> Sending…';
      }
      if (note){ note.textContent = ''; note.classList.remove('is-err'); }
      fetch('<?= $home_base ?>lead.php', { method:'POST', body: new FormData(leadForm), credentials:'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(res){
          if (res && res.ok){
            leadForm.innerHTML = '<div class="vtd-thanks"><i class="fa-solid fa-circle-check"></i><h3>Thank you!</h3><p>We\'ve received your request and will reach out within 1 business day with your matched shortlist.</p></div>';
          } else {
            if (note){ note.textContent = (res && res.error) ? res.error : 'Something went wrong, please try again.'; note.classList.add('is-err'); }
            resetBtn();
          }
        })
        .catch(function(){ if (note){ note.textContent='Network error, please try again.'; note.classList.add('is-err'); } resetBtn(); });
    });
  }
})();
</script>
<?php $hide_lead_band = true; /* this page already has the match/lead form */ ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
