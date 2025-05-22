<?php

require_once "seguranca.php";
require_once("../controller/dashboardController.php");


if (isset($_GET['data'])) {
	$hoje_pt = $_GET['data'];
	$hoje = date_create_from_format('D d/m/Y', traduz_data($hoje_pt, 'en'));
} else {
	$hoje = new DateTime();
	$hoje_pt = traduz_data($hoje->format('D d/m/Y'), 'pt');
}

function traduz_data($date, $lang = 'en')
{
	$meses_pt = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez', 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
	$meses_en = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Sun', 'Mon', 'Tue', 'Thu', 'Wed', 'Fri', 'Sat'];
	if ($lang == 'en') {
		return str_replace($meses_pt, $meses_en, $date);
	} else {
		return str_replace($meses_en, $meses_pt, $date);
	}
}

$dsc = new dashboardController();

// gera o topo da index
$tabela_topo = $dsc->gerarTopoController();
// gerar o corpo do relatorio
$tabela_corpo = $dsc->gerarCorpoController($hoje);


// configurar dias
$dia_anterior = date_create_from_format('d/m/Y', $hoje->format("d/m/Y"));
$dia_anterior->modify('-1 day');
$dia_anterior = traduz_data($dia_anterior->format("D d/m/Y"), 'pt');

$dia_posterior = date_create_from_format('d/m/Y', $hoje->format("d/m/Y"));
$dia_posterior->modify('+1 day');
$dia_posterior = traduz_data($dia_posterior->format("D d/m/Y"), 'pt');

$dia_prev = date_create_from_format('d/m/Y', $hoje->format("d/m/Y"));
$dia_prev = $dsc->prevController($dia_prev);
$dia_prev = traduz_data($dia_prev->format("D d/m/Y"), 'pt');

$dia_next = date_create_from_format('d/m/Y', $hoje->format('d/m/Y'));
$dia_next = $dsc->nextController($dia_next);
$dia_next = traduz_data($dia_next->format("D d/m/Y"), 'pt');

?>

<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">


	<script src="js/jquery.js"></script>
	<script src="js/jquery.datetimepicker.full.js"></script>
	<script src="js/dateformat.js"></script>

	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

	<script src="js/lib.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<script>

		// variavel com a data

		var data = '<?= $hoje->format("d/m/Y") ?>';
		var data_pt = '<?= $hoje_pt ?>';
		var dia_anterior = '<?= $dia_anterior; ?>';
		var dia_posterior = '<?= $dia_posterior; ?>';
		var dia_prev = '<?= $dia_prev; ?>';
		var dia_next = '<?= $dia_next; ?>';



		// configura o calendario
		jQuery.datetimepicker.setLocale('pt');

		// configura a area visivel do formulario
		$(window).on('scroll resize load', getVisible);


		$(window).load(function () {
			$('#data').datetimepicker({
				timepicker: false,
				format: 'D d/m/Y'
			});

		});


		function atualizaTela(o) {
			// pegar os dados da data atual e atualizar a tela
			window.location.href = "index.php?data=" + $(o).val();
		}

		function alteraData(data_arg) {
			window.location.href = "index.php?data=" + data_arg;
		}


		/* Set the width of the sidebar to 250px (show it) */
		function openNav() {
  			var x = document.getElementsByClassName("menu");
			// x[0].style.width = "250px";
			x[0].style.display = "inline-block";
		}

		/* Set the width of the sidebar to 0 (hide it) */
		function closeNav() {
  			document.getElementById("menu").style.width = "0";
		}

	</script>

	<title>Reserva de Salas</title>
	
	
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="48x48" href="../favicon.ico">
	<link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
    <link rel="manifest" href="../site.webmanifest">   
	
</head>

<body>



	<!-- menu esquerdo -->
	<?php include "menu_esquerdo.php"; ?>

	<!-- conteudo -->
	<div class="corpo">

		<div class="hamb" style="font-size:40px; color: black; float:left" onclick="openNav()">&#9776;</div>

		<div class="titulo_inicial" style="font-size:30pt;vertical-align:middle">

			<!--
			<span class="left" style="font-size:30pt;padding-right:30px;vertical-align:middle" onclick="alteraData(dia_prev)">&#171;</span>
			<span  class="left" style="font-size:30pt;padding-right:30px;vertical-align:middle" onclick="alteraData(dia_anterior)">&#8249;</span>
			-->
			<img src="img/double_arrow_left.png" class="left" width="30" height="30" alt="" <?= ($dia_prev == $hoje_pt) ? 'style="opacity: 0.3"' : ''; ?> onclick="alteraData(dia_prev)" />
			<img src="img/chevron_left.png" class="left" width="30" height="30" alt=""
				onclick="alteraData(dia_anterior)" />

			<div>
				<form method="get" action="index.php" target="_self" name="form1">
					<input type="text" name="data" id="data" value="<?= $hoje_pt ?>" style="size:200px"
						onchange="atualizaTela(this)" readonly />
				</form>
			</div>


			<img src="img/chevron_right.png" class="right" width="30" height="30" alt=""
				onclick="alteraData(dia_posterior)" />
			<img src="img/double_arrow_right.png" class="right" width="30" height="30" alt="" <?= ($dia_next == $hoje_pt) ? 'style="opacity: 0.3"' : ''; ?> onclick="alteraData(dia_next)" />


			<!--
				<span class="left" style="font-size:30pt;padding-left:30px;vertical-align:middle" onclick="alteraData(dia_posterior)">&#8250; </span>   
				<span class="left" style="font-size:30pt;padding-left:30px;vertical-align:middle" onclick="alteraData(dia_next)"> &#187;</span>
			-->
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

	<!-- form -->
	<div class="form"></div>

</body>

</html>