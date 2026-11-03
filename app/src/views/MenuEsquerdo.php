<?php

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
use Salas\Core\Validation\Validator;
?>

<?php
$adminMenuItems = [];
$adminSelectorItems = [];
foreach (($menuData['adminItems'] ?? []) as $item) {
    if (in_array($item['label'], ['Utilizadores', 'Permissões', 'Temas'], true)) {
        $adminSelectorItems[] = $item;
    } else {
        $adminMenuItems[] = $item;
    }
}
?>

<!-- Menu hamburger -->
<div class="hamburger" onclick="openNav()">☰</div>

<div class="menu">
   <span class="closebtn" onclick="closeNav()">&times;</span>
   <div class="menu-inner">
   <div class="user">
      <svg viewBox="-48 -48 608 608" width="1.716rem" height="1.716rem" fill="none" stroke="#fff" stroke-width="38" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
         <path d="M203 18c-56 0-101 45-101 101 0 51 15 83 45 104 17 13 37 25 56 25s39-12 56-25c30-21 45-53 45-104 0-56-45-101-101-101Z"/>
         <path d="M147 223c-57 17-100 55-124 110C9 362 1 403 1 430c0 6 3 10 8 14 58 38 125 58 194 58 46 0 89-9 128-28"/>
         <path d="M261 223c28 9 53 24 74 42"/>
         <circle cx="391" cy="354" r="109"/>
         <path d="m337 350 40 40 69-69"/>
      </svg>
      <a class="dropdown-btn" href="#">
         <p><?= htmlspecialchars($menuData['userName'] . ' (' . $menuData['userLevel'] . ')', ENT_QUOTES, 'UTF-8') ?> ▼</p>
      </a>
      <ul class="dropdown-container">
         <?php if (!empty($menuData['showProfile'])): ?>
            <a href="<?= htmlspecialchars($menuData['profileUrl'], ENT_QUOTES, 'UTF-8') ?>"><li>Perfil</li></a>
         <?php endif; ?>
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

         <?php if (count($adminMenuItems) > 0): ?>
               <a class="dropdown-btn" href="#">
                  <li class="admin-li">Admin ▼</li>
               </a>
               <ul class="dropdown-container">
                  <?php foreach ($adminMenuItems as $item): ?>
                     <a href="<?= htmlspecialchars(app_url($item['url']), ENT_QUOTES, 'UTF-8') ?>"><li><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></li></a>
                  <?php endforeach; ?>
               </ul>
                           
         <?php endif; ?>

      </ul>
   </div>
   <div class="menu-switchers">
      <?php if (count($adminSelectorItems) > 0): ?>
      <div class="admin-switcher">
         <button type="button" id="admin-toggle" class="admin-toggle" aria-label="Administração" aria-expanded="false" aria-controls="admin-popover">
            <svg viewBox="0 0 24 24" width="1.716rem" height="1.716rem" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
               <circle cx="12" cy="12" r="3"></circle>
               <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
         </button>
         <div id="admin-popover" class="switcher-popover" role="menu" aria-hidden="true" style="display:none;">
            <?php foreach ($adminSelectorItems as $item): ?>
               <a class="switcher-popover-item" href="<?= htmlspecialchars(app_url($item['url']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></a>
            <?php endforeach; ?>
         </div>
      </div>
      <?php endif; ?>
      <div class="theme-switcher">
         <button type="button" id="theme-toggle" class="theme-toggle" aria-label="Tema" aria-expanded="false" aria-controls="theme-popover">
            <svg viewBox="0 0 24 24" width="1.716rem" height="1.716rem" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
               <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-1 0-.83.67-1.5 1.5-1.5H16c3.31 0 6-2.69 6-6 0-5.52-4.48-10-10-10z"></path>
               <circle cx="7.5" cy="11.5" r="1.5"></circle>
               <circle cx="10.5" cy="7.5" r="1.5"></circle>
               <circle cx="15.5" cy="7.5" r="1.5"></circle>
               <circle cx="18" cy="11.5" r="1.5"></circle>
            </svg>
         </button>
         <div id="theme-popover" class="switcher-popover" role="menu" aria-hidden="true" style="display:none;"></div>
      </div>
   </div>
</div>

<script>
// O código JavaScript permanece exatamente igual
/**
 * Método openNav.
 * @return mixed
 */
function openNav() {
   var menu = document.getElementsByClassName("menu")[0];
   menu.classList.add("open");
}
/**
 * Método closeNav.
 * @return mixed
 */
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
document.addEventListener('DOMContentLoaded', function () {
   var toggle = document.getElementById('theme-toggle');
   var pop = document.getElementById('theme-popover');
   if (!toggle || !pop) return;
   var current = window.__APP_THEME__ || null;
   function render() {
      pop.innerHTML = '';
      var avail = (window.SalasTheme && Array.isArray(window.SalasTheme.available))
         ? window.SalasTheme.available : ['light', 'dark'];
      avail.forEach(function (key) {
         var btn = document.createElement('button');
         btn.type = 'button';
         btn.className = 'switcher-popover-item' + (key === current ? ' active' : '');
         btn.textContent = key.charAt(0).toUpperCase() + key.slice(1);
         btn.addEventListener('click', function () {
            if (window.SalasTheme && typeof window.SalasTheme.set === 'function') {
               window.SalasTheme.set(key);
            }
         });
         pop.appendChild(btn);
      });
   }
   render();
   function openPop() {
      pop.style.display = 'flex';
      pop.classList.add('open');
      toggle.setAttribute('aria-expanded', 'true');
      pop.setAttribute('aria-hidden', 'false');
   }
   function closePop() {
      pop.style.display = 'none';
      pop.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
      pop.setAttribute('aria-hidden', 'true');
   }
   toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      if (pop.classList.contains('open')) { closePop(); } else { openPop(); }
   });
   var closeTimer = null;
   function scheduleClose() { clearTimeout(closeTimer); closeTimer = setTimeout(closePop, 250); }
   function cancelClose() { clearTimeout(closeTimer); closeTimer = null; }
   var wrapper = toggle.parentElement;
   if (wrapper) {
      wrapper.addEventListener('mouseenter', cancelClose);
      wrapper.addEventListener('mouseleave', scheduleClose);
   }
   pop.addEventListener('mouseenter', cancelClose);
   pop.addEventListener('mouseleave', scheduleClose);
   document.addEventListener('click', function (e) {
      if (!pop.contains(e.target) && e.target !== toggle) { closePop(); }
   });
   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { closePop(); }
   });
});

document.addEventListener('DOMContentLoaded', function () {
   var adminToggle = document.getElementById('admin-toggle');
   var adminPop = document.getElementById('admin-popover');
   if (!adminToggle || !adminPop) return;
   function openPop() {
      adminPop.style.display = 'flex';
      adminPop.classList.add('open');
      adminToggle.setAttribute('aria-expanded', 'true');
      adminPop.setAttribute('aria-hidden', 'false');
   }
   function closePop() {
      adminPop.style.display = 'none';
      adminPop.classList.remove('open');
      adminToggle.setAttribute('aria-expanded', 'false');
      adminPop.setAttribute('aria-hidden', 'true');
   }
   adminToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      if (adminPop.classList.contains('open')) { closePop(); } else { openPop(); }
   });
   var adminCloseTimer = null;
   function scheduleAdminClose() { clearTimeout(adminCloseTimer); adminCloseTimer = setTimeout(closePop, 250); }
   function cancelAdminClose() { clearTimeout(adminCloseTimer); adminCloseTimer = null; }
   var adminWrapper = adminToggle.parentElement;
   if (adminWrapper) {
      adminWrapper.addEventListener('mouseenter', cancelAdminClose);
      adminWrapper.addEventListener('mouseleave', scheduleAdminClose);
   }
   adminPop.addEventListener('mouseenter', cancelAdminClose);
   adminPop.addEventListener('mouseleave', scheduleAdminClose);
   document.addEventListener('click', function (e) {
      if (!adminPop.contains(e.target) && e.target !== adminToggle) { closePop(); }
   });
   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { closePop(); }
   });
});

</script>
