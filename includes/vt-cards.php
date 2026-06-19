<?php
/**
 * Reusable "Virtual Teammates" cards section (live, from the portal DB).
 *
 * Two modes:
 *  - Preview (default): a small ranked grid of real, vetted teammates drawn from
 *    one or more departments (the service-page usage).
 *  - Filterable directory ($vtc_filterable = true): the full matching bench with
 *    search + department + skill filters and a "load more" button, styled exactly
 *    like /virtual-teammates/ — used on the business page for non-clinical roles.
 *
 * Anonymous-safe: public name (first + last initial), role, country and a few
 * skills only — no PII, no media. Full profiles/videos/résumés live at
 * /virtual-teammates/. Graceful no-op if the DB is missing or nobody matches.
 *
 * Set before include (all optional except $home_base + one of $vtc_depts /
 * $vtc_exclude_depts):
 *   $home_base         string  e.g. '../' (path back to webroot)
 *   $vtc_depts         array   departments to draw from (e.g. ['Medical'])
 *   $vtc_exclude_depts array   departments to EXCLUDE — selects everything else
 *                               (e.g. ['Medical','Dental']). Takes precedence.
 *   $vtc_filterable    bool    render search/department/skill filters + load-more
 *                               and show the full bench (default false = preview)
 *   $vtc_page          int     load-more page size in filterable mode (default 9)
 *   $vtc_keywords      array   role/skill keywords to prioritise (preview ranking)
 *   $vtc_limit         int     max cards in preview mode (default 6)
 *   $vtc_label         string  eyebrow label (default 'Meet the Bench')
 *   $vtc_heading       string  H2 (HTML allowed, e.g. with <em>)
 *   $vtc_sub           string  sub copy under the H2
 *   $vtc_cta_href      string  card button href (default $home_base.'#cta-practice-audit')
 *   $vtc_cta_intent    string  data-cta-intent on the card button (default 'practice-audit')
 *   $vtc_cta_label     string  card button text (default 'Request this teammate')
 *   $vtc_cta_vt        bool    add data-vt-id/data-vt-name for on-page form prefill (default false)
 *   $vtc_browse        bool    show the "Browse all Virtual Teammates" footer (preview only; default true)
 */

$home_base         = $home_base ?? './';
$vtc_depts         = $vtc_depts ?? [];
$vtc_exclude_depts = $vtc_exclude_depts ?? [];
if (!$vtc_depts && !$vtc_exclude_depts) { return; }

$vtc_keywords   = $vtc_keywords   ?? [];
$vtc_limit      = $vtc_limit      ?? 6;
$vtc_filterable = $vtc_filterable ?? false;
$vtc_page       = $vtc_page       ?? 9;
$vtc_label      = $vtc_label      ?? 'Meet the Bench';
$vtc_heading    = $vtc_heading    ?? 'Meet Your <em>Virtual Teammates</em>';
$vtc_sub        = $vtc_sub        ?? 'A sample of real, vetted teammates, matched to your time zone and ready to start in 1&ndash;2 weeks.';
$vtc_cta_href   = $vtc_cta_href   ?? ($home_base . '#cta-practice-audit');
$vtc_cta_intent = $vtc_cta_intent ?? 'practice-audit';
$vtc_cta_label  = $vtc_cta_label  ?? 'Book my practice staffing audit';
$vtc_cta_vt     = $vtc_cta_vt     ?? false;
$vtc_browse     = $vtc_browse     ?? true;

/* ── Load candidates from the portal DB ── */
$vtc_rows = [];
$vtc_dbp  = __DIR__ . '/../data/portal.sqlite';
if (is_file($vtc_dbp)) {
    try {
        $vtc_pdo = new PDO('sqlite:' . $vtc_dbp);
        $vtc_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT u.id, u.first_name, u.last_name, u.country, u.photo_url,
                       p.department, p.role_title, p.primary_skills, p.experience_years,
                       p.ehr_software, p.hipaa_certified, p.summary
                FROM vt_profiles p
                JOIN users u ON u.id = p.user_id
                WHERE u.role IN ('vt_hired','vt_onpool') AND u.active = 1
                  AND u.email NOT LIKE 'demo-%'
                  AND TRIM(COALESCE(p.department,'')) <> ''";
        if ($vtc_exclude_depts) {
            $ph   = implode(',', array_fill(0, count($vtc_exclude_depts), '?'));
            $sql .= " AND p.department NOT IN ($ph)";
            $args = array_values($vtc_exclude_depts);
        } else {
            $ph   = implode(',', array_fill(0, count($vtc_depts), '?'));
            $sql .= " AND p.department IN ($ph)";
            $args = array_values($vtc_depts);
        }
        $st = $vtc_pdo->prepare($sql);
        $st->execute($args);
        $vtc_rows = $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $_) { $vtc_rows = []; }
}
if (!$vtc_rows) { return; }

/* ── Rank: page-role keyword match first, then teammates with a photo, then
 *    by department + name. Keeps cards relevant but always fills the grid. ── */
$vtc_kw = array_values(array_filter(array_map(
    static fn($k) => trim(mb_strtolower((string) $k)),
    $vtc_keywords
)));
$vtc_score = static function (array $r) use ($vtc_kw): int {
    if (!$vtc_kw) { return 0; }
    $hay = mb_strtolower(($r['role_title'] ?? '') . ' ' . ($r['primary_skills'] ?? ''));
    foreach ($vtc_kw as $k) { if ($k !== '' && str_contains($hay, $k)) { return 1; } }
    return 0;
};
usort($vtc_rows, static function ($a, $b) use ($vtc_score) {
    $sa = $vtc_score($a); $sb = $vtc_score($b);
    if ($sa !== $sb) { return $sb <=> $sa; }
    $pa = trim((string) ($a['photo_url'] ?? '')) !== '' ? 1 : 0;
    $pb = trim((string) ($b['photo_url'] ?? '')) !== '' ? 1 : 0;
    if ($pa !== $pb) { return $pb <=> $pa; }
    return [$a['department'], $a['first_name']] <=> [$b['department'], $b['first_name']];
});
$vtc_total = count($vtc_rows);
// Filterable mode renders the full bench (paginated client-side); preview slices.
$vtc_show  = $vtc_filterable ? $vtc_rows : array_slice($vtc_rows, 0, max(1, (int) $vtc_limit));

/* ── Helpers ── */
$vtc_h = static fn($s): string => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
$vtc_split = static function (string $s): array {
    $parts = preg_split('/[\r\n;,|•·]+/u', $s) ?: [];
    $out = [];
    foreach ($parts as $p) { $p = trim($p); if ($p !== '') { $out[$p] = true; } }
    return array_keys($out);
};

/* ── Department → skills map for the dependent skill filter (filterable only) ── */
$vtc_deptSkills = [];
if ($vtc_filterable) {
    foreach ($vtc_rows as $r) {
        $d = trim((string) $r['department']);
        if ($d === '') { continue; }
        foreach ($vtc_split((string) ($r['primary_skills'] ?? '')) as $sk) {
            $vtc_deptSkills[$d][$sk] = true;
        }
    }
    foreach ($vtc_deptSkills as $d => $set) { $vtc_deptSkills[$d] = array_keys($set); sort($vtc_deptSkills[$d]); }
    ksort($vtc_deptSkills);
}
?>
<section class="sec vtd-directory vt-cards" aria-labelledby="vtc-h"<?php if ($vtc_filterable): ?> id="vtcDirectory" data-page="<?= (int) $vtc_page ?>" data-dept-skills="<?= $vtc_h(json_encode($vtc_deptSkills, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?>"<?php endif; ?>>
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-id-badge"></i> <?= $vtc_h($vtc_label) ?></div>
    <h2 class="svc-h2" id="vtc-h"><?= $vtc_heading /* HTML allowed */ ?></h2>
    <p class="sec-sub" style="max-width:700px;margin:0 auto;"><?= $vtc_sub /* HTML allowed */ ?></p>
  </div>

  <?php if ($vtc_filterable): ?>
  <div class="vtd-filters reveal" role="search">
    <div class="vtd-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="search" class="vtc-search-input" placeholder="Search by name, role or skill…" aria-label="Search virtual teammates">
    </div>
    <select class="vtd-select vtc-dept" aria-label="Filter by department">
      <option value="">All departments</option>
      <?php foreach (array_keys($vtc_deptSkills) as $d): ?>
        <option value="<?= $vtc_h($d) ?>"><?= $vtc_h($d) ?></option>
      <?php endforeach; ?>
    </select>
    <select class="vtd-select vtc-skill" aria-label="Filter by skill" disabled>
      <option value="">All skills</option>
    </select>
    <button type="button" class="vtd-reset vtc-reset">Reset</button>
    <span class="vtc-count" aria-live="polite" style="color:rgba(255,255,255,.6);font-size:13px;"></span>
  </div>
  <?php endif; ?>

  <div class="vtd-grid"<?php if (!$vtc_filterable): ?> style="margin-top:34px;"<?php endif; ?>>
    <?php foreach ($vtc_show as $i => $v):
        $first   = trim((string) $v['first_name']);
        $lastIni = ($ln = trim((string) $v['last_name'])) !== '' ? mb_strtoupper(mb_substr($ln, 0, 1)) . '.' : '';
        $name    = trim($first . ' ' . $lastIni) ?: 'Virtual Teammate';
        $dept    = trim((string) $v['department']);
        $role    = trim((string) $v['role_title']) ?: ($dept ?: 'Virtual Assistant');
        $country = trim((string) $v['country']);
        $allSk   = $vtc_split((string) ($v['primary_skills'] ?? ''));
        $hasPhoto= trim((string) ($v['photo_url'] ?? '')) !== '';
        $ini     = $vtc_h(mb_strtoupper(mb_substr($name, 0, 1)) ?: '?');
        $delay   = 'd' . (($i % 3) + 1);
        $blob    = strtolower(trim(implode(' ', array_filter([$name, $role, $dept, $country, implode(' ', $allSk)]))));

        /* ── Talent-card content (mirrors the homepage "Meet the Team" cards) ── */
        $hay      = mb_strtolower($dept . ' ' . $role);
        $isDental = str_contains($hay, 'dental');
        $isClin   = $isDental || str_contains($hay, 'medical');
        // Specialty tag: department name, classified gold (medical/other) vs violet (dental).
        $tag      = $dept !== '' ? $dept : ($isDental ? 'Dental VA' : ($isClin ? 'Medical VA' : 'Virtual Assistant'));
        // Meta pills: Systems (EHR) · Experience · HIPAA — same trio as the talent cards.
        $ehr      = trim((string) ($v['ehr_software'] ?? ''));
        $years    = (int) ($v['experience_years'] ?? 0);
        $expLbl   = $years > 0 ? $years . '+ yrs' : 'Experienced';
        $hipaa    = !empty($v['hipaa_certified']) || $isClin;
        // One-line capability: first sentence of summary, else primary skills, else a default.
        $cap = trim((string) ($v['summary'] ?? ''));
        if ($cap !== '') {
            $cap = preg_split('/(?<=[.!?])\s+/', $cap)[0];
        } elseif ($allSk) {
            $cap = implode(', ', array_slice($allSk, 0, 4));
        } else {
            $cap = 'Ready to support your team from day one.';
        }
        $cap = function_exists('mb_strimwidth')
            ? mb_strimwidth($cap, 0, 96, '…')
            : (strlen($cap) > 96 ? substr($cap, 0, 95) . '…' : $cap);
    ?>
    <article class="vtd-card reveal <?= $delay ?>"
             data-dept="<?= $vtc_h($dept) ?>"
             data-skills="<?= $vtc_h(strtolower(implode('||', $allSk))) ?>"
             data-search="<?= $vtc_h($blob) ?>">
      <div class="vtd-photo">
        <?php if ($hasPhoto): ?>
          <img src="<?= $home_base ?>talent-photo.php?id=<?= (int) $v['id'] ?>&amp;thumb=1" alt="<?= $vtc_h($name . ', ' . $role) ?>" decoding="async" loading="lazy" width="92" height="92"
               onerror="this.outerHTML='<span class=&quot;vtd-photo-ph&quot;><?= $ini ?></span>';">
        <?php else: ?>
          <span class="vtd-photo-ph"><?= $ini ?></span>
        <?php endif; ?>
      </div>
      <div class="vtd-name"><?= $vtc_h($name) ?></div>
      <span class="prof-tag <?= $isDental ? 'dent' : 'med' ?>"><?= $vtc_h($tag) ?></span>
      <div class="vtd-role"><?= $vtc_h($role) ?></div>
      <div class="prof-meta">
        <?php if ($ehr !== ''): ?><span class="prof-meta-pill"><i class="fa-solid fa-laptop-medical"></i> <?= $vtc_h($ehr) ?></span><?php endif; ?>
        <span class="prof-meta-pill"><i class="fa-solid fa-clock"></i> <?= $vtc_h($expLbl) ?></span>
        <?php if ($hipaa): ?><span class="prof-meta-pill"><i class="fa-solid fa-shield-halved"></i> HIPAA-Compliant</span><?php endif; ?>
      </div>
      <div class="prof-cap"><?= $vtc_h($cap) ?></div>
      <a class="vtd-view" href="<?= $vtc_h($vtc_cta_href) ?>" data-cta-intent="<?= $vtc_h($vtc_cta_intent) ?>"<?php if ($vtc_cta_vt): ?> data-vt-id="<?= (int) $v['id'] ?>" data-vt-name="<?= $vtc_h($name . ', ' . $role) ?>"<?php endif; ?>>
        <i class="fa-solid fa-user-plus"></i> <?= $vtc_h($vtc_cta_label) ?>
      </a>
    </article>
    <?php endforeach; ?>
  </div>

  <?php if ($vtc_filterable): ?>
  <p class="vtd-noresults" hidden>No teammates match your filters. <a href="<?= $vtc_h($vtc_cta_href) ?>" data-cta-intent="<?= $vtc_h($vtc_cta_intent) ?>">Tell us what you need</a> and we&rsquo;ll find them.</p>
  <div class="vtd-more-wrap"><button type="button" class="btn-primary vtd-more">Load more teammates</button></div>
  <?php elseif ($vtc_browse): ?>
  <p class="cta-stages-foot reveal" style="margin-top:28px;">
    <?php if ($vtc_total > count($vtc_show)): ?>Showing <?= count($vtc_show) ?> of <?= (int) $vtc_total ?> on the bench. <?php endif; ?>
    <a href="<?= $home_base ?>virtual-teammates/">Browse all Virtual Teammates</a> to filter by department and skill and see full profiles, intro videos &amp; résumés.
  </p>
  <?php endif; ?>
</section>
<?php if ($vtc_filterable): ?>
<script>
(function () {
  var sec = document.getElementById('vtcDirectory');
  if (!sec) { return; }
  var grid     = sec.querySelector('.vtd-grid');
  var cards    = Array.prototype.slice.call(grid.querySelectorAll('.vtd-card'));
  var search   = sec.querySelector('.vtc-search-input');
  var deptSel  = sec.querySelector('.vtc-dept');
  var skillSel = sec.querySelector('.vtc-skill');
  var moreBtn  = sec.querySelector('.vtd-more');
  var noRes    = sec.querySelector('.vtd-noresults');
  var countEl  = sec.querySelector('.vtc-count');
  var resetBtn = sec.querySelector('.vtc-reset');
  var DEPT_SKILLS = {};
  try { DEPT_SKILLS = JSON.parse(sec.getAttribute('data-dept-skills') || '{}'); } catch (e) {}
  var PAGE = parseInt(sec.getAttribute('data-page'), 10) || 9;
  var shown = PAGE;

  function norm(s){ return (s == null ? '' : String(s)).trim().toLowerCase(); }

  function matches(card){
    var q  = norm(search && search.value);
    var d  = deptSel ? deptSel.value : '';
    var sk = norm(skillSel && skillSel.value);
    if (q && card.getAttribute('data-search').indexOf(q) === -1) { return false; }
    if (d && card.getAttribute('data-dept') !== d) { return false; }
    if (sk && ('||' + card.getAttribute('data-skills') + '||').indexOf('||' + sk + '||') === -1) { return false; }
    return true;
  }

  function apply(){
    var visible = 0;
    cards.forEach(function (c) {
      if (matches(c)) { visible++; c.style.display = visible <= shown ? '' : 'none'; }
      else { c.style.display = 'none'; }
    });
    if (noRes)   { noRes.hidden = visible !== 0; }
    if (moreBtn) { moreBtn.style.display = visible > shown ? '' : 'none'; }
    if (countEl) { countEl.textContent = visible + (visible === 1 ? ' teammate' : ' teammates'); }
  }

  function populateSkills(){
    if (!skillSel) { return; }
    var d = deptSel ? deptSel.value : '';
    skillSel.innerHTML = '<option value="">All skills</option>';
    var list = (d && DEPT_SKILLS[d]) ? DEPT_SKILLS[d] : [];
    list.forEach(function (s) { var o = document.createElement('option'); o.value = s; o.textContent = s; skillSel.appendChild(o); });
    skillSel.disabled = list.length === 0;
  }

  if (search)   { search.addEventListener('input', function () { shown = PAGE; apply(); }); }
  if (deptSel)  { deptSel.addEventListener('change', function () { populateSkills(); shown = PAGE; apply(); }); }
  if (skillSel) { skillSel.addEventListener('change', function () { shown = PAGE; apply(); }); }
  if (moreBtn)  { moreBtn.addEventListener('click', function () { shown += PAGE; apply(); }); }
  if (resetBtn) { resetBtn.addEventListener('click', function () { if (search) { search.value = ''; } if (deptSel) { deptSel.value = ''; } populateSkills(); shown = PAGE; apply(); }); }
  apply();
})();
</script>
<?php endif; ?>
<?php
// Reset so a later include on the same request can't inherit stale values.
unset($vtc_depts, $vtc_exclude_depts, $vtc_filterable, $vtc_page, $vtc_keywords,
      $vtc_limit, $vtc_label, $vtc_heading, $vtc_sub, $vtc_cta_href, $vtc_cta_intent,
      $vtc_cta_label, $vtc_cta_vt, $vtc_browse, $vtc_deptSkills);
