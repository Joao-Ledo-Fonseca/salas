<?php

require_once "seguranca.php";
require_once "../controller/permissoesController.php";

$permissoesController = new permissoesController();
$permissoes = $permissoesController->salvar();
$tabela     = $permissoesController->listar();


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
<title>Permissoes</title>

<body>

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- conteudo -->
    <div class="corpo">

        <h3>Permissões</h3>

        <form name="form1" method="post" target="_self">

            <table class="tabela_comum" cellpadding="1" cellspacing="1">
                <thead>
                    <tr>
                        <th style="width:200px;">Nome</th>
                        <th style="width:70px">User</th>
                        <th style="width=70px">Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?PHP
                    echo $tabela
                        ?>
                </tbody>
            </table>
            <div id="salvar" style="display:none">
                <input type="submit" name="salvar" value="Salvar" class="btn1" />
            </div>
        </form>

    </div>

</body>

</html>