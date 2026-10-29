<?php $title = 'Cadastro de Categorias'; ?>

<h3>Cadastro de Categorias</h3>

<?php if (!empty($errormsg)): ?>
    <div class="form_sel lista_comum" style="color:#900"><?= htmlspecialchars($errormsg, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="container_conteudo lista_comum" style="max-height:60vh; overflow-y:auto;">
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
                <td><input type="text" name="descricao" value="<?= htmlspecialchars((string) $descricao, ENT_QUOTES, 'UTF-8') ?>" maxlength="255" size="50" /> </td>
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
            <input type="submit" name="cancelar" value="Cancelar" class="btn1" id="cancelar" style="display:inline" onclick="cancelaInputsRequired()" />
        </div>
    </form>
</div>
