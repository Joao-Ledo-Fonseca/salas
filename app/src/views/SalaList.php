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

 $title = 'Cadastro de Salas'; ?>

<h3>Cadastro de Salas</h3>

<div class="lista_comum container_top" style="max-height:60vh; overflow-y:auto;">
    <form class="form_sel" name="form1" method="post" action="<?= app_url('?acao=sala_list') ?>">

        <div style="float:left;">
            <input type="button" name="novo" value="Novo" class="btn1"
                   onclick="abre('sala_form.php?categoria_filtro=<?= (int)$categoria_id ?>&activas=<?= htmlspecialchars((string)$activas, ENT_QUOTES, 'UTF-8') ?>')" />
        </div>

        <span style="float:right; margin-left: 15px;">
            <label for="categoria"><b>Categorias</b></label>
            <select id="categoria" class="dashboard-category-select" style="width:160px;" name="categoria_id" onchange="this.form.submit()">
                <option value="0" <?= $categoria_id === 0 ? 'selected' : '' ?>>Todas</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int) $categoria['id'] ?>" <?= ((int) $categoria['id'] === $categoria_id ? 'selected' : '') ?>><?= htmlspecialchars((string) $categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </span>

        <div style="float:right; width:140px">
            <input type="radio" id="activa" name="activas" value="activas" <?= ($activas=="activas"?'checked':'') ?> onchange="this.form.submit()" />
            <label for="activa" class="dashboard-radio-text"><strong>Só activas</strong></label><br>
            <input type="radio" id="inactiva" name="activas" value="inactivas" <?= ($activas=="inactivas"?'checked':'') ?> onchange="this.form.submit()" />
            <label for="inactiva" class="dashboard-radio-text"><strong>Inactivas</strong></label><br>
            <input type="radio" id="todas" name="activas" value="todas" <?= ($activas=="todas"?'checked':'') ?> onchange="this.form.submit()" />
            <label for="todas" class="dashboard-radio-text"><strong>Todas</strong></label><br>
        </div>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
    </form>
</div>

<div class="container_conteudo" style="max-height:60vh; overflow-y:auto;">
    <table class="lista_comum" cellpadding="4" cellspacing="4">
        <thead>
            <tr>
                <th width="150px">Categoria</th>
                <th width="150px">Nome</th>
                <th width="200px">Descrição</th>
                <th width="200px">Activa</th>
                <th width="50px">ID</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $categoriaAnterior = null; ?>
            <?php foreach ($lista as $sala): ?>
                <?php $categoriaAtual = (string) $sala['categoria']; ?>
                <tr>
                    <td style="border:none"><?= $categoriaAtual === $categoriaAnterior ? '' : htmlspecialchars($categoriaAtual, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a href="<?= htmlspecialchars(app_url('?acao=sala_form&id=' . (int) $sala['id'] . '&categoria_filtro=' . rawurlencode((string) $categoria_id) . '&activas=' . rawurlencode((string) $activas)), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $sala['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars((string) $sala['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= !empty($sala['activa']) ? '&#10003;' : '-' ?></td>
                    <td><?= (int) $sala['id'] ?></td>
                    <td></td>
                </tr>
                <?php $categoriaAnterior = $categoriaAtual; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
