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

 $title = 'Lista de Utilizadores'; ?>

<h3>Lista de Utilizadores</h3>

<div class="lista_comum container_top" style="max-height:60vh; overflow-y:auto;">
    <form class="form_sel" name="form1" method="post" target="_self">
        <div class="form_sel">
            <input type="button" name="novo" value="Novo" class="btn1" onclick="abre('usuario_form.php')" />
        </div>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
    </form>
</div>

<div class="container_conteudo" style="max-height:60vh; overflow-y:auto;">
    <table class="lista_comum" cellpadding="4" cellspacing="4">
        <thead>
            <tr>
                <th width="150px">Nome</th>
                <th width="150px">E-mail</th>
                <th width="100px">Nível</th>
                <th width="50px">ID</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista as $usuario): ?>
                <tr>
                    <td><a href="<?= htmlspecialchars(app_url('?acao=usuario_form&id=' . (int) $usuario['id']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $usuario['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars((string) $usuario['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $usuario['nivel_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $usuario['id'] ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
