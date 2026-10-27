<?php

require_once __DIR__. "/../app/controllers/permissoesController.php";
$pc = new permissoesController();

$u_n = $_SESSION['user_nivel'];
$id_u = $_SESSION['user_nome'] . ' (' . $pc->nomeNivel($u_n, 1) . ')';
$id_u_link = '<a href="' . app_url('?acao=usuario_form&id=' . (int) $_SESSION['user_id']) . '">' . $id_u . '</a>';
?>

<!-- Menu hamburger -->
<div class="hamburger" onclick="openNav()">☰</div>

<div class="menu">
   <span class="closebtn" onclick="closeNav()">&times;</span>
   <div class="menu-inner">
      <div class="user">
         <img src="<?= app_url('/img/do-utilizador-branco.png') ?>" width="20" height="20" alt="" />
         <p><?= $id_u_link ?></p>
      </div>
      <ul>
         <a href="<?= app_url() ?>"><li>Home</li></a>
         
         <?php if ($pc->validaPermissao("M_Estatisticas", $u_n)): ?>
            <a href="<?= app_url('?acao=relatorios') ?>"><li>Estatisticas</li></a>
         <?php endif; ?>
         <?php if ($pc->validaPermissao("M_Relatorios", $u_n)): ?>
            <a href="<?= app_url('?acao=relatorios2') ?>"><li>Relatorios</li></a>
         <?php endif; ?>

         <?php
         $adminItems = [];
         if ($pc->validaPermissao("M_Categorias", $u_n))    $adminItems[] = '<a href="' . app_url('?acao=categoria_list') . '"><li>Categorias</li></a>';
         if ($pc->validaPermissao("M_Salas", $u_n))         $adminItems[] = '<a href="' . app_url('?acao=sala_list') . '"><li>Salas</li></a>';
         if ($pc->validaPermissao("M_Periodos", $u_n))      $adminItems[] = '<a href="' . app_url('?acao=periodo_list') . '"><li>Períodos</li></a>';
         if ($pc->validaPermissao("M_Utilizadores", $u_n))  $adminItems[] = '<a href="' . app_url('?acao=usuario_list') . '"><li>Utilizadores</li></a>';
         if ($pc->validaPermissao("M_Permissoes", $u_n))    $adminItems[] = '<a href="' . app_url('?acao=permissoes_form') . '"><li>Permissões</li></a>';
         ?>

         <?php if (count($adminItems) > 0): ?>            
               <a class="dropdown-btn" href="#">
                  <li class="admin-li">Admin ▼</li>
               </a>
               <ul class="dropdown-container">
                  <?= implode('', $adminItems) ?>                  
               </ul>
                           
         <?php endif; ?>

         <hr />
         <a href="<?= app_url('?acao=logout') ?>"><li>Sair</li></a>
      </ul>
   </div>
</div>

<script>
// O código JavaScript permanece exatamente igual
function openNav() {
   var menu = document.getElementsByClassName("menu")[0];
   menu.classList.add("open");
}
function closeNav() {
   var menu = document.getElementsByClassName("menu")[0];
   menu.classList.remove("open");
}

window.addEventListener('click', function (event) {
   var menu = document.getElementsByClassName("menu")[0];
   var hamburger = document.getElementsByClassName("hamburger")[0];
   if (window.innerWidth <= 900 && menu.classList.contains("open")) {
      if (!menu.contains(event.target) && event.target !== hamburger) {
         closeNav();
      }
   }
});

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