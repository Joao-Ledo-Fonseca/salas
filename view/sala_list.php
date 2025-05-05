<?php

require_once "seguranca.php";
require_once "../controller/salaController.php";
require_once "../controller/categoriaController.php";

if (isset($_POST['selecao'])) {
   $selecao = Util::clearparam($_POST['selecao']);
} else {
   $selecao = 'todas';
}

$categoriaController = new categoriaController();
$categorias = $categoriaController->listarController('nomes');

$salaController = new salaController();
$lista = $salaController->listarcontroller($selecao);

?>


<!DOCTYPE html
   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

   <script src="js/jquery.js"></script>
   <script src="js/jquery.datetimepicker.full.js"></script>
   <script src="js/dateformat.js"></script>

   <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">
   <link rel="stylesheet" type="text/css" href="css/estilo.css">


   <script src="js/lib.js"></script>

   <title>Cadastro de Salas</title>

</head>



<body>

   <!-- form -->
   <div class="form">

   </div>

   <!-- menu esquerdo -->
   <?php include "menu_esquerdo.php"; ?>

   <!-- conteudo -->
   <div class="corpo">

      <h3> Cadastro de Salas </h3>

      <div class="container">

         <div class="apar">
            <input type="button" name="novo" value="novo" class="btn1" onclick="abre('sala_form.php')" />
         </div>

         <div class="apar">
            <form name="form1" method="post" width=300px target="_self">
               <select name="selecao" style="width:200px" id="selecao" onchange="this.form.submit()">
                  <option value="todas" <?= (($selecao == 'todas') ? 'selected' : '') ?>>Todas</option>
                  <?php
                  foreach ($categorias as $nome) {
                     echo '<option value="' . $nome . '"  ' . (($nome == $selecao) ? 'selected' : ' ') . ' > ' . $nome . '</option>';
                  }
                  ?>
               </select>
            </form>
         </div>
      </div>


      <div class="container">

         <div>
            <table class="lista_comum" cellpadding="4" cellspacing="4">
               <thead>

                  <tr>
                     <th> id </th>
                     <th> Nome </th>
                     <th> Descrição </th>
                     <th> Categoria </th>
                  </tr>

               </thead>

               <tbody>

                  <?= $lista ?>

               </tbody>

            </table>
         </div>

      </div>



   </div>
</body>

</html>