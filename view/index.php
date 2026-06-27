<!DOCTYPE html>
<html lang="pt-PT">

<?php

require_once "seguranca.php";
require_once "config.php";
require_once("../controller/dashboardController.php");
require_once("../controller/util.php");

$hoje = null;
if ($param = filter_input(INPUT_GET, 'data', FILTER_UNSAFE_RAW)) {
	$hoje = Util::parseDataPt($param);
}
if ($hoje === null) {
	$hoje = new DateTime();
}

$hoje_pt = Util::traduz_data($hoje->format('D d/m/Y'), 'pt');

$dsc = new dashboardController();

// obter categoria selecionada (padrão = 0 para todas)
$categoria_id = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?? 0;

// obter lista de categorias para filtro
$categorias = $dsc->listarCategoriasController();

// gera o topo da index
$tabela_topo = $dsc->gerarTopoController();
// gerar o corpo do relatorio
$tabela_corpo = $dsc->gerarCorpoController($hoje, $categoria_id);

// configurar dias
$dia_anterior = Util::calcularDiaAdjacente($hoje, -1);
$dia_posterior = Util::calcularDiaAdjacente($hoje, 1);
$dia_prev = Util::calcularDiaController($hoje, array($dsc, 'prevController'), $categoria_id);
$dia_next = Util::calcularDiaController($hoje, array($dsc, 'nextController'), $categoria_id);

?>


<head>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">


	<script src="js/jquery.js"></script>
	<script src="js/jquery.datetimepicker.full.js"></script>
	<script src="js/dateformat.js"></script>

	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

	<style>
		:root {
			--dashboard-reservado: <?= htmlspecialchars($cfg->dashboard_colors->reservado, ENT_QUOTES, 'UTF-8') ?>;
			--dashboard-confirmado: <?= htmlspecialchars($cfg->dashboard_colors->confirmado, ENT_QUOTES, 'UTF-8') ?>;
			--dashboard-cancelado: <?= htmlspecialchars($cfg->dashboard_colors->cancelado, ENT_QUOTES, 'UTF-8') ?>;
			--dashboard-disponivel: <?= htmlspecialchars($cfg->dashboard_colors->disponivel, ENT_QUOTES, 'UTF-8') ?>;
			--dashboard-disponivel-hover: <?= htmlspecialchars($cfg->dashboard_colors->disponivel_hover, ENT_QUOTES, 'UTF-8') ?>;
			--dashboard-selected: <?= htmlspecialchars($cfg->dashboard_colors->selected, ENT_QUOTES, 'UTF-8') ?>;
			--dashboard-cell-border: <?= htmlspecialchars($cfg->dashboard_colors->cell_border, ENT_QUOTES, 'UTF-8') ?>;
			--dashboard-cell-text: <?= htmlspecialchars($cfg->dashboard_colors->cell_text, ENT_QUOTES, 'UTF-8') ?>;
		}
	</style>

	<script src="js/lib.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<link rel="icon" href="../favicon.ico" type="image/x-icon">
	<title>Reserva de Salas</title>

	<script>
		var data = <?= json_encode($hoje->format('d/m/Y')) ?>;
		var data_pt = <?= json_encode($hoje_pt) ?>;
		var dia_anterior = <?= json_encode($dia_anterior) ?>;
		var dia_posterior = <?= json_encode($dia_posterior) ?>;
		var dia_prev = <?= json_encode($dia_prev) ?>;
		var dia_next = <?= json_encode($dia_next) ?>;
		var categoria_id = <?= json_encode($categoria_id) ?>;

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
				<img src="img/double_arrow_left.png" width="30" height="30" alt="Reserva Anterior" Title="Reserva Anterior" class="nav-button<?= ($dia_prev == $hoje_pt ? ' disabled' : '') ?>" onclick="alteraData(dia_prev)" />
				<img src="img/chevron_left.png" class="nav-button" width="30" height="30" alt="Dia anterior" Title="Dia anterior" onclick="alteraData(dia_anterior)" />

				<div class="date-center">
				<form method="get" action="index.php" target="_self" name="form1">
					<input type="text" name="data" id="data" value="<?= htmlspecialchars($hoje_pt, ENT_QUOTES, 'UTF-8') ?>" class="date-input" onchange="atualizaTela(this)" readonly />
				</form>
			</div>

			<img src="img/chevron_right.png" class="nav-button" width="30" height="30" alt="Dia posterior" Title="Dia Seguinte" onclick="alteraData(dia_posterior)" />
			<img src="img/double_arrow_right.png" width="30" height="30" alt="Reserva Seguinte" Title="Reserva Seguinte" class="nav-button<?= ($dia_next == $hoje_pt ? ' disabled' : '') ?>" onclick="alteraData(dia_next)" />
		</div>

		<div class="dashboard-legend">

			<span class="dashboard-legend-item"><span class="dashboard-legend-swatch" style="background: var(--dashboard-reservado);"></span><strong>Reservado</strong></span>
			<span class="dashboard-legend-item"><span class="dashboard-legend-swatch" style="background: var(--dashboard-confirmado);"></span><strong>Confirmado</strong></span>
			<span class="dashboard-legend-item"><span class="dashboard-legend-swatch" style="background: var(--dashboard-cancelado);"></span><strong>Cancelado</strong></span>
			<span class="dashboard-legend-item"><span class="dashboard-legend-swatch" style="background: var(--dashboard-disponivel);"></span><strong>Disponível</strong></span>

			
			<select id="categoria_filter" class="dashboard-category-select" onchange="mudaCategoria(this)">
				<option value="0"<?= $categoria_id === 0 ? ' selected' : '' ?>>Todas</option>
				<?php foreach ($categorias as $categoria): ?>
					<option value="<?= htmlspecialchars($categoria['id'], ENT_QUOTES, 'UTF-8') ?>"<?= ($categoria['id'] == $categoria_id) ? ' selected' : '' ?>> 
						<?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
				<?php endforeach; ?>
			</select>

		</div>
	</div>

	<table style="border:0" cellpadding="4" cellspacing="0">
		<thead>

			<?= $tabela_topo ?>

		</thead>

	</table>

	<div class="tabelarow">
			<table style="border:0" cellpadding="4" cellspacing="0">

				<tbody>

					<?= $tabela_corpo ?>

				</tbody>

			</table>
		</div>

	</div>

</body>

</html>