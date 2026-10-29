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
use Salas\Core\Support\Util;
use Salas\Core\Validation\Validator;
use Salas\Application\Services\AuthorizationService;
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
