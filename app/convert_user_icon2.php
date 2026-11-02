<?php
// Converter SVG utilizador: viewBox 796 796 200 200 -> 0 0 200 200
$f = 'c:/wamp64/dev/salas/app/src/views/MenuEsquerdo.php';
$c = file_get_contents($f);

$old = '<img src="<?=
 =app_url(\'/img/do-utilizador-branco.png\') ?>" width="20" height="20" style="width:1.43rem;height:1.43rem;" alt="" />';

$new = '<svg class="menu-user-icon" viewBox="0 0 200 200" width="1.43rem" height="1.43rem" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
	<circle cx="100" cy="100" r="20" stroke-width="3" />
	<ellipse cx="100" cy="60" rx="30" ry="26" stroke-width="3" />
	<path d="M80,78 L120,82" stroke-width="3" />
	<path d="M75,82 L125,86" stroke-width="2" />
	<path d="M78,90 L122,94" stroke-width="2" />
</svg>';
echo $new;
?>