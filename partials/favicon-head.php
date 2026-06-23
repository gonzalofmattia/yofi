<?php
/** Favicon — assets/img/favicon.ico + favicon.png */
$faviconIco = asset_path('img/favicon.ico');
$faviconPng = asset_path('img/favicon.png');
?>
<link rel="icon" href="<?= htmlspecialchars($faviconIco, ENT_QUOTES, 'UTF-8') ?>" sizes="any">
<link rel="shortcut icon" href="<?= htmlspecialchars($faviconIco, ENT_QUOTES, 'UTF-8') ?>">
<link rel="icon" type="image/png" href="<?= htmlspecialchars($faviconPng, ENT_QUOTES, 'UTF-8') ?>">
<link rel="apple-touch-icon" href="<?= htmlspecialchars($faviconPng, ENT_QUOTES, 'UTF-8') ?>">
