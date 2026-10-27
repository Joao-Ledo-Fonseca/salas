<!DOCTYPE html>
<html lang="pt-PT">

<?php

define('REQUIRED_PERMISSION', 'M_Permissoes');
require_once "seguranca.php";
require_once __DIR__ . "/../app/controllers/permissoesController.php";

$permissoesController = new permissoesController();
$permissoesController->cancelar();
$permissoes = $permissoesController->salvar();
$tabela_n = $permissoesController->listar('n'); // Permissoes por nivel
$tabela_s = $permissoesController->listar('s'); // Configurações simples


?>


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <script src="<?= app_url('/js/jquery.js') ?>"></script>
    <script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>
    <script src="<?= app_url('/js/dateformat.js') ?>"></script>

    <!-- <link href="<?= app_url('/css/select2.min.css') ?>" rel="stylesheet" /> -->
    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">

    <!-- <script src="<?= app_url('/js/select2.min.js') ?>"></script> -->
    <script src="<?= app_url('/js/lib.js') ?>"></script>
   
    <title>Permissoes</title>

</head>


<body>

    <!-- Formulário -->
    <div class="form"></div><br>

    <!-- Menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- Conteúdo principal -->
    <div class="corpo">
        <h3>Permissões</h3>

        <div class="container_conteudo">
            <!-- 1. O FORM ABRE NO TOPO -->
            <form name="form1" method="post" action="<?= app_url('?acao=permissoes_form') ?>">

                <div class="lista_comum">
                    <!-- Tabela de Níveis -->
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

                    <!-- Tabela de Configurações Simples -->
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
                </div> <!-- Fecha a lista_comum das tabelas -->

                <!-- 🎯 NOVA ESTRUTURA: Esta div principal está SEMPRE VISÍVEL -->
                <div class="lista_comum">
                    <div class="form_sel" style="display: flex; gap: 10px;">

                        <!-- O botão Voltar fica FORA do bloco oculto, logo estará SEMPRE VISÍVEL -->
                        <input type="submit" name="cancelar" value="Voltar" class="btn1" id="cancelar"
                            onclick="cancelaInputsRequired()" />

                        <!-- O botão Salvar fica dentro de um bloco isolado que começa OCULTO -->
                        <div id="salvar" style="display:none">
                            <input type="submit" name="salvar" value="Salvar" class="btn1" />
                        </div>



                    </div>
                </div>

                <!-- 3. O FORM FECHA AQUI -->
            </form>
        </div> <!-- Fecha o container_conteudo -->
    </div>

</body>

</html>