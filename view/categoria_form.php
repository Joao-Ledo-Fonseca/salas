<?php

require_once "seguranca.php";
require_once "../controller/categoriaController.php";

$categoriaController = new categoriaController();

$categoria = $categoriaController->cancelar();
$categoria = $categoriaController->excluir();
$categoria = $categoriaController->salvar();
$categoria = $categoriaController->abrir();

if (isset($categoria['id']))
    extract($categoria);


if (!isset($id)) {
    $id = 0;
    $nome = '';
    $descricao = '';
    $imagem_id = '';

    $nome_img = '';
    $tamanho_img = '';
    $tipo_img = '';
    $imagem = '';
}

if (isset($_GET['errormsg'])) {
    $errormsg = $_GET['errormsg'];
} else {
    $errormsg = '';
}

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <script src="js/jquery.js"></script>
    <script src="js/jquery.datetimepicker.full.js"></script>
    <script src="js/dateformat.js"></script>

    <!-- <link href="css/select2.min.css" rel="stylesheet" /> -->
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

    <!-- <script src="js/select2.min.js"></script> -->
    <script src="js/lib.js"></script>

</head>

<title>Cadastro de Categorias</title>

<body>

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- conteudo -->

    <div class="corpo">

        <h3> Cadastro de Categorias </h3>
        <div class="container">
            <div class="a_par">

                <form enctype="multipart/form-data" name="form1" method="post" target="_self">

                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <input type="hidden" name="imagem_id" value="<?= $imagem_id ?>" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />

                    <table class="tabela_comum" cellpadding="4" cellspacing="4">

                        <tr>
                            <td width="100"> Nome </td>
                            <td><input type="text" name="nome" value="<?= $nome ?>" /> </td>
                            <td> </td>
                        </tr>
                        <tr>
                            <td width="100"> Descrição </td>
                            <td><input type="text" name="descricao" value="<?= $descricao ?>" /> </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td width="100"> Foto </td>
                            <td>
                                <div>
                                    <label for="newimagem" class="custom-file-upload btn1">Ficheiro</label>
                                    <input name="imagem" id="newimagem" type="file" onclick="renderizaNovaImagem()" />

                                </div>
                            </td>
                            <td></td>
                        </tr>

                    </table>
                    <br>
                    <div class="form_sel lista_comum">
                        <input type="submit" name="salvar" value="Salvar" class="btn1" />
                        <input type="submit" name="excluir" value="Excluir" class="btn1" />
                        <input type="submit" name="cancelar" value="Cancelar" class="btn1" id="cancelar"
                            style="float:inline" onclick="cancelaInputsRequired()" />
                        <span style="color:#900"><?php echo $errormsg; ?></span>
                    </div>

                </form>

            </div>
            <div class="a_par">
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