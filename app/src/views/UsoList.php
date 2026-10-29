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

 $title = 'Cadastro de Usos'; ?>

<h3>Cadastro de Usos</h3>

<div class="container_top lista_comum" style="max-height:60vh; overflow-y:auto;">
    <form class="form_sel" name="form1" method="post" target="_self">
        <span style="float:left;">
            <input type="button" name="novo" value="Novo" class="btn1" onclick="abre('uso_form.php')" />
        </span>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
    </form>
</div>

<div class="container_conteudo lista_comum" style="max-height:60vh; overflow-y:auto;">
    <table cellpadding="4" cellspacing="4">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>ID</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista as $uso): ?>
                <tr>
                    <td><a href="<?= htmlspecialchars(app_url('?acao=uso_form&id=' . (int) $uso['id']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $uso['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars((string) $uso['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $uso['id'] ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
