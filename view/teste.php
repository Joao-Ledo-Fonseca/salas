<?php
require_once "seguranca.php";
//require_once("../controller/dashboardController.php");

require_once "../controller/salaController.php";
require_once "../controller/categoriaController.php";


//$dsc = new dashboardController();

// Obtém a seleção de categoria
$selecao = 'todas';

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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <!--
    <link rel="stylesheet" href="./css/estilo.css">
    <link rel="stylesheet" href="styles.css">
    -->

    <style>
        
       body{ padding:0px; margin:0px;font-family: 'Open Sans', sans-serif; color:#4E4E4E; overflow:hidden; background-color:#FFFFFF; }
        h3 { font-weight:300; }

        /* Menu-Esquerdo */
.menu  {  background-color:#464646; font-size:14px; width:130px;  height:100%; float:left; padding-top:100px;  }
.menu .user {color:#FFFFFF;padding-left:15px; padding-bottom: 20px;} 
.menu hr {border: 0px solid #FFFFFF;}
.menu ul{ padding:0px; margin:0px; }
.menu li {  padding:15px; margin:0px; color:#FFFFFF;  list-style:none;  transition: background-color 0.7s ease, color 0.7s ease;  background-color:#575757; cursor:pointer; }
.menu li:hover, .dropdown-btn:hover { background-color:#D4D4D4; }
.menu a{ color:#FFFFFF; text-decoration:none; }
.menu li:hover a{color:#3B3B3B; }
.hamb { display:none;}
        
  
        
        
        .corpo {width:100%-100px;float:left;margin: 0px 10px 10px};
        .container_conteudo {width:100%;display:block;height:auto}
        .container_top {width:100%;display:block;height:auto;padding: 0px 0px 30px; }      

        .lista_comum {}
        .tabela_comum {}

        .form {}
        .form_sel {width:100%;display:block;clear:both;height:auto}    
        
        table {}
;
        * {border: 1px solid black;};
        .menu * {border: 0px;}
    </style>

</head>

<body>


    <!-- Menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- Formulário -->
    <div class="form"></div>

    <!-- Conteúdo principal -->
    <div class="corpo">
        <h3>Cadastro de Salas</h3>

        <div class="form_sel">
            <form name="form1" method="post" target="_self">
                <span style="float:left;">
                    <input type="button" name="novo" value="Novo" class="btn1"
                        onclick="abre('sala_form.php?categoria=<?= $selecao ?>')" />
                </span>

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
                        <th width="50px">Activa</th>
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