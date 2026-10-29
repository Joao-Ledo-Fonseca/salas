<?php $title = 'Cadastro de Salas'; ?>

<h3>Cadastro de Salas</h3><br>Categorias:<?= htmlspecialchars((string)$categoria_filtro, ENT_QUOTES, 'UTF-8') ?> Activas:<?= htmlspecialchars((string)$activas_filtro, ENT_QUOTES, 'UTF-8') ?>

<div class="container">
    <div class="a_par">
        <div class=aviso style="color:red"><?= htmlspecialchars((string)$errormsg, ENT_QUOTES, 'UTF-8') ?></div>

        <form enctype="multipart/form-data" name="form1" method="post" action="<?= htmlspecialchars(app_url('?acao=sala_form&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)), ENT_QUOTES, 'UTF-8') ?>">

            <input type="hidden" name="id" value="<?= (int) $id ?>" />
            <input type="hidden" name="imagem_id" value="<?= htmlspecialchars((string) $imagem_id, ENT_QUOTES, 'UTF-8') ?>" />
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
            <input type="hidden" name="categoria_filtro" value="<?= htmlspecialchars((string) $categoria_filtro, ENT_QUOTES, 'UTF-8') ?>" />
            <input type="hidden" name="activas" value="<?= htmlspecialchars((string) $activas_filtro, ENT_QUOTES, 'UTF-8') ?>" />
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
                <tr>
                    <td width="100px">Foto</td>
                    <td>
                        <div>
                            <label for="newimagem" class="custom-file-upload btn1">Ficheiro</label>
                            <input name="imagem" id="newimagem" type="file" onclick="renderizaNovaImagem()" />
                        </div>
                    </td>
                </tr>
            </table>
            <input type="submit" name="salvar" value="Salvar" class="btn1" />
            <input type="submit" name="excluir" value="Excluir" class="btn1" />
            <input type="submit" name="cancelar" value="Cancelar" class="btn1" id="cancelar" style="float:inline" onclick="cancelaInputsRequired()" />
        </form>
    </div>
    <div class="apar">
        <?php
        echo '<img id="imagem" src="data:image/jpeg;base64,' . base64_encode($imagem) . '"  ' . ((strlen($imagem) > 0) ? "" : "style='display:none'") . ' width=400px />';
        ?>
    </div>
</div>

<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imagem').attr('src', e.target.result);
                $('#imagem').css('display', 'block');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#newimagem").change(function () {
        readURL(this);
    });
</script>
