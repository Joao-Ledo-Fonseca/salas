
<?php $title = 'Cadastro de Salas'; ?>

<h3>Cadastro de Salas</h3><br>Categorias:<?= htmlspecialchars((string)$categoria_filtro, ENT_QUOTES, 'UTF-8') ?> Activas:<?= htmlspecialchars((string)$activas_filtro, ENT_QUOTES, 'UTF-8') ?>

<div class="container">
    <div class="a_par">
        <div class=aviso style="color:red"><?= htmlspecialchars((string)$errormsg, ENT_QUOTES, 'UTF-8') ?></div>

        <form enctype="multipart/form-data" name="form1" method="post" action="<?= htmlspecialchars(app_url('?acao=sala_form&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)), ENT_QUOTES, 'UTF-8') ?>">

            <input type="hidden" name="id" value="<?= (int) $id ?>" />
            <input type="hidden" name="imagem_id" id="imagem_id" value="<?= htmlspecialchars((string) $imagem_id, ENT_QUOTES, 'UTF-8') ?>" />
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
            <input type="hidden" name="categoria_filtro" value="<?= htmlspecialchars((string) $categoria_filtro, ENT_QUOTES, 'UTF-8') ?>" />
            <input type="hidden" name="activas" value="<?= htmlspecialchars((string) $activas_filtro, ENT_QUOTES, 'UTF-8') ?>" />
    <input type="hidden" name="return_to" value="<?= htmlspecialchars((string) ($return_to ?? ''), ENT_QUOTES, 'UTF-8') ?>" />
    <input type="hidden" name="return_id" value="<?= (int) ($return_id ?? 0) ?>" />
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />

            <table class="tabela_comum" cellpadding="4" cellspacing="4">
                <tr>
                    <td width="100px">Nome</td>
                    <td><input type="text" name="nome" value="<?= htmlspecialchars((string) $nome, ENT_QUOTES, 'UTF-8') ?>" /> </td>
                </tr>
                <tr>
                    <td width="100px">Descrição</td>
                    <td><input type="text" name="descricao" value="<?= htmlspecialchars((string) $descricao, ENT_QUOTES, 'UTF-8') ?>" /> </td>
                </tr>
                <tr>
                    <td width="100px">Categoria</td>
                    <td>
                        <select name="categoria_id" style="width:180px" id="selecao">
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria['id'] ?>" <?= ((int) $categoria['id'] === (int) $categoria_id ? 'selected' : '') ?>><?= htmlspecialchars((string) $categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="100px">Nº Lugares</td>
                    <td><input type="text" name="lugares" value="<?= (int) $lugares ?>" /> </td>
                </tr>
                <tr>
                    <td width="100px">Activa</td>
                    <td><input type="checkbox" name='activa' value=activa <?= ($activa ? "checked" : "") ?> ></td>
                </tr>
                <tr>
                    <td width="100px">Foto</td>
                    <td>
                        <div>
                            <button type="button" id="btnFicheiro" class="btn1" onclick="abrirOverlayImagens()">Ficheiro</button>
                            <input name="imagem" id="newimagem" type="file" style="display:none;" />
                        </div>
                    </td>
                </tr>
            </table>
            <input type="submit" name="salvar" value="Salvar" class="btn1" />
            <input type="submit" name="excluir" value="Excluir" class="btn1" />
<?php
$cancelUrl = (($return_to ?? '') !== '')
    ? app_url('?acao=' . ($return_to ?? '') . '&id=' . (int) ($return_id ?? 0))
    : app_url('?acao=sala_list&categoria_id=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro));
?>
            <input type="button" value="Cancelar" class="btn1" onclick="window.location.href='<?= htmlspecialchars($cancelUrl, ENT_QUOTES, 'UTF-8') ?>'" />
        </form>

    </div>
    <div class="apar">
        <?php
        echo '<img id="imagem" src="data:image/jpeg;base64,' . base64_encode($imagem) . '"  ' . ((strlen($imagem) > 0) ? "" : "style='display:none'") . ' width=400px />';
        ?>
    </div>

    <!-- Overlay de seleção de imagem (fora do container para evitar problemas de stacking) -->
    <div id="overlayImagens" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; pointer-events:auto;">
        <div style="position:absolute; right:20px; top:60px; width:320px; max-height:80vh; overflow-y:auto; background:#fff; border:1px solid #999; border-radius:6px; padding:10px; box-shadow:0 4px 12px rgba(0,0,0,0.3); pointer-events:auto;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <h4 style="margin:0;">Selecionar imagem</h4>
                <button type="button" onclick="fecharOverlayImagens()" style="background:none; border:none; font-size:18px; cursor:pointer;">&times;</button>
            </div>
            <div style="display:flex; gap:6px; margin-bottom:10px;">
                <button type="button" id="btnCarregar" class="btn1" onclick="carregarNovaImagem()" style="flex:1;">Carregar</button>
                <button type="button" class="btn1" onclick="semImagem()" style="flex:1;">Sem Imagem</button>
            </div>
            <div id="gridImagens" style="display:grid; grid-template-columns:repeat(3,1fr); gap:6px;">
                <?php foreach (($imagens ?? []) as $img): ?>
                    <div class="img-thumb" data-id="<?= (int) $img['id'] ?>" data-base64="<?= base64_encode($img['imagem']) ?>" style="cursor:pointer; border:1px solid #ccc; border-radius:4px; overflow:hidden; text-align:center;" onclick="selecionarImagem(this)">
                        <img src="data:<?= htmlspecialchars($img['tipo_imagem'], ENT_QUOTES, 'UTF-8') ?>;base64,<?= base64_encode($img['imagem']) ?>" style="width:100%; height:60px; object-fit:cover;" />
                        <div style="font-size:9px; padding:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($img['nome_imagem'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<div class="container_conteudo" style="width:100%; margin-top:10px; box-sizing:border-box;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
        <h4 style="margin:0;">Reservas da sala</h4>
        <?php if (!empty($podeEliminarReservas)): ?>
            <form name="eliminar_reservas_form" method="post" action="<?= htmlspecialchars(app_url('?acao=sala_form&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)), ENT_QUOTES, 'UTF-8') ?>" style="margin:0;">
                <input type="hidden" name="id" value="<?= (int) $id ?>" />
                <input type="hidden" name="categoria_filtro" value="<?= htmlspecialchars((string) $categoria_filtro, ENT_QUOTES, 'UTF-8') ?>" />
                <input type="hidden" name="activas" value="<?= htmlspecialchars((string) $activas_filtro, ENT_QUOTES, 'UTF-8') ?>" />
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
                <input type="submit" name="eliminar_reservas" value="Eliminar Reservas" class="btn1" style="height:22px; padding:0 10px; font-size:11px; width:auto;" />
            </form>
        <?php endif; ?>
    </div>
    <?php if (empty($reservasSala)): ?>
        <p>Nenhuma reserva encontrada.</p>
    <?php else: ?>
        <?php $statusNomes = [1 => 'Reservado', 2 => 'Confirmado', 3 => 'Cancelado']; ?>
        <div style="width:100%; max-height:55vh; overflow-y:auto; overflow-x:auto;">
            <table class="tabela_comum" cellpadding="0" cellspacing="0" style="width:100%; min-width:600px; font-size:11px;">
                <tr style="border-bottom:1px solid #999;">
                    <th style="padding:0 3px;">ID</th>
                    <th style="padding:0 3px;">Período</th>
                    <th style="padding:0 3px;">Dia</th>
                    <th style="padding:0 3px;">Entidade</th>
                    <th style="padding:0 3px;">Uso</th>
                    <th style="padding:0 3px;">Status</th>
                    <th style="padding:0 3px;">Utilizador</th>
                    <th style="padding:0 3px;">Eliminar</th>
                </tr>
                <?php foreach ($reservasSala as $reserva): ?>
                    <tr style="line-height:1.0;">
                        <td style="padding:0 3px;"><?= (int) $reserva['id'] ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars((string) $reserva['periodo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars((string) $reserva['dia'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars((string) $reserva['professor_desc'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars((string) $reserva['disciplina_desc'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars($statusNomes[(int)$reserva['status']] ?? (string)$reserva['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;"><?= htmlspecialchars((string) $reserva['usuario'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="padding:0 3px;">
                            <?php if ($podeEliminarReservas): ?>
                                <form method="post" action="<?= htmlspecialchars(app_url('?acao=sala_form&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)), ENT_QUOTES, 'UTF-8') ?>" style="display:inline; margin:0;">
                                    <input type="hidden" name="id" value="<?= (int) $id ?>" />
                                    <input type="hidden" name="categoria_filtro" value="<?= htmlspecialchars((string) $categoria_filtro, ENT_QUOTES, 'UTF-8') ?>" />
                                    <input type="hidden" name="activas" value="<?= htmlspecialchars((string) $activas_filtro, ENT_QUOTES, 'UTF-8') ?>" />
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
                                    <input type="hidden" name="eliminar_reserva" value="1" />
                                    <input type="hidden" name="reserva_id" value="<?= (int) $reserva['id'] ?>" />
                                    <button type="submit" class="btn1" style="width:60px; height:18px; padding:0; font-size:10px;">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <hr style="border:0; border-top:1px solid #999; margin:0;" />
        </div>
    <?php endif; ?>
</div>



<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imagem').attr('src', e.target.result);
                $('#imagem').css('display', 'block');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#newimagem").change(function () {
        readURL(this);
    });

    // Move overlay to body to escape .corpo overflow clipping
    (function() {
        function moveOverlay() {
            var overlay = document.getElementById('overlayImagens');
            if (overlay && overlay.parentNode !== document.body) {
                document.body.appendChild(overlay);
            }
            // Close overlay when clicking on the backdrop
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) {
                        fecharOverlayImagens();
                    }
                });
            }
            // Ensure overlay and children are clickable
            if (overlay) {
                overlay.style.pointerEvents = 'auto';
                var inner = overlay.querySelector('div');
                if (inner) {
                    inner.style.pointerEvents = 'auto';
                }
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', moveOverlay);
        } else {
            setTimeout(moveOverlay, 0);
        }
    })();

    function abrirOverlayImagens() {
        var overlay = document.getElementById('overlayImagens');
        overlay.style.display = 'block';
    }

    // Event delegation for overlay clicks (fallback if inline onclick fails)
    document.addEventListener('click', function(e) {
        var target = e.target;
        var overlay = document.getElementById('overlayImagens');
        // Close overlay when clicking on backdrop
        if (overlay && target === overlay) {
            fecharOverlayImagens();
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        // Check if clicked on a thumbnail
        var thumb = target.closest ? target.closest('.img-thumb') : null;
        if (thumb && thumb.getAttribute('data-id')) {
            selecionarImagem(thumb);
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        // Check if clicked on Carregar button
        if (target.id === 'btnCarregar' || (target.textContent && target.textContent.trim() === 'Carregar')) {
            carregarNovaImagem();
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        // Check if clicked on Sem Imagem button
        if (target.textContent && target.textContent.trim() === 'Sem Imagem') {
            semImagem();
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        // Check if clicked on close button
        if (target.getAttribute && target.getAttribute('onclick') === 'fecharOverlayImagens()') {
            fecharOverlayImagens();
            e.preventDefault();
            e.stopPropagation();
            return;
        }
    });
    function fecharOverlayImagens() {
        document.getElementById('overlayImagens').style.display = 'none';
    }
    function selecionarImagem(el) {
        var id = el.getAttribute('data-id');
        var b64 = el.getAttribute('data-base64');
        document.getElementById('imagem_id').value = id;
        var img = document.getElementById('imagem');
        img.src = 'data:image/jpeg;base64,' + b64;
        img.style.display = 'block';
        fecharOverlayImagens();
    }
    function semImagem() {
        document.getElementById('imagem_id').value = '';
        document.getElementById('imagem').style.display = 'none';
        fecharOverlayImagens();
    }
    function carregarNovaImagem() {
        fecharOverlayImagens();
        document.getElementById('newimagem').click();
    }
    function renderizaNovaImagem() {
        var input = document.getElementById('newimagem');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.getElementById('imagem');
                img.src = e.target.result;
                img.style.display = 'block';
                document.getElementById('imagem_id').value = '';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    document.getElementById('newimagem').addEventListener('change', renderizaNovaImagem);
</script>