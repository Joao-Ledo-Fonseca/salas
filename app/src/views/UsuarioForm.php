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

 <?php $title = 'Perfil de utilizador'; ?>

<script>
    nomeSessao = <?= json_encode($sessionUserName) ?>;
    nivelSessao = <?= $sessionUserLevel ?>;
    nomeEditar = <?= json_encode((string) $nome) ?>;
    nivelEditar = <?= (int) $nivel ?>;

    /**
     * Método user_self_edit.
     * @param uSessa $o
     * @param nSessa $o
     * @param uEdita $r
     * @param nEdita $r
     * @return mixed
     */
    function user_self_edit(uSessao, nSessao, uEditar, nEditar) {
        if (uEditar == uSessao || nEditar > nSessao || nSessao == 0) {
            document.getElementById("permissoes").style.display = "none";
            document.getElementById("excluir").style.display = "none";
        }
    }
</script>

<h3>Perfil de utilizador</h3>

<div class="container_conteudo lista_comum">
    <div class="aviso text-error"><?= htmlspecialchars((string)$errormsg, ENT_QUOTES, 'UTF-8') ?></div>

    <div class="form-col">
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
                    <select class="w-150" name="nivel" id="nivel">
                        <?php foreach ($levelOptions as $option): ?>
                            <option value="<?= (int) $option['value'] ?>" <?= ((int) $nivel === (int) $option['value'] ? 'selected' : '') ?>><?= htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <input type="submit" name="salvar" value="Salvar" class="btn1" />
        <input type="button" value="Cancelar" class="btn1" onclick="window.location.href='<?= htmlspecialchars(app_url(!empty($isSelf) ? '' : '?acao=usuario_list'), ENT_QUOTES, 'UTF-8') ?>'" />
        <input type="submit" name="excluir" value="Excluir" class="btn1 inline-form" id="excluir" />
    </form></div>
</div>


<div class="container_conteudo form-section">
    <div class="flex-between mb-4">
        <h4 class="m-0">Reservas do utilizador</h4>
        <?php if (!empty($podeEliminarReservas)): ?>
            <form name="eliminar_reservas_form" method="post" action="<?= app_url('?acao=usuario_form') ?>" class="m-0">
                <input type="hidden" name="id" value="<?= (int) $id ?>" />
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
                <input type="submit" name="eliminar_reservas" value="Eliminar Reservas" class="btn1 btn-compact" />
            </form>
        <?php endif; ?>
    </div>
    <?php
    $reservasAssociadas = $reservasUsuario;
    $mostrarUtilizador = false;
    $urlEliminarReserva = app_url('?acao=usuario_form&id=' . (int) $id);
    $camposEliminarReserva = ['id' => (int) $id];
    include __DIR__ . '/components/ReservasAssociadas.php';
    ?>
</div>

<script>
    window.addEventListener('load', function () {
        user_self_edit(nomeSessao, nivelSessao, nomeEditar, nivelEditar);
    });
</script>
