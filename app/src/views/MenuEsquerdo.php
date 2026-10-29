<?php
use Salas\Core\Support\Csrf;
use Salas\Models\Reserva;
use Salas\Models\Sala;
use Salas\Models\Categoria;
use Salas\Models\Usuario;
use Salas\Models\Periodo;
use Salas\Models\Uso;
use Salas\Controllers\SalaController;
use Salas\Controllers\CategoriaController;
use Salas\Controllers\UsuarioController;
use Salas\Controllers\ReservaController;
use Salas\Controllers\PeriodoController;
use Salas\Controllers\UsoController;
use Salas\Controllers\PermissoesController;
use Salas\Core\Support\Util;
use Salas\Core\Validation\Validator;
use Salas\Application\Services\AuthorizationService;
?>

<!-- Menu hamburger -->
<div class="hamburger" onclick="openNav()">☰</div>

<div class="menu">
   <span class="closebtn" onclick="closeNav()">&times;</span>
   <div class="menu-inner">
   <div class="user">
      <img src="<?= app_url('/img/do-utilizador-branco.png') ?>" width="20" height="20" alt="" />
      <a class="dropdown-btn" href="#">
         <p><?= htmlspecialchars($menuData['userName'] . ' (' . $menuData['userLevel'] . ')', ENT_QUOTES, 'UTF-8') ?> ▼</p>
      </a>
      <ul class="dropdown-container">
         <a href="<?= htmlspecialchars($menuData['profileUrl'], ENT_QUOTES, 'UTF-8') ?>"><li>Perfil</li></a>
         <a href="<?= app_url('?acao=logout') ?>"><li>Sair</li></a>
      </ul>
   </div>
      <ul>
         <a href="<?= app_url() ?>"><li>Home</li></a>
         
         <?php if ($menuData['showStatistics']): ?>
            <a href="<?= app_url('?acao=estatisticas') ?>"><li>Estatisticas</li></a>
         <?php endif; ?>
         <?php if ($menuData['showReports']): ?>
            <a href="<?= app_url('?acao=relatorios') ?>"><li>Relatorios</li></a>
         <?php endif; ?>

         <?php if (count($menuData['adminItems']) > 0): ?>
               <a class="dropdown-btn" href="#">
                  <li class="admin-li">Admin ▼</li>
               </a>
               <ul class="dropdown-container">
                  <?php foreach ($menuData['adminItems'] as $item): ?>
                     <a href="<?= htmlspecialchars(app_url($item['url']), ENT_QUOTES, 'UTF-8') ?>"><li><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></li></a>
                  <?php endforeach; ?>
               </ul>
                           
         <?php endif; ?>

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
