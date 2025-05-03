<?php

require_once "seguranca.php";
require_once "../controller/categoriaController.php";

$categoriaController = new categoriaController();

$categoria = $categoriaController->excluir();
$categoria = $categoriaController->salvar();
$categoria = $categoriaController->abrir();

if (isset($categoria[0]))
    extract($categoria[0]);


if (!isset($id)) {
    $id = 0;
    $nome = '';
    $descricao = '';
    $nome_img = '';
    $tamanho_img = '';
    $tipo_img = '';
    $imagem = '';
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

        <div class="apar" style="float:left">

            <form enctype="multipart/form-data" name="form1" method="post" target="_self">

                <input type="hidden" name="id" value="<?= $id ?>" />
                <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />

                <table class="tabela_comum" cellpadding="4" cellspacing="4">

                    <tr>
                        <td width="100"> Nome </td>
                        <td><input type="text" name="nome" value="<?= $nome ?>" /> </td>
                        <td width="30"></td>
                        <td width="100"> </td>
                        <td> </td>
                    </tr>
                    <tr>
                        <td width="100"> Descrição </td>
                        <td><input type="text" name="descricao" value="<?= $descricao ?>" /> </td>
                        <td width="30"></td>
                        <td width="100"> </td>
                        <td> </td>
                    </tr>
                    <tr>
                        <td width="100"> Foto </td>
                        <td>
                            <div><input name="imagem" type="file" /></div>
                        </td>
                        <td width="30"></td>
                        <td width="100"> </td>
                        <td> </td>
                    </tr>

                </table>

                <input type="submit" name="salvar" value="Salvar" class="btn1" />
                <input type="submit" name="excluir" value="Excluir" class="btn1" />

            </form>

        </div>
        <div class="apar" style="float:right">
            <?php
            if (strlen($imagem) > 0) {
                echo '<img src="data:image/jpeg;base64,' . base64_encode($imagem) . '"  width=100% />';
                // echo '<img src="data:'.$tipo_imagem.';base64,'. base64_encode( $imagem ) .'" width=100% />';
                //echo '<img src="data:'.$tipo_imagem.';base64,'. base64_encode( $imagem )  .'" width=100% />';            
            }

            ?>
        </div>
    </div>

</body>

</html>