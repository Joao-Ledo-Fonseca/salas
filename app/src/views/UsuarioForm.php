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

 $title = 'Perfil de utilizador'; ?>

<script>
    nomeSessao = <?= json_encode($sessionUserName) ?>;
    nivelSessao = <?= $sessionUserLevel ?>;
    nomeEditar = <?= json_encode((string) $nome) ?>;
    nivelEditar = <?= (int) $nivel ?>;

    function user_self_edit(uSessao, nSessao, uEditar, nEditar) {
        if (uEditar == uSessao || nEditar > nSessao || nSessao == 0) {
            document.getElementById("permissoes").style.display = "none";
            document.getElementById("excluir").style.display = "none";
        }
    }
</script>

<h3>Perfil de utilizador</h3>

<div class="container_conteudo lista_comum">
    <div class=aviso style="color:red"><?= htmlspecialchars((string)$errormsg, ENT_QUOTES, 'UTF-8') ?></div>

    <div style="flex:1 1 360px; min-width:320px; order:1;">
    <form name="form1" method="post" action="<?= app_url('?acao=usuario_form') ?>">

        <input type="hidden" name="id" value="<?= (int)$id ?>" />
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />

        <table class="tabela_comum" cellpadding="4" cellspacing="4">
            <tr>
                <td width="100">Nome</td>
                <td><input type="text" name="nome" value="<?= htmlspecialchars((string) $nome, ENT_QUOTES, 'UTF-8') ?>" required /> </td>
            </tr>
            <tr>
                <td width="100">E-mail</td>
                <td><input type="email" name="email" value="<?= htmlspecialchars((string) $email, ENT_QUOTES, 'UTF-8') ?>" /></td>
            </tr>
            <tr>
                <td width="100">Senha</td>
                <td><input type="password" name="senha" value="" placeholder="Deixe em branco para manter a senha atual" /> </td>
            </tr>
            <tr>
                <td width="100">Telefone</td>
                <td><input type="text" name="telefone" value="<?= htmlspecialchars((string) $telefone, ENT_QUOTES, 'UTF-8') ?>" /> </td>
            </tr>
            <tr>
                <td width="100">NIF</td>
                <td><input type="text" name="NIF" value="<?= htmlspecialchars((string) $NIF, ENT_QUOTES, 'UTF-8') ?>" /> </td>
            </tr>
            <tr id="permissoes">
                <td width="100">Nivel</td>
                <td>
                    <select style="width:150px" name="nivel" id="nivel">
                        <?php foreach ($levelOptions as $option): ?>
                            <option value="<?= (int) $option['value'] ?>" <?= ((int) $nivel === (int) $option['value'] ? 'selected' : '') ?>><?= htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <input type="submit" name="salvar" value="Salvar" class="btn1" />
        <input type="button" value="Cancelar" class="btn1" onclick="window.location.href='<?= htmlspecialchars(app_url('?acao=usuario_list'), ENT_QUOTES, 'UTF-8') ?>'" />
        <input type="submit" name="excluir" value="Excluir" class="btn1" id="excluir" style="display:inline" />
    </form></div>
</div>


<div class="container_conteudo" style="width:100%; margin-top:10px; box-sizing:border-box;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
        <h4 style="margin:0;">Reservas do utilizador</h4>
        <?php if (!empty($podeEliminarReservas)): ?>
            <form name="eliminar_reservas_form" method="post" action="<?= app_url('?acao=usuario_form') ?>" style="margin:0;">
                <input type="hidden" name="id" value="<?= (int) $id ?>" />
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
                <input type="submit" name="eliminar_reservas" value="Eliminar Reservas" class="btn1" style="height:22px; padding:0 10px; font-size:11px; width:auto;" />
            </form>
        <?php endif; ?>
    </div>
    <?php if (empty($reservasUsuario)): ?>
        <p>Nenhuma reserva encontrada.</p>
    <?php else: ?>
        <?php $statusNomes = [1 => 'Reservado', 2 => 'Confirmado', 3 => 'Cancelado']; ?>
        <div style="width:100%; max-height:55vh; overflow-y:auto; overflow-x:auto;">
            <table class="tabela_comum" cellpadding="0" cellspacing="0" style="width:100%; min-width:600px; font-size:11px;">
                <tr style="border-bottom:1px solid #999;">
                    <th style="padding:0 3px;">ID</th>
                    <th style="padding:0 3px;">Sala</th>
                    <th style="padding:0 3px;">Período</th>
                    <th style="padding:0 3px;">Dia</th>
                    <th style="padding:0 3px;">Disciplina</th>
                    <th style="padding:0 3px;">Status</th>
                    <th style="padding:0 3px;">Eliminar</th>
                </tr>
                <?php foreach ($reservasUsuario as $reserva): ?>
                    <tr style="line-height:1.0;">
                        <td style="padding:0 3px;"><?= (int) $reserva['id'] ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars((string) $reserva['sala'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars((string) $reserva['periodo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars((string) $reserva['dia'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars((string) $reserva['disciplina_desc'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars($statusNomes[(int)$reserva['status']] ?? (string)$reserva['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;">
                            <?php if ($podeEliminarReservas): ?>
                                <form method="post" action="<?= app_url('?acao=usuario_form') ?>" style="display:inline; margin:0;">
                                    <input type="hidden" name="id" value="<?= (int) $id ?>" />
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
                                    <input type="hidden" name="eliminar_reserva" value="1" />
                                    <input type="hidden" name="reserva_id" value="<?= (int) $reserva['id'] ?>" />
                                    <button type="submit" class="btn1" style="width:60px; height:18px; padding:0; font-size:10px;">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <hr style="border:0; border-top:1px solid #999; margin:0;" />
        </div>
    <?php endif; ?>
</div>

<script>
    window.addEventListener('load', function () {
        user_self_edit(nomeSessao, nivelSessao, nomeEditar, nivelEditar);
    });
</script>
