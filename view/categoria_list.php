<?php

require_once "seguranca.php";
require_once "../controller/categoriaController.php";

$categoriaController = new categoriaController();
$lista = $categoriaController->listarcontroller();

?>
<!DOCTYPE html>   
<html lang="pt-PT">

<head>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">   

   <script src="js/jquery.js"></script>
   <script src="js/jquery.datetimepicker.full.js"></script>
   <script src="js/dateformat.js"></script>

   <link rel="stylesheet" type="text/css" href="css/estilo.css">
   <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

   <script src="js/lib.js"></script>
   <title>Cadastro de Categorias</title>

</head>

<body>

   <!-- form -->
   <div class="form">
   </div>


   <!-- menu esquerdo -->
   <?php include "menu_esquerdo.php"; ?>

   <!-- conteudo -->

   <div class="corpo">
      <h3> Cadastro de Categorias </h3>
      <div class="container_top lista_comum">
         <form class="form_sel" name="form1" method="post" target="_self">
            <span style="float:left;">
               <input type="button" name="novo" value="novo" class="btn1" onclick="abre('categoria_form.php')">
            </span>
      </div>

      <div class="container_conteudo lista_comum">
         <table cellpadding="4" cellspacing="4">
            <thead>
               <tr>
                  <th> Nome </th>
                  <th> Descrição </th>
                  <th> id </th>
                  <th></th>
               </tr>
            </thead>
            <tbody>
               <?= $lista ?>
            </tbody>
         </table>

      </div>
</body>

</html>