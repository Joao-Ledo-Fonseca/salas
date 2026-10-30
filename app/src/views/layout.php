<?php
use Salas\Core\Support\Csrf;
use Salas\Models\Reserva;
use Salas\Models\Sala;
use Salas\Models\Categoria;
use Salas\Models\Usuario;
use Salas\Models\Periodo;
use Salas\Models\Uso;
use Salas\Controllers\SalaController;
use Salas\Controllers\CategoriaController;
use Salas\Controllers\UsuarioController;
use Salas\Controllers\ReservaController;
use Salas\Controllers\PeriodoController;
use Salas\Controllers\UsoController;
use Salas\Controllers\PermissoesController;
use Salas\Core\Validation\Validator;
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script src="<?= app_url('/js/jquery.js') ?>"></script>
    <script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>
    <script src="<?= app_url('/js/dateformat.js') ?>"></script>
    <script src="<?= app_url('/js/lib.js') ?>"></script>

    <script>window.__APP_THEME__ = <?= json_encode((isset($cfg) && isset($cfg->theme) ? $cfg->theme->key : 'light'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
    <script>window.__APP_THEMES__ = <?= json_encode((isset($cfg) && isset($cfg->availableThemes) ? $cfg->availableThemes : ['light', 'dark']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
    <script src="<?= app_url('/js/theme.js') ?>"></script>

    <?php if (!empty($cfg) && isset($cfg->theme)): ?>
    <style>
        :root {
            <?= $cfg->theme->toCssVariables() ?>
        }
    </style>
    <?php endif; ?>

    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">

    <?php if (!empty($extraHead)): ?>
        <?= $extraHead ?>
    <?php endif; ?>

    <title><?= htmlspecialchars($title ?? 'Reserva de Salas', ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>

    <!-- menu esquerdo -->
    <?php
    if (!isset($menuData)) {
        $menuData = [];
    }
    include __DIR__ . '/MenuEsquerdo.php';
    ?>

    <!-- conteudo -->
    <div class="corpo">
        <?= $content ?? '' ?>
    </div>

</body>
</html>
