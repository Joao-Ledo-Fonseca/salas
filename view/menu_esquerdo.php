<?php

require_once "../controller/permissoesController.php";
$pc = new permissoesController();

$u_n = $_SESSION['user_nivel'];
$id_u = $_SESSION['user_nome'] . ' (' . $pc->nomeNivel($u_n, 1) . ')';
$id_u_link = '<a href="usuario_form.php?id=' . $_SESSION['user_id'] . '">' . $id_u . '</a>';
?>

<!-- Menu hamburger -->
<div class="hamburger" onclick="openNav()">&#9776;</div>

<div class="menu">
   <span class="closebtn" onclick="closeNav()">&times;</span>
   <div class="menu-inner">
      <div class="user">
         <img src="img/do-utilizador-branco.png" width="20" height="20" alt="" />
         <p><?= $id_u_link ?></p>
      </div>
      <ul>
         <a href="index.php"><li>Home</li></a>
         <?php if ($pc->validaPermissao("M_Estatisticas", $u_n)): ?>
            <a href="relatorios.php"><li>Estatisticas</li></a>
         <?php endif; ?>
         <?php if ($pc->validaPermissao("M_Relatorios", $u_n)): ?>
            <a href="relatorios2.php"><li>Relatorios</li></a>
         <?php endif; ?>

         <?php
         // Verifica se há pelo menos um submenu para mostrar o dropdown
         $adminItems = [];
         if ($pc->validaPermissao("M_Categorias", $u_n))    $adminItems[] = '<a href="categoria_list.php"><li>Categorias</li></a>';
         if ($pc->validaPermissao("M_Salas", $u_n))         $adminItems[] = '<a href="sala_list.php"><li>Salas</li></a>';
         if ($pc->validaPermissao("M_Periodos", $u_n))      $adminItems[] = '<a href="periodo_list.php"><li>Períodos</li></a>';
         if ($pc->validaPermissao("M_Utilizadores", $u_n))  $adminItems[] = '<a href="usuario_list.php"><li>Utilizadores</li></a>';
         if ($pc->validaPermissao("M_Permissoes", $u_n))    $adminItems[] = '<a href="permissoes_form.php"><li>Permissões</li></a>';
         ?>

         <?php if (count($adminItems) > 0): ?>            
               <a class="dropdown-btn" href="#">
                  <li class="admin-li">Admin &#9660;</li>
               </a>
               <ul class="dropdown-container">
                  <?= implode('', $adminItems) ?>                  
               </ul>
                           
         <?php endif; ?>

         <hr />
         <a href="logout.php"><li>Sair</li></a>
      </ul>
   </div>
</div>

<script>
function openNav() {
   var menu = document.getElementsByClassName("menu")[0];
   menu.classList.add("open");
}
function closeNav() {
   var menu = document.getElementsByClassName("menu")[0];
   menu.classList.remove("open");
}

// Fecha o menu ao clicar fora (mobile)
window.addEventListener('click', function (event) {
   var menu = document.getElementsByClassName("menu")[0];
   var hamburger = document.getElementsByClassName("hamburger")[0];
   if (window.innerWidth <= 900 && menu.classList.contains("open")) {
      if (!menu.contains(event.target) && event.target !== hamburger) {
         closeNav();
      }
   }
});

// Dropdown Admin
document.addEventListener('DOMContentLoaded', function () {
   var dropdownBtns = document.getElementsByClassName("dropdown-btn");
   for (var i = 0; i < dropdownBtns.length; i++) {
      dropdownBtns[i].addEventListener("click", function (e) {
         e.preventDefault();
         var adminLi = this.closest('.admin-li');
         var dropdownContent = this.nextElementSibling;
         if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
            if (adminLi) adminLi.classList.remove("activo");
         } else {
            dropdownContent.style.display = "block";
            if (adminLi) adminLi.classList.add("activo");
         }
      });
   }
});
</script>