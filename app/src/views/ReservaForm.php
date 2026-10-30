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

 <?php $title = 'Reserva'; ?>

<form name="frm_reserva" id="frm_reserva" onsubmit="return salvaFormularioReserva()">

    <h3>Reserva</h3>

    <input type="hidden" name="id" value="<?= (int) $id ?>" />
    <input type="hidden" name="sala_id" value="<?= (int) $sala_id ?>" />
    <input type="hidden" name="periodo_id" value="<?= (int) $periodo_id ?>" />
    <input type="hidden" name="periodos" id="periodos" value="<?= htmlspecialchars((string)($periodos ?? ''), ENT_QUOTES, 'UTF-8') ?>" />
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />

    <input type="text" placeholder="dia" name="dia" id="dia" value="<?= htmlspecialchars((string)$dia, ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom:2px;" /><BR />
    <input type="text" name="sala" value="<?= htmlspecialchars((string)$sala, ENT_QUOTES, 'UTF-8') ?>" disabled style="margin-bottom:2px;" />
    <input type="text" name="periodo" value="<?= (isset($periodos) && substr_count((string)$periodos, ',') > 0) ? 'Multiplos' : htmlspecialchars((string)$periodo, ENT_QUOTES, 'UTF-8') ?>" disabled style="margin-bottom:2px;" />

    <input type="text" placeholder="Entidade" name="entidade" id="entidade" value="<?= htmlspecialchars((string)$entidade_desc, ENT_QUOTES, 'UTF-8') ?>" required style="margin-bottom:2px;" /><BR />

    <select name="uso_id" id="uso_id" required style="margin-top:0; margin-bottom:2px;">
        <option value="">-- selecione uso --</option>
        <?php foreach (($usos ?? []) as $u): ?>
            <option value="<?= (int) $u['id'] ?>" <?= ((int)($u['id']) === (int)($uso_id ?? 0) ? 'selected' : '') ?>><?= htmlspecialchars((string) $u['nome'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select><BR />



    <input type="text" placeholder="Observação" name="observacao" id="observacao" value="<?= htmlspecialchars((string)$observacao, ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom:2px;" /><BR />
    <br />

    <select name="status" id="status" style="margin-bottom:2px;">
        <?php foreach ($options_status as $option): ?>
            <option value="<?= (int) $option['value'] ?>" <?= $option['selected'] ? 'selected' : '' ?>><?= htmlspecialchars($option['nome'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select><BR />

    <h3>Repetir semanalmente até</h3>
    <input type="text" placeholder="data final" name="data_final" id="data_final" value="" style="margin-bottom:2px;" /><BR />

    <input type="button" name="Fechar" value="Fechar" class="btn1" style="width:80px" onClick="fecharForm()" >
    <input type="button" name="Excluir" value="Excluir" class="btn1" style="width:80px" onclick="excluirFormularioReserva(<?= (int) $id ?>)">
    <input type="submit" name="salvar" value="Salvar" class="btn1" style="width:80px">
    <br><br>
    <Label for="user">Registado por: </Label>
    <input type="text" placeholder="User" name="user" id="user" value="<?= htmlspecialchars((string)$usuario, ENT_QUOTES, 'UTF-8') ?>" style="width:50%; margin-bottom:2px;" disabled><BR />

</form>
