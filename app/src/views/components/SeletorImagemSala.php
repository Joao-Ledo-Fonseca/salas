<div
    id="seletor-imagem-sala"
    data-list-url="<?= htmlspecialchars(app_url('?acao=imagens_list'), ENT_QUOTES, 'UTF-8') ?>"
    data-save-url="<?= htmlspecialchars(app_url('?acao=sala_form'), ENT_QUOTES, 'UTF-8') ?>"
    data-sala-id="<?= (int) $id ?>"
    data-category-filter="<?= htmlspecialchars((string) $categoria_filtro, ENT_QUOTES, 'UTF-8') ?>"
    data-active-filter="<?= htmlspecialchars((string) $activas_filtro, ENT_QUOTES, 'UTF-8') ?>"
    data-csrf-token="<?= htmlspecialchars(\Salas\Core\Support\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>"
>
    <input type="hidden" name="imagem_id" id="imagem_id" value="<?= htmlspecialchars((string) $imagem_id, ENT_QUOTES, 'UTF-8') ?>" form="sala-form" />
    <input name="imagem" id="newimagem" type="file" style="display:none;" form="sala-form" />

    <?php if (strlen((string) $imagem) > 0): ?>
        <img id="imagem" src="data:image/jpeg;base64,<?= base64_encode($imagem) ?>" width="400" style="cursor:pointer;" alt="Imagem da sala" />
    <?php else: ?>
        <div id="imagemPlaceholder" style="width:400px; height:200px; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; color:#999; font-size:12px; cursor:pointer;">Adicionar Imagem</div>
        <img id="imagem" style="display:none; width:400px;" alt="Pré-visualização da imagem da sala" />
    <?php endif; ?>

    <div id="overlayImagens" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; pointer-events:auto;">
        <div style="position:absolute; right:20px; top:60px; width:320px; max-height:80vh; overflow-y:auto; background:#fff; border:1px solid #999; border-radius:6px; padding:10px; box-shadow:0 4px 12px rgba(0,0,0,0.3); pointer-events:auto;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <h4 style="margin:0;">Selecionar imagem</h4>
                <button type="button" class="fechar-overlay-imagens" style="background:none; border:none; font-size:1.29rem; cursor:pointer;" aria-label="Fechar">&times;</button>
            </div>
            <div style="display:flex; gap:6px; margin-bottom:10px;">
                <button type="button" id="btnCarregar" class="btn1" style="flex:1;">Carregar</button>
                <button type="button" class="btn1 sem-imagem" style="flex:1;">Sem Imagem</button>
            </div>
            <div id="erroImagens" class="text-error" role="alert" style="display:none;"></div>
            <div id="gridImagens" style="display:grid; grid-template-columns:repeat(3,1fr); gap:6px;"></div>
            <div style="margin-top:8px; text-align:center;">
                <button type="button" id="btnCarregarMais" class="btn1" style="display:none; width:100%;">Carregar mais</button>
            </div>
        </div>
    </div>
</div>
