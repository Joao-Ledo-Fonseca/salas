<!DOCTYPE html>   
<html lang="pt-PT">


<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">    

    <script src="<?= app_url('/js/jquery.js') ?>"></script>
    <script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>
    <script src="<?= app_url('/js/dateformat.js') ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">

    <script src="<?= app_url('/js/lib.js') ?>"></script>

</head>

<title>Cadastro de salas</title>

<body>


    <!-- menu esquerdo -->
    <?php include __DIR__ . "/MenuEsquerdo.php"; ?>

    <!-- conteudo -->
    <div class="corpo">

        <h3> Cadastro de Salas </h3><br>Categorias:<?= $categoria_filtro ?> Activas:<?= $activas_filtro ?>

        <div class="container">
            <div class="a_par">
                <div class=aviso style="color:red"><?= $errormsg ?></div>

                <form enctype="multipart/form-data" name="form1" method="post" action="<?= htmlspecialchars(app_url('?acao=sala_form&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)), ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <input type="hidden" name="imagem_id" value="<?= $imagem_id ?>" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                    <input type="hidden" name="categoria_filtro" value= <?= $categoria_filtro ?> />
                    <input type="hidden" name="activas" value= <?= $activas_filtro ?> />

                    <table class="tabela_comum" cellpadding="4" cellspacing="4">
                        <tr>
                            <td width="100px"> Nome </td>
                            <td><input type="text" name="nome" value="<?= $nome ?>" /> </td>
                        </tr>
                        <tr>
                            <td width="100px"> Descrição </td>
                            <td><input type="text" name="descricao" value="<?= $descricao ?>" /> </td>                            
                        </tr>
                        <tr>
                            <td width="100px"> Categoria </td>
                            <td>
                                <select name="categoria_id" style="width:180px" id="selecao">
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= (int) $categoria['id'] ?>" <?= ((int) $categoria['id'] === (int) $categoria_id ? 'selected' : '') ?>><?= htmlspecialchars((string) $categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>                            
                        </tr>
                        <tr>
                            <td width="100px"> Nº Lugares </td>
                            <td><input type="text" name="lugares" value="<?= $lugares ?>" /> </td>                            
                        </tr>
                        <tr>
                            <td width="100px"> Activa </td>
                            <td><input type="checkbox" name='activa' value=activa <?= ($activa ? "checked" : "") ?> >
                            </td>

                        </tr>
                        <tr>
                            <td width="100px"> Foto </td>
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
                    <input type="submit" name="cancelar" value="Cancelar" class="btn1" id="cancelar"
                        style="float:inline" onclick="cancelaInputsRequired()" />

                </form>
            </div>
            <div class="apar">
                <?php
                echo '<img id="imagem" src="data:image/jpeg;base64,' . base64_encode($imagem) . '"  ' . ((strlen($imagem) > 0) ? "" : "style='display:none'") . 'width=400px ?>';
                ?>
            </div>
        </div>
    </div>

</body>
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

</html>
