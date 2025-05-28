<!DOCTYPE html>
<html lang="pt-PT">


<?php

require_once "seguranca.php";
require_once "../controller/util.php";
require_once "../controller/salaController.php";
require_once "../controller/categoriaController.php";

// Obtém a seleção de categoria e activas
$categoria_id = (isset($_POST['categoria_id']) ? (int) Util::clearparam($_POST['categoria_id']) : 0 );
$activas = (isset($_POST['activas']) ? Util::clearparam($_POST['activas']) : 'activas') ;   


// Controladores
$categoriaController = new categoriaController();
$categorias = $categoriaController->optionsCategoria(true, $categoria_id );

$salaController = new salaController();
$lista = $salaController->listarcontroller($categoria_id, $activas);

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
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/estilo.css">

    <title>Cadastro de Salas</title>
</head>

<body>
    <!-- Formulário -->
    <div class="form"></div>

    <!-- Menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- Conteúdo principal -->
    <div class="corpo">
        <h3>Cadastro de Salas</h3>

        <div class="lista_comum container_top">            
            <form class="form_sel" name="form1" method="post" target="_self">
                <div style="float:left;">
                    <input type="button" name="novo" value="Novo" class="btn1" 
                           onclick="abre('sala_form.php?categoria_filtro=<?= $categoria_id ?>')" />
                </div>
                   
                <span style="float:right;">
                    <label for="categoria"><b>Categorias</b></label>
                    <select id="categoria" style="width:200px;" name="categoria_id" onchange="this.form.submit()">                                            
                        
                        <?= $categorias ?>
                        
                    </select>
                    
                </span>

                <div style="float:right; width:140px">                    
                    <input type="radio" id="activa" name="activas" value="activas" <?= ($activas=="activas"?'checked':'') ?> onchange="this.form.submit()">
                    <label for="activa">Só activas</label><br>
                    <input type="radio" id="inactiva" name="activas" value="inactivas" <?= ($activas=="inactivas"?'checked':'') ?> onchange="this.form.submit()">
                    <label for="inactiva">Inactivas</label><br>
                    <input type="radio" id="todas" name="activas" value="todas" <?= ($activas=="todas"?'checked':'') ?> onchange="this.form.submit()">
                    <label for="todas">Todas</label><br>                    
                </div>
            </form>
        </div>

        <div class="container_conteudo">
            <table class="lista_comum" cellpadding="4" cellspacing="4">
                <thead>
                    <tr>
                        <th width="150px">Categoria</th>
                        <th width="150px">Nome</th>
                        <th width="200px">Descrição</th>
                        <th width="200px">Activa</th>
                        <th width="50px">ID</th>
                        <th> </th>
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