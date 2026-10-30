<?php
use Salas\Core\Support\Csrf;
use Salas\Core\Theme\ThemeLoader;
?>
<?php $title = 'Temas'; ?>

<h3>Temas</h3>

<div class="container_conteudo" style="max-height: calc(100vh - 220px); overflow-y: auto;">

  <div class="lista_comum" style="margin-bottom: 16px;">
    <h4>Tema ativo</h4>
    <form method="post" action="<?= app_url('?acao=temas') ?>" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <select name="active_theme" id="active_theme" style="width: 40ch; max-width: 40ch;" onchange="this.form.submit()">
        <?php foreach ($availableThemes as $t): ?>
          <option value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>" <?= $t === $activeTheme ? 'selected' : '' ?>>
            <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
      <input type="hidden" name="ativar_tema" value="1" />
      <input type="hidden" name="edit_theme" value="<?= htmlspecialchars($editTheme, ENT_QUOTES, 'UTF-8') ?>" />
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>" />
      <input type="submit" class="btn1" value="Ativar e definir como padrão" />
    </form>
    <p style="font-size:12px; color:var(--color-text-muted,#666);">O tema ativo é aplicado imediatamente e guardado como padrão.</p>
  </div>

  <div class="lista_comum" style="margin-bottom: 16px;">
    <h4>Tema para edição</h4>
    <form method="get" action="<?= app_url('?acao=temas') ?>" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <input type="hidden" name="acao" value="temas" />
      <input type="hidden" name="theme" value="<?= htmlspecialchars($activeTheme, ENT_QUOTES, 'UTF-8') ?>" />
      <select name="edit_theme" onchange="this.form.submit()" style="width: 40ch; max-width: 40ch;">
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

    <div class="lista_comum">
      <table cellpadding="1" cellspacing="0" style="font-size: 0.85rem; line-height: 1.6rem;">
        <tr>
          <td><label>Nome</label></td>
          <td><input type="text" name="name" value="<?= htmlspecialchars((string)($themeData['name'] ?? $editTheme), ENT_QUOTES, 'UTF-8') ?>" /></td>
        </tr>
        <tr>
          <td><label>Família de fonte</label></td>
          <td><input type="text" name="fontFamily" value="<?= htmlspecialchars((string)($themeData['fontFamily'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:320px;" /></td>
        </tr>
        <tr>
          <td><label>Tamanho base</label></td>
          <td><input type="number" name="fontSizeBase" min="8" max="16" step="1" value="<?= htmlspecialchars((string)preg_replace('/[^0-9]/', '', (string)($themeData['fontSizeBase'] ?? '14px')), ENT_QUOTES, 'UTF-8') ?>" /> px</td>
        </tr>
      </table>
    </div>

    <div class="lista_comum">
      <h4>Cores gerais</h4>
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
        $generalColorKeys = [];
        $menuColorKeys = [];
        foreach ($colorKeys as $k) {
          if (strpos($k, 'menu') === 0) {
            $menuColorKeys[] = $k;
          } else {
            $generalColorKeys[] = $k;
          }
        }
      ?>
      <table cellpadding="1" cellspacing="0" style="font-size: 0.85rem; line-height: 1.6rem;">
        <?php foreach ($generalColorKeys as $k): ?>
          <?php $val = (string)($themeData['colors'][$k] ?? '#000000'); ?>
          <tr>
            <td style="width: 160px; white-space: nowrap;" title="<?= htmlspecialchars($colorTips[$k] ?? $k, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?></td>
            <td style="white-space: nowrap;">
              <input type="text" class="color-input" name="colors[<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" data-var="--color-<?= htmlspecialchars(strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $k)), ENT_QUOTES, 'UTF-8') ?>" style="width: 6.43rem; height: 1.43rem; padding: 0.14rem 0.29rem;" />
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="lista_comum">
      <h4>Cores do menu</h4>
      <table cellpadding="1" cellspacing="0" style="font-size: 0.85rem; line-height: 1.6rem;">
        <?php foreach ($menuColorKeys as $k): ?>
          <?php $val = (string)($themeData['colors'][$k] ?? '#000000'); ?>
          <tr>
            <td style="width: 160px; white-space: nowrap;" title="<?= htmlspecialchars($menuTips[$k] ?? $k, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?></td>
            <td style="white-space: nowrap;">
              <input type="text" class="color-input" name="colors[<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" data-var="--color-<?= htmlspecialchars(strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $k)), ENT_QUOTES, 'UTF-8') ?>" style="width: 6.43rem; height: 1.43rem; padding: 0.14rem 0.29rem;" />
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

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
      <table cellpadding="1" cellspacing="0" style="font-size: 0.85rem; line-height: 1.6rem;">
        <?php foreach ($dashboardKeys as $k): ?>
          <?php $val = (string)($themeData['dashboard'][$k] ?? '#ffffff'); ?>
          <tr>
            <td style="width: 160px; white-space: nowrap;" title="<?= htmlspecialchars($dashTips[$k] ?? $k, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?></td>
            <td style="white-space: nowrap;">
              <input type="text" class="color-input" name="dashboard[<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" data-var="--dashboard-<?= htmlspecialchars(strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $k)), ENT_QUOTES, 'UTF-8') ?>" style="width: 6.43rem; height: 1.43rem; padding: 0.14rem 0.29rem;" />
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="lista_comum" style="display:flex; gap:10px;">
      <input type="button" class="btn1" value="Aplicar (teste)" onclick="aplicarPreview()" />
      <input type="button" class="btn1" value="Repor" onclick="reporTema()" />
      <input type="submit" name="gravar" class="btn1" value="Gravar" />
    </div>

  </form>
</div>

<link rel="stylesheet" type="text/css" href="<?= app_url('/css/pickr.min.css') ?>">
<script src="<?= app_url('/js/pickr.min.js') ?>"></script>
<script src="<?= app_url('/js/temas.js') ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    initPickers();
  });
</script>

