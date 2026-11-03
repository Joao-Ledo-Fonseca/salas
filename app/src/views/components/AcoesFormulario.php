<?php
$formActions = $acoesFormulario ?? [];
$cancelAction = $formActions['cancelar'] ?? null;
$cancelMode = is_array($cancelAction) ? ($cancelAction['mode'] ?? 'redirect') : null;
$cancelUrl = is_array($cancelAction) ? (string) ($cancelAction['url'] ?? '') : '';
$cancelArgument = json_encode(
    $cancelUrl,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
$cancelClass = is_array($cancelAction) ? (string) ($cancelAction['class'] ?? 'btn1') : 'btn1';
$cancelId = is_array($cancelAction) ? (string) ($cancelAction['id'] ?? '') : '';
?>
<?php if (!empty($formActions['salvar'])): ?>
    <input type="submit" name="salvar" value="Salvar" class="btn1" />
<?php endif; ?>
<?php if (!empty($formActions['excluir'])): ?>
    <input type="submit" name="excluir" value="Excluir" class="<?= htmlspecialchars((string) ($formActions['excluirClass'] ?? 'btn1'), ENT_QUOTES, 'UTF-8') ?>" />
<?php endif; ?>
<?php if ($cancelMode === 'redirect'): ?>
    <input type="button" value="Cancelar" class="<?= htmlspecialchars($cancelClass, ENT_QUOTES, 'UTF-8') ?>"<?= $cancelId !== '' ? ' id="' . htmlspecialchars($cancelId, ENT_QUOTES, 'UTF-8') . '"' : '' ?> onclick="window.location.href=<?= htmlspecialchars($cancelArgument === false ? '""' : $cancelArgument, ENT_QUOTES, 'UTF-8') ?>" />
<?php elseif ($cancelMode === 'submit'): ?>
    <input type="submit" name="cancelar" value="Cancelar" class="<?= htmlspecialchars($cancelClass, ENT_QUOTES, 'UTF-8') ?>"<?= $cancelId !== '' ? ' id="' . htmlspecialchars($cancelId, ENT_QUOTES, 'UTF-8') . '"' : '' ?><?= !empty($cancelAction['clearRequired']) ? ' onclick="cancelaInputsRequired()"' : '' ?> />
<?php endif; ?>
