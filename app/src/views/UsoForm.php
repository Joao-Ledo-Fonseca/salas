<?php
use Salas\Infrastructure\Support\Csrf;
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
use Salas\Infrastructure\Support\Util;
use Salas\Infrastructure\Validation\Validator;
use Salas\Application\Services\AuthorizationService;
?>

 $title = 'Cadastro de Usos'; ?>

<h3>Cadastro de Usos</h3>

<?php if (!empty($errormsg)): ?>
    <div class="form_sel lista_comum" style="color:#900"><?= htmlspecialchars($errormsg, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="container_conteudo lista_comum" style="max-height:60vh; overflow-y:auto;">
    <form name="form1" method="post" action="<?= app_url('?acao=uso_form') ?>">

        <input type="hidden" name="id" value="<?= (int) $id ?>" />
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />

        <table class="tabela_comum" cellpadding="4" cellspacing="4">
            <tr>
                <td width="100">Nome</td>
                <td><input type="text" name="nome" value="<?= htmlspecialchars((string) $nome, ENT_QUOTES, 'UTF-8') ?>" maxlength="100" size="30" required /> </td>
            </tr>
            <tr>
                <td width="100">Descrição</td>
                <td><input type="text" name="descricao" value="<?= htmlspecialchars((string) $descricao, ENT_QUOTES, 'UTF-8') ?>" maxlength="255" size="50" /> </td>
            </tr>
        </table>

        <br>
        <div class="form_sel lista_comum">
            <input type="submit" name="salvar" value="Salvar" class="btn1" />
            <input type="submit" name="excluir" value="Excluir" class="btn1" />
            <input type="submit" name="cancelar" value="Cancelar" class="btn1" id="cancelar" style="display:inline" onclick="cancelaInputsRequired()" />
        </div>
    </form>
</div>
