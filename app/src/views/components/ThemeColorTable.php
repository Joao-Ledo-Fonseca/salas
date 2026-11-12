<?php
/**
 * Componente para renderizar tabela de cores de tema.
 * 
 * @param array $keys Lista de chaves de cores.
 * @param array $themeData Dados do tema.
 * @param string $paletteType Tipo de paleta (colors ou dashboard).
 * @param string $inputNamePrefix Prefixo para o nome do input.
 */
?>
<table class="tema-table tabela_comum">
    <?php foreach ($keys as $k): ?>
        <?php 
            $val = (string)($themeData[$paletteType][$k] ?? '#000000');
            $label = Salas\Core\Theme\ThemeColorManager::label($k) ?? $k;
            $cssVar = Salas\Core\Theme\ThemeColorManager::cssVar($k) ?? '--color-' . Salas\Core\Theme\ThemeColorManager::kebab($k);
            $usage = Salas\Core\Theme\ThemeColorManager::usage($k) ?? '';
            $tooltip = "Chave: {$k}\nVariável: {$cssVar}\nUso: {$usage}";
        ?>
        <tr>
            <td class="w-240 whitespace-nowrap">
                <label for="color_<?= htmlspecialchars($inputNamePrefix . '_' . $k, ENT_QUOTES, 'UTF-8') ?>" 
                       class="theme-color-label" 
                       data-tooltip="<?= htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                </label>
            </td>
            <td class="whitespace-nowrap col-input flex items-center">
                <input type="text" id="color_<?= htmlspecialchars($inputNamePrefix . '_' . $k, ENT_QUOTES, 'UTF-8') ?>" class="color-input color-input-compact" 
                       name="<?= $inputNamePrefix ?>[<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>]" 
                       value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" 
                       autocomplete="off"
                       data-palette-type="<?= htmlspecialchars($paletteType, ENT_QUOTES, 'UTF-8') ?>" 
                       data-palette-key="<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>" 
                       data-var="<?= htmlspecialchars($cssVar, ENT_QUOTES, 'UTF-8') ?>" />
                <span class="col-btn"></span>
            </td>
            <td class="w-full"></td>
        </tr>
    <?php endforeach; ?>
</table>
