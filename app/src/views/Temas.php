<?php

/**
 * @var string $activeTheme
 * @var string $editTheme
 * @var array<string> $availableThemes
 * @var array $themeData
 * @var string $fontFamilySelection
 * @var string $csrfToken
 * @var string $errormsg
 */
$activeTheme = isset($activeTheme) ? (string)$activeTheme : 'light';
$editTheme = isset($editTheme) ? (string)$editTheme : $activeTheme;
$availableThemes = isset($availableThemes) && is_array($availableThemes) ? $availableThemes : [];
$themeData = isset($themeData) && is_array($themeData) ? $themeData : [];
$fontFamilySelection = isset($fontFamilySelection) ? (string)$fontFamilySelection : '';
$csrfToken = isset($csrfToken) ? (string)$csrfToken : '';
$errormsg = isset($errormsg) ? (string)$errormsg : '';

$title = 'Temas';
?>

<h3>Temas</h3>
<?php if (!empty($errormsg)): ?>
  <div class="form_sel lista_comum text-error"><?= htmlspecialchars($errormsg, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="container_conteudo">
  <div class="lista_comum mb-4">
    <h4>Tema ativo</h4>
    <form method="post" action="<?= app_url('?acao=temas') ?>" class="flex gap-10 items-center flex-wrap">
      <select name="active_theme" id="active_theme" class="w-40ch" onchange="this.form.submit()">
        <?php foreach ($availableThemes as $t): ?>
          <option value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>" <?= $t === $activeTheme ? 'selected' : '' ?>>
            <?= htmlspecialchars(Salas\Core\Theme\ThemeColorManager::label($t) ?? $t, ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
      <input type="hidden" name="ativar_tema" value="1" />
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>" />
      <input type="submit" class="btn1" value="Ativar" />
    </form>
  </div>

  <div class="lista_comum mb-4">
    <h4>Tema para editar</h4>
    <form method="get" action="<?= app_url('?acao=temas') ?>" class="flex gap-10 items-center flex-wrap">
      <input type="hidden" name="acao" value="temas" />
      <input type="hidden" name="theme" value="<?= htmlspecialchars($activeTheme, ENT_QUOTES, 'UTF-8') ?>" />
      <select name="edit_theme" id="edit_theme" class="w-40ch" onchange="this.form.submit()">
        <?php foreach ($availableThemes as $t): ?>
          <option value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>" <?= $t === $editTheme ? 'selected' : '' ?>>
            <?= htmlspecialchars(Salas\Core\Theme\ThemeColorManager::label($t) ?? $t, ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <form name="form_tema" method="post" action="<?= app_url('?acao=temas') ?>">
    <input type="hidden" name="acao" value="temas" />
    <input type="hidden" name="theme" value="<?= htmlspecialchars($activeTheme, ENT_QUOTES, 'UTF-8') ?>" />
    <input type="hidden" name="edit_theme" value="<?= htmlspecialchars($editTheme, ENT_QUOTES, 'UTF-8') ?>" />
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>" />

    <div class="tema-tabs mt-4">
      <button type="button" class="tema-tab active" onclick="showTab('base')">Base</button>
      <button type="button" class="tema-tab" onclick="showTab('gerais-app')">Gerais (app)</button>
      <button type="button" class="tema-tab" onclick="showTab('gerais-avisos')">Avisos</button>
      <button type="button" class="tema-tab" onclick="showTab('menu')">Menu</button>
      <button type="button" class="tema-tab" onclick="showTab('dashboard')">Dashboard</button>
    </div>

    <script>
      function showTab(tabName) {
        document.querySelectorAll('.tema-tab-content').forEach(function(el) {
          el.classList.remove('active');
        });
        document.querySelectorAll('.tema-tab').forEach(function(el) {
          el.classList.remove('active');
        });
        var content = document.getElementById('tab-' + tabName);
        if (content) content.classList.add('active');
        document.querySelectorAll('.tema-tab').forEach(function(btn) {
          if (btn.getAttribute('onclick') && btn.getAttribute('onclick').indexOf(tabName) !== -1) {
            btn.classList.add('active');
          }
        });
      }
    </script>

    <div class="tema-tab-content active" id="tab-base">
      <div class="lista_comum">
        <table class="tema-table tabela_comum">
          <tr>
            <td><label for="theme_name">Nome</label></td>
            <td><input type="text" id="theme_name" name="name" autocomplete="off" value="<?= htmlspecialchars((string)($themeData['name'] ?? $editTheme), ENT_QUOTES, 'UTF-8') ?>" data-tooltip="Chave: name | Variável: --color-name | Uso: Nome do tema" /></td>
            <td class="w-full"></td>
          </tr>
          <tr>
            <td><label for="font_size_base">Tamanho base</label></td>
            <td><input type="number" id="font_size_base" name="fontSizeBase" min="8" max="24" autocomplete="off" value="<?= htmlspecialchars((string)preg_replace('/[^0-9]/', '', (string)$themeData['fontSizeBase'] ?? '14'), ENT_QUOTES, 'UTF-8') ?>" data-tooltip="Chave: fontSizeBase | Variável: --font-size-base | Uso: Tamanho base da fonte" /> px</td>
            <td class="w-full"></td>
          </tr>
          <tr>
            <td><label for="font_family">Fonte</label></td>
            <td>
              <select name="fontFamily" id="font_family" data-tooltip="Chave: fontFamily | Variável: --font-family | Uso: Família de fonte">
                <?php foreach (Salas\Core\Theme\ThemeFontFamily::options() as $name => $stack): ?>
                  <option value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" data-stack="<?= htmlspecialchars($stack, ENT_QUOTES, 'UTF-8') ?>" style="font-family: <?= htmlspecialchars($stack, ENT_QUOTES, 'UTF-8') ?>;" <?= $name === $fontFamilySelection ? 'selected' : '' ?>><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td class="w-full"></td>
          </tr>
          <tr>
            <td><label for="base_color" class="theme-color-label"
                data-tooltip="<?= htmlspecialchars(
                                'Chave: baseColor' . "\nVariável: " . (Salas\Core\Theme\ThemeColorManager::cssVar('baseColor') ?? '--color-base') . "\nUso: " . (Salas\Core\Theme\ThemeColorManager::usage('baseColor') ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                              ) ?>">
                Cor base
              </label></td>
            <td class="col-input flex items-center"><input type="text" id="base_color" class="color-input color-input-compact" name="baseColor" value="<?= htmlspecialchars((string)$themeData['baseColor'] ?? '#FFFFFF', ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" data-var="--color-base" /><span class="col-btn"></span></td>
            <td class="w-full"></td>
          </tr>
        </table>
        <div class="flex gap-10 mt-4">
          <input type="button" class="btn1" value="Testar" onclick="aplicarPreview()" />
          <input type="button" class="btn1" value="Repor" onclick="reporTema()" />
          <input type="submit" name="gravar" id="btn_gravar" class="btn1" value="Gravar" />
          <div class="ml-100 flex gap-10">
            <input type="submit" name="criar_tema" class="btn1" value="Criar" />
            <input type="submit" name="apagar_tema" class="btn1 btn-danger" value="Eliminar" onclick="return confirm('Tem certeza que deseja eliminar este tema?');" />
          </div>
        </div>
      </div>
    </div>

    <div class="tema-tab-content" id="tab-gerais-app">
      <div class="lista_comum">
        <?php
        $keys = Salas\Core\Theme\ThemeColorManager::getAppKeys();
        $paletteType = 'colors';
        $inputNamePrefix = 'colors';
        include 'components/ThemeColorTable.php';
        ?>
      </div>
    </div>

    <div class="tema-tab-content" id="tab-gerais-avisos">
      <div class="lista_comum">
        <?php
        $keys = Salas\Core\Theme\ThemeColorManager::getAvisosKeys();
        $paletteType = 'colors';
        $inputNamePrefix = 'colors';
        include 'components/ThemeColorTable.php';
        ?>
      </div>
    </div>

    <div class="tema-tab-content" id="tab-menu">
      <div class="lista_comum">
        <?php
        $keys = Salas\Core\Theme\ThemeColorManager::getMenuKeys();
        $paletteType = 'colors';
        $inputNamePrefix = 'colors';
        include 'components/ThemeColorTable.php';
        ?>
      </div>
    </div>

    <div class="tema-tab-content" id="tab-dashboard">
      <div class="lista_comum">
        <?php
        $keys = Salas\Core\Theme\ThemeColorManager::allDashboardKeys();
        $paletteType = 'dashboard';
        $inputNamePrefix = 'dashboard';
        include 'components/ThemeColorTable.php';
        ?>
      </div>
    </div>
    <script src="<?= app_url('/js/temas_base.js') ?>"></script>
    <script src="<?= app_url('/Tests/ThemeSmokeTest.js') ?>"></script>
    <script>
      (function() {
        var init = function() {
          if (typeof window.initPickers === 'function') {
            window.initPickers();
          }

          var form = document.forms['form_tema'];
          var btnGravar = document.getElementById('btn_gravar');

          form.addEventListener('input', function() {
            btnGravar.disabled = false;
          });
          btnGravar.disabled = true;

          document.getElementById('edit_theme').addEventListener('change', function() {
            this.form.submit();
          });
        };
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', init);
        } else {
          init();
        }
      })();
    </script>
  </form>
</div>