<?php

require_once "seguranca.php";
require_once "../controller/salaController.php";
require_once "../controller/categoriaController.php";

// Obtém a seleção de categoria
$selecao = isset($_POST['selecao']) ? Util::clearparam($_POST['selecao']) : 'todas';

// Controladores
$categoriaController = new categoriaController();
$categorias = $categoriaController->listarController('nomes');

$salaController = new salaController();
$lista = $salaController->listarcontroller($selecao);

?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Salas</title>

    <!-- Scripts -->
    <script src="js/jquery.js"></script>
    <script src="js/jquery.datetimepicker.full.js"></script>
    <script src="js/dateformat.js"></script>
    <script src="js/lib.js"></script>

    <!-- Estilos -->
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/estilo.css">
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
                           onclick="abre('sala_form.php?categoria=<?= $selecao ?>')" />
                </div>

                <span style="float:right;">
                    <label for="selecao"><b>Categorias</b></label>
                    <select name="selecao" id="selecao" style="width:200px;" onchange="this.form.submit()">
                        
                    
                        <option value="todas" <?= $selecao === 'todas' ? 'selected' : '' ?>>Todas</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['nome'] ?>" <?= $cat['nome'] === $selecao ? 'selected' : '' ?>>
                                <?= $cat['nome'] ?>
                            </option>
                        <?php endforeach; ?>
                        
                    </select>
                </span>
            </form>
        </div>

        <div class="container_conteudo">
            <table class="lista_comum" cellpadding="4" cellspacing="4">
                <thead>
                    <tr>
                        <th width="150px">Categoria</th>
                        <th width="150px">Nome</th>
                        <th width="200px">Descrição</th>
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