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

<!DOCTYPE html>
<html lang="pt-PT">

<?php
?>
<head>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">


	<script src="<?= app_url('/js/jquery.js') ?>"></script>
	<script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>
	<script src="<?= app_url('/js/dateformat.js') ?>"></script>

	<script>window.__APP_THEME__ = <?= json_encode((isset($cfg) && isset($cfg->theme) ? $cfg->theme->key : 'light'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
	<script>window.__APP_THEMES__ = <?= json_encode((isset($cfg) && isset($cfg->availableThemes) ? $cfg->availableThemes : ['light', 'dark']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
	<script src="<?= app_url('/js/theme.js') ?>"></script>

	<link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">

	<?php if (!empty($cfg) && isset($cfg->theme)): ?>
	<style>
		:root {
			<?= $cfg->theme->toCssVariables() ?>
		}
	</style>
	<?php endif; ?>

	<script src="<?= app_url('/js/lib.js') ?>"></script>
	<script src="<?= app_url('/js/drag.js') ?>"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<link rel="icon" href="<?= app_url('/favicon.ico') ?>" type="image/x-icon">
	<title>Reserva de Salas</title>

	<script>
		var data = <?= json_encode($dashboardView['date']->format('d/m/Y')) ?>;
		var data_pt = <?= json_encode($dashboardView['dateLabel']) ?>;
		var dia_anterior = <?= json_encode($dashboardView['previousDay']) ?>;
		var dia_posterior = <?= json_encode($dashboardView['nextDay']) ?>;
		var dia_prev = <?= json_encode($dashboardView['previousReservation']) ?>;
		var dia_next = <?= json_encode($dashboardView['nextReservation']) ?>;
		var categoria_id = <?= json_encode($dashboardView['categoryId']) ?>;
		var usuario_id = <?= json_encode($dashboardView['usuarioId']) ?>;
		var salaInfos = <?= json_encode(array_values(array_map(function ($r) {
			return [
				'nome' => (string) ($r['nome'] ?? ''),
				'descricao' => (string) ($r['descricao'] ?? ''),
				'capacidade' => (int) ($r['capacidade'] ?? 0),
				'img' => !empty($r['imagem_base64']) ? ('data:' . (($r['imagem_tipo'] ?? '') ?: 'image/jpeg') . ';base64,' . $r['imagem_base64']) : '',
			];
		}, $dashboardView['rooms'])), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

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
					var info = (typeof salaInfos !== 'undefined' && salaInfos[idx]) ? salaInfos[idx] : {};
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
		$(window).on('scroll resize load', getVisible);
		$(window).on('load', function () {
			$('#data').datetimepicker({
				timepicker: false,
				format: 'D d/m/Y'
			});
		});

		/**
		 * Método buildDashboardUrl.
		 * @param data_ar $g
		 * @param categori $a
		 * @return mixed
		 */
		function buildDashboardUrl(data_arg, categoria) {
			var url = 'index.php?data=' + encodeURIComponent(data_arg);
			if (categoria && categoria !== 0) {
				url += '&categoria=' + encodeURIComponent(categoria);
			}
			var usuario = document.getElementById('usuario_filter') ? document.getElementById('usuario_filter').value : usuario_id;
			if (usuario && usuario !== 0) {
				url += '&usuario=' + encodeURIComponent(usuario);
			}
			return url;
		}

		/**
		 * Método atualizaTela.
		 * @param mixed $o
		 * @return mixed
		 */
		function atualizaTela(o) {
			var categoria = document.getElementById('categoria_filter') ? document.getElementById('categoria_filter').value : categoria_id;
			window.location.href = buildDashboardUrl($(o).val(), categoria);
		}

		/**
		 * Método alteraData.
		 * @param data_ar $g
		 * @return mixed
		 */
		function alteraData(data_arg) {
			var categoria = document.getElementById('categoria_filter') ? document.getElementById('categoria_filter').value : categoria_id;
			window.location.href = buildDashboardUrl(data_arg, categoria);
		}

		/**
		 * Método mudaUsuario.
		 * @param selec $t
		 * @return mixed
		 */
		function mudaUsuario(select) {
			var usuario = select.value;
			var data_atual = document.getElementById('data') ? document.getElementById('data').value : data;
			window.location.href = buildDashboardUrl(data_atual, categoria_id);
		}

		/**
		 * Método mudaCategoria.
		 * @param selec $t
		 * @return mixed
		 */
		function mudaCategoria(select) {
			var categoria = select.value;
			var data_atual = document.getElementById('data') ? document.getElementById('data').value : data;
			window.location.href = buildDashboardUrl(data_atual, categoria);
		}
	</script>


</head>

<body>

	<!-- form -->
	<div class="form"></div>

	<!-- menu esquerdo -->
	<?php include __DIR__ . "/MenuEsquerdo.php"; ?>

	<!-- conteudo -->
	<div class="corpo">

		<div class="titulo_inicial">

			<div class="toolbar-row">
				<img src="<?= app_url('/img/double_arrow_left.png') ?>" width="26" height="26" alt="Reserva Anterior"
					Title="Reserva Anterior" class="nav-button<?= ($dashboardView['previousReservation'] == $dashboardView['dateLabel'] ? ' disabled' : '') ?>"
					onclick="alteraData(dia_prev)" />
				<img src="<?= app_url('/img/chevron_left.png') ?>" class="nav-button" width="26" height="26" alt="Dia anterior"
					Title="Dia anterior" onclick="alteraData(dia_anterior)" />

				<div class="date-center">
					<form method="get" action="index.php" target="_self" name="form1">
						<input type="text" name="data" id="data"
							value="<?= htmlspecialchars($dashboardView['dateLabel'], ENT_QUOTES, 'UTF-8') ?>" class="date-input"
							onchange="atualizaTela(this)" readonly />
					</form>
				</div>

				<img src="<?= app_url('/img/chevron_right.png') ?>" class="nav-button" width="26" height="26" alt="Dia posterior"
					Title="Dia Seguinte" onclick="alteraData(dia_posterior)" />
				<img src="<?= app_url('/img/double_arrow_right.png') ?>" width="26" height="26" alt="Reserva Seguinte"
					Title="Reserva Seguinte" class="nav-button<?= ($dashboardView['nextReservation'] == $dashboardView['dateLabel'] ? ' disabled' : '') ?>"
					onclick="alteraData(dia_next)" />
			</div>

			<div class="dashboard-legend">

				<span class="dashboard-legend-item"><span class="dashboard-legend-swatch"
						style="background: var(--dashboard-reservado);"></span><strong>Reservado</strong></span>
				<span class="dashboard-legend-item"><span class="dashboard-legend-swatch"
						style="background: var(--dashboard-confirmado);"></span><strong>Confirmado</strong></span>
				<span class="dashboard-legend-item"><span class="dashboard-legend-swatch"
						style="background: var(--dashboard-cancelado);"></span><strong>Cancelado</strong></span>
				<span class="dashboard-legend-item"><span class="dashboard-legend-swatch"
						style="background: var(--dashboard-disponivel);"></span><strong>Disponível</strong></span>


				<div style="display:inline-block; vertical-align:top;">
				<div class="filter-select-wrapper">
				<select id="categoria_filter" class="dashboard-category-select" onchange="mudaCategoria(this)" title="Filtrar por categoria (sala)">
					<option value="0" <?= $dashboardView['categoryId'] === 0 ? ' selected' : '' ?>>Todas</option>
					<?php foreach ($dashboardView['categories'] as $categoria): ?>
						<option value="<?= htmlspecialchars($categoria['id'], ENT_QUOTES, 'UTF-8') ?>"
							<?= ($categoria['id'] == $dashboardView['categoryId']) ? ' selected' : '' ?>>
							<?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
						<?php endforeach; ?>
				</select>
				<svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M 3,4 C 3,3.45 3.45,3 4,3 L 20,3 C 20,3 20.35,3.05 20.6,3.3 C 20.85,3.55 21,4 21,4 L 14,12 L 14,18 L 10,21 L 10,12 Z"/></svg>
				</div>
				<div style="font-size:9px; text-align:left; margin-top:1px; line-height:1;">Salas</div>
				</div>

				<?php if (!empty($dashboardView['showUserFilter'])): ?>
				<div style="display:inline-block; vertical-align:top;">
				<div class="filter-select-wrapper">
				<select id="usuario_filter" class="dashboard-category-select" onchange="mudaUsuario(this)" title="Filtrar por utilizador">
					<option value="0" <?= $dashboardView['usuarioId'] === 0 ? ' selected' : '' ?>>Todos</option>
					<?php foreach ($dashboardView['users'] as $u): ?>
						<option value="<?= htmlspecialchars($u['id'], ENT_QUOTES, 'UTF-8') ?>"
							<?= ($u['id'] == $dashboardView['usuarioId']) ? ' selected' : '' ?>>
							<?= htmlspecialchars($u['nome'], ENT_QUOTES, 'UTF-8') ?></option>
						<?php endforeach; ?>
				</select>
				<svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M 3,4 C 3,3.45 3.45,3 4,3 L 20,3 C 20,3 20.35,3.05 20.6,3.3 C 20.85,3.55 21,4 21,4 L 14,12 L 14,18 L 10,21 L 10,12 Z"/></svg>
				</div>
				<div style="font-size:9px; text-align:left; margin-top:1px; line-height:1;">User</div>
				</div>
				<?php endif; ?>

			</div>
		</div>

		<table class="dashboard-table" style="border:0" cellpadding="2" cellspacing="0">
			<thead>

				<tr>
					<th>Sala</th>
					<?php foreach ($dashboardView['periods'] as $period): ?>
						<th><?= htmlspecialchars($period['nome'], ENT_QUOTES, 'UTF-8') ?></th>
					<?php endforeach; ?>
				</tr>

			</thead>

		</table>

		<div class="tabelarow">
			<table class="dashboard-table" style="border:0" cellpadding="2" cellspacing="0">

				<tbody>

					<?php foreach ($dashboardView['rooms'] as $roomIndex => $room): ?>
						<tr>
							<td><span class="sala-nome" data-room-index="<?= (int) $roomIndex ?>"><?= htmlspecialchars($room['nome'], ENT_QUOTES, 'UTF-8') ?></span></td>
							<?php foreach ($room['cells'] as $cell): ?>
								<td <?= $cell['can_open'] ? 'onClick="abreReserva(this)"' : '' ?>
									class="<?= htmlspecialchars($cell['classe'], ENT_QUOTES, 'UTF-8') ?><?= !empty($cell['tooltip']) ? ' reserva-has-tooltip' : '' ?>"
									id="<?= htmlspecialchars((string) $cell['id'], ENT_QUOTES, 'UTF-8') ?>"
									data-sala="<?= htmlspecialchars((string) $cell['sala_id'], ENT_QUOTES, 'UTF-8') ?>"
									data-periodo="<?= htmlspecialchars((string) $cell['periodo_id'], ENT_QUOTES, 'UTF-8') ?>"
									data-usuario_id="<?= htmlspecialchars((string) $cell['usuario_id'], ENT_QUOTES, 'UTF-8') ?>"
									<?= !empty($cell['tooltip']) ? 'title="' . htmlspecialchars($cell['tooltip'], ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
									<?= htmlspecialchars((string) $cell['uso'], ENT_QUOTES, 'UTF-8') ?>&nbsp;<hr>&nbsp;<?= htmlspecialchars((string) $cell['entidade'], ENT_QUOTES, 'UTF-8') ?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>

				</tbody>

			</table>
		</div>

	</div>

</body>

</html>
