<?php
/**
 * Shared error-page template (404 / 403 / 500, etc.).
 *
 * A thin wrapper at the site root (e.g. /404.php) sets these before including:
 *   $err_code        int     HTTP status to send (e.g. 404)
 *   $err_eyebrow     string  small label above the headline
 *   $err_head        string  headline (HTML allowed)
 *   $err_head_plain  string  plain-text headline (for <title>)
 *   $err_msg         string  body copy (HTML allowed)
 *   $err_msg_plain   string  plain-text body (for meta description)
 *
 * Sends the matching HTTP status, renders the normal site chrome, noindex.
 */
$err_code = $err_code ?? 404;
http_response_code((int) $err_code);

// Root-absolute base derived from THIS script's own location, so CSS/JS/image
// and nav/footer links resolve correctly no matter which (possibly deep) URL
// triggered the error. /404.php -> "/"; /vtnew/404.php -> "/vtnew/".
$home_base = preg_replace('#/[^/]*$#', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
if ($home_base === '' || $home_base === null) { $home_base = '/'; }

$page_title       = $err_code . ' — ' . ($err_head_plain ?? 'Error') . ' | Virtual Teammate';
$page_description = $err_msg_plain ?? 'Sorry, something went wrong.';
$robots           = 'noindex,nofollow';
$is_homepage      = false;
$hide_lead_band   = true;   // keep the error page focused, no lead-capture band
include __DIR__ . '/head.php';
include __DIR__ . '/nav.php';
?>
<style>
/* Error pages (404/403/500) — centered, branded, self-contained. */
.err-page{min-height:58vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:90px 24px 110px;}
.err-wrap{max-width:640px;}
.err-code{font-size:140px;line-height:1;font-weight:800;letter-spacing:-.04em;margin-bottom:6px;
  background:linear-gradient(120deg,#dfa949,#f5e4b8 50%,#dfa949);-webkit-background-clip:text;background-clip:text;color:transparent;}
.err-page .sec-lbl{justify-content:center;display:inline-flex;}
.err-h1{font-size:34px;font-weight:800;color:#fff;letter-spacing:-.02em;margin:12px 0 14px;}
.err-p{font-size:16px;line-height:1.6;color:rgba(255,255,255,.7);margin:0 auto 30px;max-width:520px;}
.err-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-bottom:30px;}
.err-links{display:flex;gap:8px 22px;justify-content:center;flex-wrap:wrap;}
.err-links a{font-size:14px;color:rgba(255,255,255,.55);text-decoration:none;font-weight:600;transition:color .2s ease;}
.err-links a:hover{color:var(--gold-lt,#f5e4b8);}
@media (max-width:600px){.err-code{font-size:92px;}.err-h1{font-size:26px;}}
</style>
<main>
<section class="err-page">
  <div class="err-wrap reveal">
    <div class="err-code"><?= htmlspecialchars((string) $err_code) ?></div>
    <div class="sec-lbl"><i class="fa-solid fa-compass"></i> <?= $err_eyebrow ?? 'Page not found' ?></div>
    <h1 class="err-h1"><?= $err_head ?? 'This page took a wrong turn' ?></h1>
    <p class="err-p"><?= $err_msg ?? 'The page you&rsquo;re looking for isn&rsquo;t here. Let&rsquo;s get you back on track.' ?></p>
    <div class="err-btns">
      <a href="<?= $home_base ?>" class="btn-primary">Back to homepage <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>contact/" class="btn-glass">Contact us <i class="fa-solid fa-headset"></i></a>
    </div>
    <div class="err-links">
      <a href="<?= $home_base ?>#specialties">Services</a>
      <a href="<?= $home_base ?>case-studies/">Case Studies</a>
      <a href="<?= $home_base ?>about/">About</a>
      <a href="<?= $home_base ?>careers/">Careers</a>
    </div>
  </div>
</section>
</main>
<?php include __DIR__ . '/footer.php'; ?>
