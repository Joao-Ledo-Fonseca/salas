<?php
$listaCreateUrl = (string) ($acoesLista['createUrl'] ?? '');
$listaContainerClass = (string) ($acoesLista['containerClass'] ?? 'container_top lista_comum');
$listaContainerStyle = (string) ($acoesLista['containerStyle'] ?? 'max-height:60vh; overflow-y:auto;');
$listaFormAction = $acoesLista['formAction'] ?? null;
$listaCreateArgument = json_encode(
    $listaCreateUrl,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
?>
<div class="<?= htmlspecialchars($listaContainerClass, ENT_QUOTES, 'UTF-8') ?>"<?= $listaContainerStyle !== '' ? ' style="' . htmlspecialchars($listaContainerStyle, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
    <form class="form_sel" name="form1" method="post" target="_self"<?= is_string($listaFormAction) ? ' action="' . htmlspecialchars($listaFormAction, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
        <span style="float:left;">
            <input type="button" name="novo" value="Novo" class="btn1" onclick="abre(<?= htmlspecialchars($listaCreateArgument === false ? '""' : $listaCreateArgument, ENT_QUOTES, 'UTF-8') ?>)" />
        </span>
        <?= \Salas\Core\Support\Csrf::field() ?>
    </form>
</div>
