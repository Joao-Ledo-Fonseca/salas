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
use Salas\Core\Validation\Validator;
?>

 <?php
  $titulo = 'Cadastro de Salas';
  include __DIR__ . "/components/cabecalho2.php";
  ?>



<div class="lista_comum container_top" style="max-height:60vh; overflow-y:auto;">
    <form class="form_sel" name="form1" method="post" action="<?= app_url('?acao=sala_list') ?>">

        <div style="float:left;">
            <input type="button" name="novo" value="Novo" class="btn1"
                   onclick="abre('sala_form.php?categoria_filtro=<?= (int)$categoria_id ?>&activas=<?= htmlspecialchars((string)$activas, ENT_QUOTES, 'UTF-8') ?>')" />
        </div>

        <span style="float:right; margin-left: 15px;">
            <label for="categoria"><b>Categorias</b></label>
            <div class="filter-select-wrapper">
                <select id="categoria" class="dashboard-category-select" style="width:160px;" name="categoria_id" onchange="this.form.submit()">
                    <option value="0" <?= $categoria_id === 0 ? 'selected' : '' ?>>Todas</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int) $categoria['id'] ?>" <?= ((int) $categoria['id'] === $categoria_id ? 'selected' : '') ?>><?= htmlspecialchars((string) $categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M 3,4 C 3,3.45 3.45,3 4,3 L 20,3 C 20,3 20.35,3.05 20.6,3.3 C 20.85,3.55 21,4 21,4 L 14,12 L 14,18 L 10,21 L 10,12 Z"/></svg>
            </div>
        </span>

        <div style="float:right; width:140px">
            <input type="radio" id="activa" name="activas" value="activas" <?= ($activas=="activas"?'checked':'') ?> onchange="this.form.submit()" />
            <label for="activa" class="dashboard-radio-text"><strong>Só activas</strong></label><br>
            <input type="radio" id="inactiva" name="activas" value="inactivas" <?= ($activas=="inactivas"?'checked':'') ?> onchange="this.form.submit()" />
            <label for="inactiva" class="dashboard-radio-text"><strong>Inactivas</strong></label><br>
            <input type="radio" id="todas" name="activas" value="todas" <?= ($activas=="todas"?'checked':'') ?> onchange="this.form.submit()" />
            <label for="todas" class="dashboard-radio-text"><strong>Todas</strong></label><br>
        </div>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
    </form>
</div>

<div class="container_conteudo" style="max-height:60vh; overflow-y:auto;">
    <table class="lista_comum" cellpadding="4" cellspacing="4">
        <thead>
            <tr>
                <th width="150px">Categoria</th>
                <th width="150px">Nome</th>
                <th width="200px">Descrição</th>
                <th width="80px">Nº Lugares</th>
                <th width="200px">Activa</th>
                <th width="50px">ID</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $categoriaAnterior = null; ?>
            <?php foreach ($lista as $sala): ?>
                <?php $categoriaAtual = (string) $sala['categoria']; ?>
                <tr>
                    <td style="border:none"><?= $categoriaAtual === $categoriaAnterior ? '' : htmlspecialchars($categoriaAtual, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a class="sala-nome" data-room-index="<?= (int) $sala['id'] ?>" href="<?= htmlspecialchars(app_url('?acao=sala_form&id=' . (int) $sala['id'] . '&categoria_filtro=' . rawurlencode((string) $categoria_id) . '&activas=' . rawurlencode((string) $activas)), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $sala['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars((string) $sala['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $sala['lugares'] ?></td>
                    <td><?= !empty($sala['activa']) ? '&#10003;' : '-' ?></td>
                    <td><?= (int) $sala['id'] ?></td>
                    <td></td>
                </tr>
                <?php $categoriaAnterior = $categoriaAtual; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
var salaInfos = {
<?php foreach ($lista as $sala): ?>
  <?= (int) $sala['id'] ?>: {
    nome: <?= json_encode((string) $sala['nome'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
    descricao: <?= json_encode((string) $sala['descricao'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
    capacidade: <?= (int) $sala['lugares'] ?>,
    img: <?= json_encode(!empty($sala['imagem']) ? ('data:' . (($sala['tipo_imagem'] ?? '') ?: 'image/jpeg') . ';base64,' . base64_encode($sala['imagem'])) : '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
  },
<?php endforeach; ?>
};
$(function () {
  var card = $('<div id="sala-card" class="sala-card"></div>').appendTo('body');
  function escapeHtml(text) {
    return $('<div>').text(text == null ? '' : String(text)).html();
  }
  var hoverTimer = null;
  $(document).on('mouseenter', '.sala-nome', function () {
    var $el = $(this);
    hoverTimer = setTimeout(function () {
      var idx = $el.data('room-index');
      var info = salaInfos[idx] || {};
      var html = '<strong class="sala-card-title">' + escapeHtml(info.nome || '') + '</strong>';
      if (info.descricao) {
        html += '<p class="sala-card-desc">' + escapeHtml(info.descricao) + '</p>';
      }
      html += '<p class="sala-card-cap"><strong>Capacidade:</strong> ' + (info.capacidade || 0) + '</p>';
      if (info.img) {
        html += '<img src="' + info.img + '" class="sala-card-img" alt="" />';
      }
      card.html(html).show();
      var offset = $el.offset();
      card.css({
        top: offset.top + $el.outerHeight() + 4,
        left: offset.left
      });
    }, 1000);
  });
  $(document).on('mouseleave', '.sala-nome', function () {
    if (hoverTimer) { clearTimeout(hoverTimer); hoverTimer = null; }
    card.hide();
  });
});
</script>
