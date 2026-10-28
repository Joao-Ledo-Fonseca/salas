<?php

?>
<!DOCTYPE html>   
<html lang="pt-PT">

<head>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">   

   <script src="<?= app_url('/js/jquery.js') ?>"></script>
   <script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>
   <script src="<?= app_url('/js/dateformat.js') ?>"></script>

   <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
   <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">

   <script src="<?= app_url('/js/lib.js') ?>"></script>
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
               <?php foreach ($lista as $categoria): ?>
                  <tr>
                     <td><a href="<?= htmlspecialchars(app_url('?acao=categoria_form&id=' . (int) $categoria['id']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $categoria['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                     <td><?= htmlspecialchars((string) $categoria['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                     <td><?= (int) $categoria['id'] ?></td>
                     <td></td>
                  </tr>
               <?php endforeach; ?>
            </tbody>
         </table>

      </div>
</body>

</html>
