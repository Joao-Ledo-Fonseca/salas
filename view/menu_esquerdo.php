<?PHP
require_once "../controller/permissoesController.php";
$pc = new permissoesController();
?>

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

      <a href="index.php"><li>Home</li></a>

      <?php

      if ($pc->validaPermissao("M_Estatisticas", $u_n)) {
         echo '<a href="relatorios.php"><li>Estatisticas</li></a>';
      }

      if ($pc->validaPermissao("M_Relatorios", $u_n)) {
         echo '<a href="relatorios2.php"><li>Relatorios</li></a>';
      }
      ?>

      
      <a class="dropdown-btn" href="#"><li>Admin &#9660;</li></a>
      
      <div class="dropdown-container">
         <?PHP
         if ($pc->validaPermissao("M_Categorias", $u_n)) {
            echo '<a href="categoria_list.php"><li>Categorias</li></a>';
         }
         if ($pc->validaPermissao("M_Salas", $u_n)) {
            echo '<a href="sala_list.php"><li>Salas</li></a>';
         }
         if ($pc->validaPermissao("M_Periodos", $u_n)) {
            echo '<a href="periodo_list.php"><li>Períodos</li></a>';
         }
         if ($pc->validaPermissao("M_Utilizadores", $u_n)) {
            echo '<a href="usuario_list.php"><li>Utilizadores</li></a>';
         }
         if ($pc->validaPermissao("M_Permissoes", $u_n)) {
            echo '<a href="permissoes_form.php"><li>Permissões</li></a>';
         }
         ?>
      </div>
      <hr />
      <a href="logout.php"><li>Sair</li></a>
      </ul>
   </div>
</div>


<script>
   /* Loop through all dropdowns, to toggle between hiding and showing its dropdown content
      This allows to have multiple dropdowns without any conflict */
   var dropdown = document.getElementsByClassName("dropdown-btn");
   var i;

   for (i = 0; i < dropdown.length; i++) {

      let dentro = dropdown[i].nextElementSibling.childNodes;
      /* let dentro = dropdown[i].parentNode.nextElementSibling.childNodes;

      /* Havendo um elemento <li></li> o array fica com três elementos, pois há sempre um elemento text antes e depois do <li> */
      if (dentro.length < 3) {
         dropdown[i].style.display = "none";
      };

      dropdown[i].addEventListener("click", function () {
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