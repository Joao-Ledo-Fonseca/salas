<?php
use Salas\Core\Support\Csrf;
use Salas\Core\Theme\ThemeLoader;
?>
<?php $title = 'Temas'; ?>

<h3>Temas</h3>

<div class="container_conteudo">

  <div class="lista_comum mb-4">
    <h4>Tema ativo</h4>
    <form method="post" action="<?= app_url('?acao=temas') ?>" class="flex gap-10 items-center flex-wrap">
      <select name="active_theme" id="active_theme" class="w-40ch" onchange="this.form.submit()">
        <?php foreach ($availableThemes as $t): ?>
          <option value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>" <?= $t === $activeTheme ? 'selected' : '' ?>>
            <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
      <input type="hidden" name="ativar_tema" value="1" />
      <input type="hidden" name="edit_theme" value="<?= htmlspecialchars($editTheme, ENT_QUOTES, 'UTF-8') ?>" />
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>" />
      <input type="submit" class="btn1" value="Ativar" />
    </form>
    <p class="text-sm text-muted">O tema ativo é aplicado imediatamente e guardado como padrão.</p>
  </div>

  <div class="lista_comum mb-4">
    <h4>Tema para edição</h4>
    <form method="get" action="<?= app_url('?acao=temas') ?>" class="flex gap-10 items-center flex-wrap">
      <input type="hidden" name="acao" value="temas" />
      <input type="hidden" name="theme" value="<?= htmlspecialchars($activeTheme, ENT_QUOTES, 'UTF-8') ?>" />
      <select name="edit_theme" onchange="this.form.submit()" class="w-40ch">
        <?php foreach ($availableThemes as $t): ?>
          <option value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>" <?= $t === $editTheme ? 'selected' : '' ?>>
            <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
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


    <div class="lista_comum flex gap-10">
      <input type="button" class="btn1" value="Testar" onclick="aplicarPreview()" />
      <input type="button" class="btn1" value="Repor" onclick="reporTema()" />
      <input type="submit" name="gravar" class="btn1" value="Gravar" />
    </div>
    <div class="tema-tables-scroll">
    <div class="tema-tabs">
      <button type="button" class="tema-tab active" onclick="showTab('base')">Tema para edição</button>
      <button type="button" class="tema-tab" onclick="showTab('gerais-app')">Gerais (app)</button>
      <button type="button" class="tema-tab" onclick="showTab('gerais-avisos')">Gerais (avisos/erros)</button>
      <button type="button" class="tema-tab" onclick="showTab('menu')">Menu</button>
      <button type="button" class="tema-tab" onclick="showTab('dashboard')">Dashboard</button>
    </div>

    <div class="tema-tab-content active" id="tab-base">
      <div class="lista_comum">
        <table class="tema-table">
          <tr>
            <td><label>Nome</label></td>
            <td><input type="text" name="name" value="<?= htmlspecialchars((string)($themeData['name'] ?? $editTheme), ENT_QUOTES, 'UTF-8') ?>" /></td>
          </tr>
          <tr>
            <td><label>Família de fonte</label></td>
            <td><input type="text" name="fontFamily" value="<?= htmlspecialchars((string)($themeData['fontFamily'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-320" /></td>
          </tr>
          <tr>
            <td><label>Tamanho base</label></td>
            <td><input type="number" name="fontSizeBase" min="8" max="16" step="1" value="<?= htmlspecialchars((string)preg_replace('/[^0-9]/', '', (string)($themeData['fontSizeBase'] ?? '14px')), ENT_QUOTES, 'UTF-8') ?>" /> px</td>
        </tr>
        <tr>
            <td><label>Cor base</label></td>
            <td><input type="text" class="color-input color-input-compact" name="baseColor" value="<?= htmlspecialchars((string)($themeData['baseColor'] ?? '#FFFFFF'), ENT_QUOTES, 'UTF-8') ?>" data-var="--color-base" /></td>
          </tr>
        </table>
      </div>
    </div>

    <div class="tema-tab-content" id="tab-gerais-app">
      <div class="lista_comum">
        <h4>Cores gerais (app)</h4>
      <?php
        $colorTips = [
          'bg' => 'Fundo da página',
          'surface' => 'Fundo de superfícies/cartões',
          'surfaceAlt' => 'Fundo alternativo',
          'text' => 'Texto base',
          'textStrong' => 'Texto forte/títulos',
          'textMuted' => 'Texto secundário',
          'border' => 'Bordas',
          'borderStrong' => 'Bordas fortes',
          'primary' => 'Cor primária (links/botões)',
          'primaryDark' => 'Primária escura (hover)',
          'primaryLight' => 'Primária clara',
          'success' => 'Sucesso',
          'warning' => 'Aviso',
          'danger' => 'Erro/perigo',
          'dangerDark' => 'Erro escuro',
          'dangerLight' => 'Erro claro',
          'dangerBorder' => 'Borda de erro',
          'overlay' => 'Overlay/modal',
        ];
        $menuTips = [
          'menuBg' => 'Fundo do menu',
          'menuItem' => 'Item do menu',
          'menuHoverBg' => 'Fundo hover do menu',
          'menuHoverText' => 'Texto hover do menu',
          'menuText' => 'Texto do menu',
        ];
        $generalAppKeys = [];
        $generalAvisosKeys = [];
        $menuColorKeys = [];
        $avisosKeys = ['surfaceAlt', 'borderStrong', 'success', 'warning', 'danger', 'dangerDark', 'dangerLight', 'dangerBorder'];
        foreach ($colorKeys as $k) {
          if (strpos($k, 'menu') === 0) {
            $menuColorKeys[] = $k;
          } elseif (in_array($k, $avisosKeys, true)) {
            $generalAvisosKeys[] = $k;
          } else {
            $generalAppKeys[] = $k;
          }
        }
      ?>
      <div class="tema-tables-scroll">
      <table class="tema-table">
        <?php foreach ($generalAppKeys as $k): ?>
          <?php $val = (string)($themeData['colors'][$k] ?? '#000000'); ?>
          <tr>
            <td class="w-160 whitespace-nowrap" title="<?= htmlspecialchars($colorTips[$k] ?? $k, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?></td>
            <td class="whitespace-nowrap col-input">
              <input type="text" class="color-input color-input-compact" name="colors[<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" data-var="--color-<?= htmlspecialchars(strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $k)), ENT_QUOTES, 'UTF-8') ?>" />
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
      </div>
    </div>

    </div>

    <div class="tema-tab-content" id="tab-gerais-avisos">
      <div class="lista_comum">
        <h4>Cores gerais (avisos/erros)</h4>
        <div class="tema-tables-scroll">
          <table class="tema-table">
            <?php foreach ($generalAvisosKeys as $k): ?>
              <?php $val = (string)($themeData['colors'][$k] ?? '#000000'); ?>
              <tr>
                <td class="w-160 whitespace-nowrap" title="<?= htmlspecialchars($colorTips[$k] ?? $k, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?></td>
                <td class="whitespace-nowrap col-input">
                  <input type="text" class="color-input color-input-compact" name="colors[<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" data-var="--color-<?= htmlspecialchars(strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $k)), ENT_QUOTES, 'UTF-8') ?>" />
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
      </div>
    </div>

    <div class="tema-tab-content" id="tab-menu">
      <div class="lista_comum">
        <h4>Cores do menu</h4>
      <table class="tema-table">
        <?php foreach ($menuColorKeys as $k): ?>
          <?php $val = (string)($themeData['colors'][$k] ?? '#000000'); ?>
          <tr>
            <td class="w-160 whitespace-nowrap" title="<?= htmlspecialchars($menuTips[$k] ?? $k, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?></td>
            <td class="whitespace-nowrap col-input">
              <input type="text" class="color-input color-input-compact" name="colors[<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" data-var="--color-<?= htmlspecialchars(strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $k)), ENT_QUOTES, 'UTF-8') ?>" />
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    </div>

    <div class="tema-tab-content" id="tab-dashboard">
      <div class="lista_comum">
        <h4>Cores do dashboard</h4>
      <?php
        $dashTips = [
          'reservado' => 'Célula reservada',
          'confirmado' => 'Célula confirmada',
          'cancelado' => 'Célula cancelada',
          'disponivel' => 'Célula disponível',
          'disponivelHover' => 'Hover célula disponível',
          'selected' => 'Célula selecionada',
          'cellBorder' => 'Borda da célula',
          'cellText' => 'Texto da célula',
        ];
      ?>
      <table class="tema-table">
        <?php foreach ($dashboardKeys as $k): ?>
          <?php $val = (string)($themeData['dashboard'][$k] ?? '#ffffff'); ?>
          <tr>
            <td class="w-160 whitespace-nowrap" title="<?= htmlspecialchars($dashTips[$k] ?? $k, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?></td>
            <td class="whitespace-nowrap col-input">
              <input type="text" class="color-input color-input-compact" name="dashboard[<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" data-var="--dashboard-<?= htmlspecialchars(strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $k)), ENT_QUOTES, 'UTF-8') ?>" />
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>

  </form>
</div>

<link rel="stylesheet" type="text/css" href="<?= app_url('/css/pickr.min.css') ?>">
<script src="<?= app_url('/js/pickr.min.js') ?>"></script>
<script src="<?= app_url('/js/temas.js') ?>"></script>
<script>
  function showTab(tabName) {
    document.querySelectorAll('.tema-tab').forEach(function(btn) {
      btn.classList.remove('active');
    });
    document.querySelectorAll('.tema-tab-content').forEach(function(div) {
      div.classList.remove('active');
    });
    var btn = document.querySelector('.tema-tab[onclick*="' + tabName + '"]');
    if (btn) btn.classList.add('active');
    var div = document.getElementById('tab-' + tabName);
    if (div) div.classList.add('active');
  }
  function restoreTab() {
    var saved = null;
    try { saved = localStorage.getItem('tema-tab'); } catch(e) {}
    if (saved && document.getElementById('tab-' + saved)) {
      showTab(saved);
    } else {
      showTab('gerais-app');
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      initPickers();
      restoreTab();
    });
  } else {
    initPickers();
    restoreTab();
  }
  document.querySelectorAll('.tema-tab').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var m = this.getAttribute('onclick').match(/showTab\('([^']+)'\)/);
      if (m) try { localStorage.setItem('tema-tab', m[1]); } catch(e) {}
    });
  });
</script>

