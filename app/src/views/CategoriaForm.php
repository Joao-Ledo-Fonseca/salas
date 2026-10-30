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

 <?php $title = 'Cadastro de Categorias'; ?>

<h3>Cadastro de Categorias</h3>

<?php if (!empty($errormsg)): ?>
    <div class="form_sel lista_comum text-error"><?= htmlspecialchars($errormsg, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="container_conteudo lista_comum scroll-y-60">
    <form name="form1" method="post" action="<?= app_url('?acao=categoria_form') ?>" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?= (int) $id ?>" />
        <input type="hidden" name="imagem_id" value="<?= htmlspecialchars((string) $imagem_id, ENT_QUOTES, 'UTF-8') ?>" />
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />

        <table class="tabela_comum" cellpadding="4" cellspacing="4">
            <tr>
                <td width="100">Nome</td>
                <td><input type="text" name="nome" value="<?= htmlspecialchars((string) $nome, ENT_QUOTES, 'UTF-8') ?>" maxlength="15" size="30" /> </td>
            </tr>
            <tr>
                <td width="100">Descrição</td>
                <td><input type="text" name="descricao" value="<?= htmlspecialchars((string) ($descricao ?? ''), ENT_QUOTES, 'UTF-8') ?>" maxlength="255" size="50" /> </td>
            </tr>
            <tr>
                <td width="100">Imagem</td>
                <td><input type="file" name="imagem" /> </td>
            </tr>
        </table>

        <br>
        <div class="form_sel lista_comum">
            <input type="submit" name="salvar" value="Salvar" class="btn1" />
            <input type="submit" name="excluir" value="Excluir" class="btn1" />
            <input type="button" value="Cancelar" class="btn1" onclick="window.location.href='<?= htmlspecialchars(app_url('?acao=categoria_list'), ENT_QUOTES, 'UTF-8') ?>'" />
        </div>
    </form>
</div>
<div class="container_conteudo form-section">
    <h4 class="mt-0-mb-4">Salas da categoria</h4>
    <?php if (empty($salasCategoria)): ?>
        <p>Nenhuma sala encontrada.</p>
    <?php else: ?>
        <div class="scroll-xy">
            <table class="tabela_comum compact-table" cellpadding="0" cellspacing="0">
                <tr class="border-bottom-999">
                    <th class="p-compact">ID</th>
                    <th class="p-compact">Nome</th>
                    <th class="p-compact">Descrição</th>
                    <th class="p-compact">Lugares</th>
                    <th class="p-compact">Activa</th>
                </tr>
                <?php foreach ($salasCategoria as $sala): ?>
                    <tr class="lh-compact">
                        <td class="p-compact minh-18"><?= (int) $sala['id'] ?></td>
                        <td class="p-compact minh-18"><a href="<?= htmlspecialchars(app_url('?acao=sala_form&id=' . (int) $sala['id'] . '&categoria_filtro=' . rawurlencode((string) $id) . '&activas=activas&return_to=categoria_form&return_id=' . (int) $id), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $sala['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                        <td class="p-compact minh-18"><?= htmlspecialchars((string) $sala['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="p-compact minh-18"><?= (int) $sala['lugares'] ?></td>
                        <td class="p-compact minh-18"><?= ((int) $sala['activa'] === 1 ? 'Sim' : 'Não') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <hr class="hr-compact" />
        </div>
    <?php endif; ?>
</div>


