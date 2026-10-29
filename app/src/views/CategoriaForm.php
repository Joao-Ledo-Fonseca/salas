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
            <input type="button" value="Cancelar" class="btn1" onclick="window.location.href='<?= htmlspecialchars(app_url('?acao=categoria_list'), ENT_QUOTES, 'UTF-8') ?>'" />
        </div>
    </form>
</div>
<div class="container_conteudo" style="width:100%; margin-top:10px; box-sizing:border-box;">
    <h4 style="margin:0 0 4px 0;">Salas da categoria</h4>
    <?php if (empty($salasCategoria)): ?>
        <p>Nenhuma sala encontrada.</p>
    <?php else: ?>
        <div style="width:100%; max-height:55vh; overflow-y:auto; overflow-x:auto;">
            <table class="tabela_comum" cellpadding="0" cellspacing="0" style="width:100%; min-width:500px; font-size:11px;">
                <tr style="border-bottom:1px solid #999;">
                    <th style="padding:2px 3px;">ID</th>
                    <th style="padding:2px 3px;">Nome</th>
                    <th style="padding:2px 3px;">Descrição</th>
                    <th style="padding:2px 3px;">Lugares</th>
                    <th style="padding:2px 3px;">Activa</th>
                </tr>
                <?php foreach ($salasCategoria as $sala): ?>
                    <tr style="line-height:1.4;">
                        <td style="padding:2px 3px; min-height:18px;"><?= (int) $sala['id'] ?></td>
                        <td style="padding:2px 3px; min-height:18px;"><a href="<?= htmlspecialchars(app_url('?acao=sala_form&id=' . (int) $sala['id'] . '&categoria_filtro=' . rawurlencode((string) $id) . '&activas=activas&return_to=categoria_form&return_id=' . (int) $id), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $sala['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                        <td style="padding:2px 3px; min-height:18px;"><?= htmlspecialchars((string) $sala['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:2px 3px; min-height:18px;"><?= (int) $sala['lugares'] ?></td>
                        <td style="padding:2px 3px; min-height:18px;"><?= ((int) $sala['activa'] === 1 ? 'Sim' : 'Não') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <hr style="border:0; border-top:1px solid #999; margin:0;" />
        </div>
    <?php endif; ?>
</div>


