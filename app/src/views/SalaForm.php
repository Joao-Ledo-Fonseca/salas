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


<?php $title = 'Cadastro de Salas'; ?>

<h3>Cadastro de Salas</h3>

<div class="container">
    <div class="a_par">
        <div class=aviso style="color:red"><?= htmlspecialchars((string)$errormsg, ENT_QUOTES, 'UTF-8') ?></div>

        <form enctype="multipart/form-data" name="form1" id="sala-form" method="post" action="<?= htmlspecialchars(app_url('?acao=sala_form&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)), ENT_QUOTES, 'UTF-8') ?>">

            <input type="hidden" name="id" value="<?= (int) $id ?>" />
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
            <input type="hidden" name="categoria_filtro" value="<?= htmlspecialchars((string) $categoria_filtro, ENT_QUOTES, 'UTF-8') ?>" />
            <input type="hidden" name="activas" value="<?= htmlspecialchars((string) $activas_filtro, ENT_QUOTES, 'UTF-8') ?>" />
    <input type="hidden" name="return_to" value="<?= htmlspecialchars((string) ($return_to ?? ''), ENT_QUOTES, 'UTF-8') ?>" />
    <input type="hidden" name="return_id" value="<?= (int) ($return_id ?? 0) ?>" />
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />

            <table class="tabela_comum" cellpadding="4" cellspacing="4">
                <tr>
                    <td width="100px">Nome</td>
                    <td><input type="text" name="nome" value="<?= htmlspecialchars((string) $nome, ENT_QUOTES, 'UTF-8') ?>" /> </td>
                </tr>
                <tr>
                    <td width="100px">Descrição</td>
                    <td><input type="text" name="descricao" value="<?= htmlspecialchars((string) $descricao, ENT_QUOTES, 'UTF-8') ?>" /> </td>
                </tr>
                <tr>
                    <td width="100px">Categoria</td>
                    <td>
                        <select name="categoria_id" style="width:180px" id="selecao">
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria['id'] ?>" <?= ((int) $categoria['id'] === (int) $categoria_id ? 'selected' : '') ?>><?= htmlspecialchars((string) $categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="100px">Nº Lugares</td>
                    <td><input type="text" name="lugares" value="<?= (int) $lugares ?>" /> </td>
                </tr>
                <tr>
                    <td width="100px">Activa</td>
                    <td><input type="checkbox" name='activa' value=activa <?= ($activa ? "checked" : "") ?> ></td>
                </tr>
            </table>
<?php
$cancelUrl = (($return_to ?? '') !== '')
    ? app_url('?acao=' . ($return_to ?? '') . '&id=' . (int) ($return_id ?? 0))
    : app_url('?acao=sala_list&categoria_id=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro));
$acoesFormulario = [
    'salvar' => true,
    'excluir' => true,
    'cancelar' => ['mode' => 'redirect', 'url' => $cancelUrl],
];
include __DIR__ . '/components/AcoesFormulario.php';
?>
        </form>

    </div>
    <div class="apar">
        <?php
        include __DIR__ . '/components/SeletorImagemSala.php';
        ?>
    </div>
</div>
<div class="container_conteudo" style="width:100%; margin-top:10px; box-sizing:border-box;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
        <h4 style="margin:0;">Reservas da sala</h4>
        <?php if (!empty($podeEliminarReservas)): ?>
            <form name="eliminar_reservas_form" method="post" action="<?= htmlspecialchars(app_url('?acao=sala_form&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)), ENT_QUOTES, 'UTF-8') ?>" style="margin:0;">
                <input type="hidden" name="id" value="<?= (int) $id ?>" />
                <input type="hidden" name="categoria_filtro" value="<?= htmlspecialchars((string) $categoria_filtro, ENT_QUOTES, 'UTF-8') ?>" />
                <input type="hidden" name="activas" value="<?= htmlspecialchars((string) $activas_filtro, ENT_QUOTES, 'UTF-8') ?>" />
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
                <input type="submit" name="eliminar_reservas" value="Eliminar Reservas" class="btn1 btn-compact" />
            </form>
        <?php endif; ?>
    </div>
    <?php
    $reservasAssociadas = $reservasSala;
    $mostrarUtilizador = true;
    $podeEliminarReserva = !empty($podeEliminarReservas);
    $urlEliminarReserva = app_url('?acao=sala_form&id=' . (int) $id . '&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro));
    $camposEliminarReserva = [
        'id' => (int) $id,
        'categoria_filtro' => $categoria_filtro,
        'activas' => $activas_filtro,
    ];
    include __DIR__ . '/components/ReservasAssociadas.php';
    ?>
</div>

<script src="<?= htmlspecialchars(app_url('/js/sala-imagens.js'), ENT_QUOTES, 'UTF-8') ?>"></script>