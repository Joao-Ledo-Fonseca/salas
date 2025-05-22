<?php

require_once "seguranca.php";
require_once "../controller/permissoesController.php";

$permissoesController = new permissoesController();
$permissoes = $permissoesController->salvar();
$tabela_n = $permissoesController->listar('n'); // Permissoes por nivel
$tabela_s = $permissoesController->listar('s'); // Configurações simples


?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">   


    <script src="js/jquery.js"></script>
    <script src="js/jquery.datetimepicker.full.js"></script>
    <script src="js/dateformat.js"></script>

    <!-- <link href="css/select2.min.css" rel="stylesheet" /> -->
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

    <!-- <script src="js/select2.min.js"></script> -->
    <script src="js/lib.js"></script>

    <style>
        .hidden {visibility: hidden}
        .show:hover > .hidden {visibility: visible}
    </style>

    <title>Permissoes</title>

</head>


<body>

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- Formulário -->
    <div class="form"></div>

    <!-- conteudo Principal-->
    <div class="corpo">
        <h3>Permissões</h3>

        <div class="container_conteudo">
            <div class="lista_comum">
                <form name="form1" method="post" target="_self">
                    <table cellpadding="4" cellspacing="4">
                        <thead>
                            <tr>
                                <th style="display:none"></th>
                                <th style="width:200px;">Nome</th>
                                <th style="width:60px;"><?= $permissoesController->nomeNivel(0, 2) ?></th>
                                <th style="width:60px;"><?= $permissoesController->nomeNivel(1, 2) ?></th>
                                <th style="width:60px;"><?= $permissoesController->nomeNivel(2, 2) ?></th>
                                <th style="width:100px;"><?= $permissoesController->nomeNivel(3, 2) ?></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $tabela_n ?>
                        </tbody>
                    </table>

                    <table cellpadding="4" cellspacing="4">
                        <thead>
                            <tr>
                                <th style="display:none"></th>
                                <th style="width:200px;">Nome</th>
                                <th style="width:60px;">Activar</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $tabela_s ?>
                        </tbody>
                    </table>
            </div>

            <div class="lista_comum" id="salvar" style="display:none">
                <div class="form_sel ">
                    <input type="submit" name="salvar" value="Salvar" class="btn1" />
                </div>
            </div>
            

            </form>
        </div>
    </div>

</body>

</html>