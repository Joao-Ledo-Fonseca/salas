<!DOCTYPE html>
<html lang="pt-PT">
    
<?php

require_once "seguranca.php";
require_once "../controller/usuarioController.php";

$usuarioController = new UsuarioController();
$lista = $usuarioController->listarcontroller();

?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Scripts -->
    <script src="js/jquery.js"></script>
    <script src="js/jquery.datetimepicker.full.js"></script>
    <script src="js/dateformat.js"></script>
    <script src="js/lib.js"></script>

    <!-- Estilos -->
    <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="css/estilo.css">

    <title>Lista de Utilizadores</title>
    
</head>

<body>

    <!-- form -->
    <div class="form"></div>

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>
    
    <!-- conteudo -->
    <div class="corpo">

        <h3>Lista de Utilizadores</h3>
        
        <div class="lista_comum container_top">
            <form class="form_sel" name="form1" method="post" target="_self">
                <div class="form_sel">
                    <input type="button" name="novo" value="Novo" class="btn1" onclick="abre('usuario_form.php')" />
                </div>
            </form>
        </div>
        
        <div class="container_conteudo">

            <table class="lista_comum" cellpadding="4" cellspacing="4">
                <thead>
                    <tr>
                        <th width="150px">Nome</th>
                        <th width="150px">E-mail</th>
                        <th width="100px">Nível</th>
                        <th width="50px">ID</th>
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