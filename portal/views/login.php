<?php /** @var string $error @var string $email @var string $next */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>VT Portal — Log in</title>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<link rel="icon" type="image/png" href="<?= e(site_url('images/favicon-32x32.png')) ?>">
<?php include __DIR__ . '/_pwa-head.php'; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="assets/portal.css">
</head>
<body class="portal-naked">
<main class="naked-card">
  <header class="naked-h">
    <div class="naked-eyebrow"><i class="fa-solid fa-lock"></i> VT Portal</div>
    <h1>Log in to your dashboard</h1>
    <p class="naked-sub">Clients, CSMs, and Virtual Teammates use a single login. We'll route you to the right place based on your role.</p>
  </header>

  <?php if ($error !== ''): ?>
    <div class="portal-flash flash-error" role="alert"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post" action="<?= e(portal_url('login')) ?>" class="naked-form">
    <?= csrf_field() ?>
    <input type="hidden" name="next" value="<?= e($next) ?>">

    <label class="naked-label" for="email">Email</label>
    <input class="naked-field" type="email" id="email" name="email" required autofocus
           autocomplete="username" value="<?= e($email) ?>">

    <label class="naked-label" for="password">Password</label>
    <input class="naked-field" type="password" id="password" name="password" required
           autocomplete="current-password">

    <button class="btn-portal-primary naked-submit" type="submit">
      Log in <i class="fa-solid fa-arrow-right"></i>
    </button>
  </form>

  <footer class="naked-foot">
    <button type="button" class="pwa-install-cta"><i class="fa-solid fa-circle-down"></i><span>Install the app</span></button>
    <a href="<?= e(site_url()) ?>" class="naked-back">&larr; Back to marketing site</a>
  </footer>
</main>
</body>
</html>
