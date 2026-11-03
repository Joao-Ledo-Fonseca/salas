<?php
$googleFontsUrl = isset($cfg) && isset($cfg->theme) && method_exists($cfg->theme, 'googleFontsUrl')
    ? $cfg->theme->googleFontsUrl()
    : null;
?>
<?php if (is_string($googleFontsUrl)): ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link id="theme-google-font" rel="stylesheet" href="<?= htmlspecialchars($googleFontsUrl, ENT_QUOTES, 'UTF-8') ?>">
<?php endif; ?>
