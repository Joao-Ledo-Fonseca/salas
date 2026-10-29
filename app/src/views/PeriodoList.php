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

 $title = 'Cadastro de Períodos'; ?>

<h3>Cadastro de Períodos</h3>

<div class="container_top lista_comum" style="max-height:60vh; overflow-y:auto;">
    <div class="form_sel">
        <input type="button" name="novo" value="novo" class="btn1" onclick='abre("periodo_form.php")' />
    </div>
</div>

<div class="container_conteudo lista_comum" style="max-height:60vh; overflow-y:auto;">
    <table style="border-spacing: 4px; padding: 4px;">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Sequência</th>
                <th>ID</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista as $periodo): ?>
                <tr>
                    <td><a href="<?= htmlspecialchars(app_url('?acao=periodo_form&id=' . (int) $periodo['id']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $periodo['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= (int) $periodo['seq'] ?></td>
                    <td><?= (int) $periodo['id'] ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
