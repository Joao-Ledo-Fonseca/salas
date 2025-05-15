<?php

require_once "seguranca.php";
require_once "../controller/periodoController.php";

$periodoController = new periodoController();
$lista = $periodoController->listarcontroller();

?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="js/jquery.js"></script>
    <script src="js/jquery.datetimepicker.full.js"></script>
    <script src="js/dateformat.js"></script>

    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

    <script src="js/lib.js"></script>

    <title>Cadastro de Períodos</title>
</head>

<body>

    <!-- form -->
    <div class="form">
    </div>

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- conteudo -->
    <div class="corpo">
        <h3>Cadastro de Períodos</h3>
        <div class="container_top lista_comum">
            <div class="form_sel">
                <input type="button" name="novo" value="novo" class="btn1" onclick='abre("periodo_form.php")'>
            </div>
        </div>
        <div class="container_conteudo lista_comum">
            <table style="border-spacing: 4px; padding: 4px;">
                <thead>
                    <tr>
                        <th> Nome </th>
                        <th> Sequência </th>
                        <th> id </th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    <?= $lista ?>
                </tbody>
            </table>

        </div>
</body>

</html>