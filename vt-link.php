<?php
/**
 * Public, no-login VT profile viewer for CSM-generated "special links".
 * Validates the token against the portal's vt_special_links table (not revoked,
 * not expired) and renders the teammate's profile. Résumé/video are served
 * through talent-media.php with the same token.
 */
declare(strict_types=1);

$token = isset($_GET['t']) ? trim((string) $_GET['t']) : '';
$vt = null; $error = '';

if (!preg_match('/^[a-f0-9]{16,64}$/i', $token)) {
    $error = 'This link is invalid.';
} else {
    $dbPath = __DIR__ . '/data/portal.sqlite';
    if (!is_file($dbPath)) {
        $error = 'This link is temporarily unavailable.';
    } else {
        try {
            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $st = $pdo->prepare('SELECT vt_user_id, expires_at, revoked FROM vt_special_links WHERE token = :t LIMIT 1');
            $st->execute([':t' => $token]);
            $link = $st->fetch(PDO::FETCH_ASSOC);
            if (!$link)                              { $error = 'This link was not found.'; }
            elseif ((int) $link['revoked'] === 1)    { $error = 'This link has been revoked.'; }
            elseif ((int) $link['expires_at'] <= time()) { $error = 'This link has expired.'; }
            else {
                $q = $pdo->prepare(
                    "SELECT u.id, u.first_name, u.last_name, u.country, u.role,
                            p.role_title, p.department, p.experience_years, p.summary, p.experience_text,
                            p.english_level, p.predictive_index, p.quiz_tier, p.engagement_score, p.hipaa_certified
                     FROM users u LEFT JOIN vt_profiles p ON p.user_id = u.id
                     WHERE u.id = :id AND u.active = 1 AND u.role IN ('vt_onpool','vt_hired') LIMIT 1"
                );
                $q->execute([':id' => (int) $link['vt_user_id']]);
                $vt = $q->fetch(PDO::FETCH_ASSOC) ?: null;
                if (!$vt) { $error = 'This teammate is no longer available.'; }
            }
        } catch (Throwable $_) { $error = 'This link is temporarily unavailable.'; }
    }
}

$e   = static fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
$nm  = $vt ? (trim(($vt['first_name'] ?? '') . ' ' . ($vt['last_name'] ?? '')) ?: 'Virtual Teammate') : '';
$role = $vt ? (trim((string) ($vt['role_title'] ?? '')) ?: trim((string) ($vt['department'] ?? '')) ?: 'Virtual Teammate') : '';
$scores = $vt ? array_values(array_filter([
    trim((string) ($vt['predictive_index'] ?? '')),
    trim((string) ($vt['quiz_tier'] ?? '')),
    trim((string) ($vt['engagement_score'] ?? '')),
], static fn ($s) => $s !== '')) : [];
?><!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= $vt ? $e($nm) . ' — Virtual Teammate' : 'Virtual Teammate' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<style>
:root{color-scheme:dark;--gold:#dfa949;--gold-lt:#f5e4b8;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Manrope',Arial,sans-serif;background:linear-gradient(165deg,#241b52 0%,#34267e 48%,#4a3a9e 100%);background-attachment:fixed;color:#fff;min-height:100vh;-webkit-font-smoothing:antialiased;}
.vl-top{display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid rgba(255,255,255,.1);}
.vl-top img{height:30px;filter:brightness(0) invert(1);}
.vl-cta{font-size:13px;font-weight:800;color:#1a1330;background:linear-gradient(135deg,var(--gold),#fbd97a);padding:9px 16px;border-radius:9px;text-decoration:none;}
.vl-wrap{max-width:960px;margin:28px auto;padding:0 20px 60px;}
.vl-card{background:rgba(255,255,255,.06);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.16);border-radius:20px;overflow:hidden;}
.vl-hero{display:flex;gap:22px;align-items:center;flex-wrap:wrap;padding:28px;background:linear-gradient(135deg,rgba(57,25,186,.4),rgba(124,58,237,.18));}
.vl-photo{width:120px;height:120px;border-radius:50%;object-fit:cover;flex:0 0 120px;border:3px solid rgba(247,185,69,.5);background:#2a2160;box-shadow:0 12px 30px rgba(0,0,0,.45);}
.vl-id{flex:1;min-width:240px;}
.vl-name{font-size:28px;font-weight:800;letter-spacing:-.4px;}
.vl-role{font-size:15px;color:var(--gold-lt);margin-top:4px;}
.vl-meta{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;}
.vl-chip{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;padding:5px 12px;border-radius:30px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);}
.vl-chip i{color:var(--gold);}
.vl-scores{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;}
.vl-score{font-size:12px;font-weight:700;padding:5px 12px;border-radius:8px;background:rgba(57,25,186,.3);border:1px solid rgba(187,167,250,.35);color:#cdbcfb;}
.vl-body{padding:26px 28px;}
.vl-sec{margin-bottom:24px;}
.vl-sec h2{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:1.1px;color:var(--gold);margin-bottom:10px;}
.vl-sec p{font-size:14.5px;line-height:1.65;color:rgba(255,255,255,.88);white-space:pre-wrap;}
.vl-media{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.vl-media-card{background:rgba(0,0,0,.25);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:14px;}
.vl-media-h{font-size:11px;font-weight:800;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;}
.vl-video,.vl-pdf{width:100%;border:0;border-radius:8px;background:#000;display:block;}
.vl-video{aspect-ratio:16/9;}
.vl-pdf{height:520px;background:#1a1535;}
.vl-foot{display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;}
.vl-btn{font-size:12.5px;font-weight:700;color:#fff;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18);padding:8px 14px;border-radius:9px;text-decoration:none;display:inline-flex;align-items:center;gap:7px;}
.vl-btn i{color:var(--gold);}
.vl-empty{padding:80px 24px;text-align:center;}
.vl-empty i{font-size:46px;color:rgba(247,185,69,.5);margin-bottom:16px;}
.vl-empty h1{font-size:24px;margin-bottom:8px;}
.vl-empty p{color:rgba(255,255,255,.7);}
@media (max-width:680px){.vl-media{grid-template-columns:1fr;}.vl-name{font-size:23px;}}
</style>
</head>
<body>
<div class="vl-top">
  <a href="https://virtualteammate.com/"><img src="images/logo.webp" alt="Virtual Teammate"></a>
  <a class="vl-cta" href="https://virtualteammate.com/#cta">Hire a Virtual Teammate</a>
</div>

<?php if (!$vt): ?>
  <div class="vl-empty">
    <i class="fa-solid fa-link-slash"></i>
    <h1><?= $e($error ?: 'Link unavailable') ?></h1>
    <p>Please ask your Client Success Manager for an up-to-date link.</p>
    <p style="margin-top:18px;"><a class="vl-cta" href="https://virtualteammate.com/virtual-teammates/">Browse our talent &rarr;</a></p>
  </div>
<?php else:
  $resumeUrl = 'talent-media.php?id=' . (int) $vt['id'] . '&k=resume&t=' . rawurlencode($token);
  $videoUrl  = 'talent-media.php?id=' . (int) $vt['id'] . '&k=video&t=' . rawurlencode($token);
  $hasResume = (bool) glob(__DIR__ . '/data/media/vt/' . (int) $vt['id'] . '/resume.*');
  $hasVideo  = (bool) glob(__DIR__ . '/data/media/vt/' . (int) $vt['id'] . '/video.*');
?>
  <div class="vl-wrap">
    <div class="vl-card">
      <div class="vl-hero">
        <img class="vl-photo" src="talent-photo.php?id=<?= (int) $vt['id'] ?>" alt="<?= $e($nm) ?>"
             onerror="this.style.visibility='hidden';">
        <div class="vl-id">
          <div class="vl-name"><?= $e($nm) ?></div>
          <div class="vl-role"><?= $e($role) ?></div>
          <div class="vl-meta">
            <?php if (!empty($vt['department'])): ?><span class="vl-chip"><?= $e($vt['department']) ?></span><?php endif; ?>
            <?php if (!empty($vt['country'])): ?><span class="vl-chip"><i class="fa-solid fa-location-dot"></i> <?= $e($vt['country']) ?></span><?php endif; ?>
            <?php if ((int) ($vt['experience_years'] ?? 0) > 0): ?><span class="vl-chip"><i class="fa-solid fa-briefcase"></i> <?= (int) $vt['experience_years'] ?> yrs</span><?php endif; ?>
            <?php if (!empty($vt['english_level'])): ?><span class="vl-chip"><i class="fa-solid fa-language"></i> <?= $e($vt['english_level']) ?></span><?php endif; ?>
            <?php if (!empty($vt['hipaa_certified'])): ?><span class="vl-chip"><i class="fa-solid fa-shield-halved"></i> HIPAA</span><?php endif; ?>
          </div>
          <?php if ($scores): ?><div class="vl-scores"><?php foreach ($scores as $s): ?><span class="vl-score"><?= $e($s) ?></span><?php endforeach; ?></div><?php endif; ?>
        </div>
      </div>
      <div class="vl-body">
        <?php if (trim((string) ($vt['summary'] ?? '')) !== ''): ?>
          <div class="vl-sec"><h2>Professional Summary</h2><p><?= $e($vt['summary']) ?></p></div>
        <?php endif; ?>
        <?php if (trim((string) ($vt['experience_text'] ?? '')) !== ''): ?>
          <div class="vl-sec"><h2>Experience</h2><p><?= $e($vt['experience_text']) ?></p></div>
        <?php endif; ?>
        <?php if ($hasVideo || $hasResume): ?>
          <div class="vl-sec">
            <h2>Media</h2>
            <div class="vl-media">
              <div class="vl-media-card">
                <div class="vl-media-h"><i class="fa-solid fa-video"></i> Intro video</div>
                <?php if ($hasVideo): ?>
                  <video class="vl-video" controls preload="metadata" playsinline><source src="<?= $e($videoUrl) ?>"></video>
                <?php else: ?><p class="muted" style="color:rgba(255,255,255,.5);font-size:13px;">No intro video on file.</p><?php endif; ?>
              </div>
              <div class="vl-media-card">
                <div class="vl-media-h"><i class="fa-solid fa-file-pdf"></i> Résumé</div>
                <?php if ($hasResume): ?>
                  <iframe class="vl-pdf" src="<?= $e($resumeUrl) ?>#toolbar=0&navpanes=0" title="Résumé"></iframe>
                  <div class="vl-foot">
                    <a class="vl-btn" href="<?= $e($resumeUrl) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> Open</a>
                    <a class="vl-btn" href="<?= $e($resumeUrl) ?>&dl=1" download><i class="fa-solid fa-download"></i> Download</a>
                  </div>
                <?php else: ?><p class="muted" style="color:rgba(255,255,255,.5);font-size:13px;">No résumé on file.</p><?php endif; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
        <div class="vl-sec" style="margin-bottom:0;text-align:center;padding-top:8px;">
          <a class="vl-cta" href="https://virtualteammate.com/#cta">Add <?= $e($vt['first_name'] ?: 'this teammate') ?> to your team &rarr;</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
</body>
</html>
