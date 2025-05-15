<?php

require_once "seguranca.php";
require "../controller/usuarioController.php";

$usuarioController = new UsuarioController();
$lista = $usuarioController->listarcontroller();

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <script src="js/jquery.js"></script>
    <script src="js/jquery.datetimepicker.full.js"></script>
    <script src="js/dateformat.js"></script>

    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

    <script src="js/lib.js"></script>
    <title>Lista de Utilizadores</title>
</head>


<body>

    <!-- form -->
    <div class="form">

    </div>

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- conteudo -->
    <div class="corpo">

        <h3>Lista de Utilizadores</h3>

        <div class="container_top lista_comum">
            <div class="form_sel">
                <input type="button" name="novo" value="novo" class="btn1" onclick="abre('usuario_form.php')" />
            </div>
        </div>
        <div class="container_conteudo lista_comum">

            <table cellpadding="4" cellspacing="4">
                <thead>
                    <tr>
                        <th width="150px"> Nome </th>
                        <th> E-mail </th>
                        <th width="100px"> Nível</th>
                        <th width="50px"> id </thwidth>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    <?= $lista ?>

                </tbody>

            </table>
        </div>
    </div>

</body>

</html>