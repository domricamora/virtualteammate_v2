<?php
$err_code       = 500;
$err_eyebrow    = 'Something went wrong';
$err_head       = 'Our end <em style="color:var(--gold);font-style:normal;">hit a snag</em>';
$err_head_plain = 'Server error';
$err_msg        = 'Something broke on our side, not yours. Try again in a moment, or contact us if it keeps happening.';
$err_msg_plain  = 'An unexpected server error occurred. Please try again later.';
include __DIR__ . '/includes/error-page.php';
