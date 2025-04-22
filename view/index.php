<?php

require_once "seguranca.php";
require_once("../controller/dashboardController.php");

$dsc = new dashboardController();


if (isset($_GET['data'])) {
	$hoje = date_create_from_format('d/m/Y', $_GET['data']);
} else {
	$hoje = new DateTime();
}


// gera o topo da index
$tabela_topo = $dsc->gerarTopoController();

// gerar o corpo do relatorio
$tabela_corpo = $dsc->gerarCorpoController($hoje);

// configurar dias
$dia_anterior = date_create_from_format('d/m/Y', $hoje->format("d/m/Y"));
$dia_anterior->modify('-1 day');

$dia_posterior = date_create_from_format('d/m/Y', $hoje->format("d/m/Y"));
$dia_posterior->modify('+1 day');



$dia_prev = date_create_from_format('d/m/Y', $hoje->format("d/m/Y"));
$dia_prev = $dsc->prevController($dia_prev);

$dia_next = date_create_from_format('d/m/Y', $hoje->format('d/m/Y'));
$dia_next = $dsc->nextController($dia_next); 

?>

<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<script src="js/jquery.js"></script>
	<script src="js/jquery.datetimepicker.full.js"></script>
	<script src="js/dateformat.js"></script>

	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

	<script src="js/lib.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<script>

		// variavel com a data

		var data = '<?= $hoje->format("d/m/Y"); ?>';
		var dia_anterior = '<?= $dia_anterior->format("d/m/Y"); ?>';
		var dia_posterior = '<?= $dia_posterior->format("d/m/Y"); ?>';
		var dia_prev = '<?= $dia_prev->format("d/m/Y"); ?>';
		var dia_next = '<?= $dia_next->format("d/m/Y"); ?>';



		// configura o calendario
		jQuery.datetimepicker.setLocale('pt');

		// configura a area visivel do formulario
		$(window).on('scroll resize load', getVisible);


		$(window).load(function () {
			$('#data').datetimepicker({
				timepicker: false,
				format: 'd/m/Y'
			});

		});


		function atualizaTela(o) {
			// pegar os dados da data atual e atualizar a tela
			window.location.href = "index.php?data=" + $(o).val();
		}

		function alteraData(data) {
			window.location.href = "index.php?data=" + data;
		}



	</script>

	<title>Reserva de Salas</title>
</head>

<body>

	<!-- form -->
	<div class="form">

	</div>


	<!-- menu esquerdo -->
	<?php 
		include "menu_esquerdo.php"; 
	?>

	<!-- conteudo -->
	<div class="corpo">

		<div class="titulo_inicial">

			<img src="img/voltar.png" width="40" height="40" alt="" onclick="alteraData(dia_prev)" />
			<img src="img/voltar.png" width="40" height="40" alt="" onclick="alteraData(dia_anterior)" />

			<div>
				<form method="get" action="index.php" target="_self" name="form1">
					<input type="text" readonly="readonly" name="data" id="data" value="<?= $hoje->format("D d/m/Y"); ?>"
						onchange="atualizaTela(this)" />
				</form>
			</div>

			<img src="img/avancar.png" width="40" height="40" alt="" onclick="alteraData(dia_posterior)" />
			<img src="img/avancar.png" width="40" height="40" alt="" onclick="alteraData(dia_next)" />			

		</div>
		
		<table border="0" cellpadding="4" cellspacing="0">

			<thead>

				<?= $tabela_topo ?>

			</thead>

		</table>

		<div class="tabelarow">
			<table border="0" cellpadding="4" cellspacing="0">

				<tbody>

					<?= $tabela_corpo ?>

				</tbody>

			</table>
		</div>

	</div>


</body>

</html>