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

	<link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">

	<style>
		:root {
			--dashboard-reservado:
				<?= htmlspecialchars($cfg->dashboard_colors->reservado, ENT_QUOTES, 'UTF-8') ?>;
				--dashboard-confirmado:
				<?= htmlspecialchars($cfg->dashboard_colors->confirmado, ENT_QUOTES, 'UTF-8') ?>;
				--dashboard-cancelado:
				<?= htmlspecialchars($cfg->dashboard_colors->cancelado, ENT_QUOTES, 'UTF-8') ?>;
				--dashboard-disponivel:
				<?= htmlspecialchars($cfg->dashboard_colors->disponivel, ENT_QUOTES, 'UTF-8') ?>;
				--dashboard-disponivel-hover:
				<?= htmlspecialchars($cfg->dashboard_colors->disponivel_hover, ENT_QUOTES, 'UTF-8') ?>;
				--dashboard-selected:
				<?= htmlspecialchars($cfg->dashboard_colors->selected, ENT_QUOTES, 'UTF-8') ?>;
				--dashboard-cell-border:
				<?= htmlspecialchars($cfg->dashboard_colors->cell_border, ENT_QUOTES, 'UTF-8') ?>;
				--dashboard-cell-text:
				<?= htmlspecialchars($cfg->dashboard_colors->cell_text, ENT_QUOTES, 'UTF-8') ?>;
		}


	</style>

	<script src="<?= app_url('/js/lib.js') ?>"></script>
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

		jQuery.datetimepicker.setLocale('pt');
		$(window).on('scroll resize load', getVisible);
		$(window).on('load', function () {
			$('#data').datetimepicker({
				timepicker: false,
				format: 'D d/m/Y'
			});
		});

		function buildDashboardUrl(data_arg, categoria) {
			var url = 'index.php?data=' + encodeURIComponent(data_arg);
			if (categoria && categoria !== 0) {
				url += '&categoria=' + encodeURIComponent(categoria);
			}
			return url;
		}

		function atualizaTela(o) {
			var categoria = document.getElementById('categoria_filter') ? document.getElementById('categoria_filter').value : categoria_id;
			window.location.href = buildDashboardUrl($(o).val(), categoria);
		}

		function alteraData(data_arg) {
			var categoria = document.getElementById('categoria_filter') ? document.getElementById('categoria_filter').value : categoria_id;
			window.location.href = buildDashboardUrl(data_arg, categoria);
		}

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
	<?php include "menu_esquerdo.php"; ?>

	<!-- conteudo -->
	<div class="corpo">

		<div class="titulo_inicial">

			<div class="toolbar-row">
				<img src="<?= app_url('/img/double_arrow_left.png') ?>" width="30" height="30" alt="Reserva Anterior"
					Title="Reserva Anterior" class="nav-button<?= ($dashboardView['previousReservation'] == $dashboardView['dateLabel'] ? ' disabled' : '') ?>"
					onclick="alteraData(dia_prev)" />
				<img src="<?= app_url('/img/chevron_left.png') ?>" class="nav-button" width="30" height="30" alt="Dia anterior"
					Title="Dia anterior" onclick="alteraData(dia_anterior)" />

				<div class="date-center">
					<form method="get" action="index.php" target="_self" name="form1">
						<input type="text" name="data" id="data"
							value="<?= htmlspecialchars($dashboardView['dateLabel'], ENT_QUOTES, 'UTF-8') ?>" class="date-input"
							onchange="atualizaTela(this)" readonly />
					</form>
				</div>

				<img src="<?= app_url('/img/chevron_right.png') ?>" class="nav-button" width="30" height="30" alt="Dia posterior"
					Title="Dia Seguinte" onclick="alteraData(dia_posterior)" />
				<img src="<?= app_url('/img/double_arrow_right.png') ?>" width="30" height="30" alt="Reserva Seguinte"
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


				<select id="categoria_filter" class="dashboard-category-select" onchange="mudaCategoria(this)">
					<option value="0" <?= $dashboardView['categoryId'] === 0 ? ' selected' : '' ?>>Todas</option>
					<?php foreach ($dashboardView['categories'] as $categoria): ?>
						<option value="<?= htmlspecialchars($categoria['id'], ENT_QUOTES, 'UTF-8') ?>"
							<?= ($categoria['id'] == $dashboardView['categoryId']) ? ' selected' : '' ?>>
							<?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
						<?php endforeach; ?>
				</select>

			</div>
		</div>

		<table style="border:0" cellpadding="4" cellspacing="0">
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
			<table style="border:0" cellpadding="4" cellspacing="0">

				<tbody>

					<?php foreach ($dashboardView['rooms'] as $room): ?>
						<tr>
							<td><?= htmlspecialchars($room['nome'], ENT_QUOTES, 'UTF-8') ?></td>
							<?php foreach ($room['cells'] as $cell): ?>
								<td <?= $cell['can_open'] ? 'onClick="abreReserva(this)"' : '' ?>
									class="<?= htmlspecialchars($cell['classe'], ENT_QUOTES, 'UTF-8') ?>"
									id="<?= htmlspecialchars((string) $cell['id'], ENT_QUOTES, 'UTF-8') ?>"
									data-sala="<?= htmlspecialchars((string) $cell['sala_id'], ENT_QUOTES, 'UTF-8') ?>"
									data-periodo="<?= htmlspecialchars((string) $cell['periodo_id'], ENT_QUOTES, 'UTF-8') ?>"
									data-usuario_id="<?= htmlspecialchars((string) $cell['usuario_id'], ENT_QUOTES, 'UTF-8') ?>">
									<?= htmlspecialchars((string) $cell['disciplina'], ENT_QUOTES, 'UTF-8') ?>&nbsp;<hr>&nbsp;<?= htmlspecialchars((string) $cell['professor'], ENT_QUOTES, 'UTF-8') ?>
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