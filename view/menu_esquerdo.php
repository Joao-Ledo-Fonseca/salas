<?PHP
require_once "../controller/permissoesController.php";
$pc = new permissoesController();
?>

<style>
   /* Dropdown container (hidden by default). 
      Optional: add a lighter background color and some left padding 
      to change the design of the dropdown content */

   .dropdown-container {
      display: none;
      background-color: #262626;
      padding-left: 8px;
   }

   /* Optional: Style the caret down icon */
   .fa-caret-down {
      float: right;
      /*
      padding-right: 8px;
      */
   }
</style>

<div class="menu">

   <div class="user">

      <?PHP


      $u_n = $_SESSION['user_nivel'];
      $id_u = $_SESSION['user_nome'] . ' ';           
      $id_u .= '(' . $pc->nomeNivel($u_n, 1) . ')';     // 1-para mostrar abreviatura do nível
      $id_u = '<a href="usuario_form.php?id=' . $_SESSION['user_id'] . '">' . $id_u . '</a>';
      ?>

      <img src="img/do-utilizador-branco.png" width="20" height="20" alt="" />
      <p><?= $id_u ?></p>
      <!-- &ensp; -->
   </div>

   <div>
      
      <li><a href="index.php">Home</a></li>

      <?php

      if ($pc->validaPermissao("M_Estatisticas", $u_n)) {
         echo '<li><a href="relatorios.php">Estatisticas</a></li>';
      }

      if ($pc->validaPermissao("M_Relatorios", $u_n)) {
         echo '<li><a href="relatorios2.php">Relatorios</a></li>';
      }
      ?>


      <li><a class="dropdown-btn" href="#">Admin <i class="fa fa-caret-down"></i> </a></li>
      <div class="dropdown-container">
         <?PHP
                  if ($pc->validaPermissao("M_Categorias", $u_n)) {
                     echo '<li><a href="categoria_list.php">Categorias</a></li>';
                  }
         if ($pc->validaPermissao("M_Salas", $u_n)) {
            echo '<li><a href="sala_list.php">Salas</a></li>';
         }
         if ($pc->validaPermissao("M_Periodos", $u_n)) {
            echo '<li><a href="periodo_list.php">Períodos</a></li>';
         }
         if ($pc->validaPermissao("M_Utilizadores", $u_n)) {
            echo '<li><a href="usuario_list.php">Utilizadores</a></li>';
         }
         if ($pc->validaPermissao("M_Permissoes", $u_n)) {
            echo '<li><a href="permissoes_form.php">Permissões</a></li>';
         }
         ?>
      </div>

      <hr />
      <li><a href="logout.php">Sair</a></li>
      </ul>
   </div>
</div>


<script>
   /* Loop through all dropdowns, to toggle between hiding and showing its dropdown content
      This allows to have multiple dropdowns without any conflict */
   var dropdown = document.getElementsByClassName("dropdown-btn");
   var i;

   for (i = 0; i < dropdown.length; i++) {

      let dentro = dropdown[i].parentNode.nextElementSibling.childNodes;

      /* Havendo um elemento <li></li> o array fica com três elementos, pois há sempre um elemento text antes e depois do <li> */
      if (dentro.length < 3) {
         dropdown[i].parentNode.style.display = "none";
      };

      dropdown[i].parentNode.addEventListener("click", function () {
         this.classList.toggle("active");
         var dropdownContent = this.nextElementSibling;
         if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
         } else {
            dropdownContent.style.display = "block";
         }
      });
   };

</script>