<!DOCTYPE html>   
<html lang="pt-PT">

<?php

require_once "seguranca.php";
require_once "../controller/dashboardController.php";
$dsc = new dashboardController();


// Disciplina que mais reserva
$tabela_reservas = $dsc->listaReservasController();
$tabela_resumo = $dsc->disciplinaMaisReservasController();


// calculo da taxa de ocupação
$total_horarios = $dsc->totalHorariosController();

$total_reservas = $dsc->totalReservasController();

?>


<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">   

  <script src="js/jquery.js"></script>
  <script src="js/jquery.datetimepicker.full.js"></script>
  <script src="js/dateformat.js"></script>

  <!-- <link href="css/select2.min.css" rel="stylesheet" /> -->
  <link rel="stylesheet" type="text/css" href="css/estilo.css">
  <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

  <!-- <script src="js/select2.min.js"></script> -->
  <script src="js/lib.js"></script>

</head>
<title>Relatorios</title>

<body>

  <!-- menu esquerdo -->
  <?php include "menu_esquerdo.php"; ?>

  <!-- conteudo -->
  <div class="corpo">


    <div class="container">

      <div class="apar">

        <h3> Total de reservas por disciplina </h3>


        <table class="lista_comum" cellpadding="4" cellspacing="4">

          <tr>
            <th></th>
            <th width="300"> Nome </th>
            <th> Total</th>
          </tr>

          <?= $tabela_resumo ?>

        </table>

        

      </div>

      <div class="apar">
      <h3> Taxa de ocupação das salas </h3>
        <div id="chart_div"></div>

        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">

          // Load the Visualization API and the piechart package.
          google.load('visualization', '1.0', { 'packages': ['corechart'] });

          // Set a callback to run when the Google Visualization API is loaded.
          google.setOnLoadCallback(drawChart);

          // Callback that creates and populates a data table,
          // instantiates the pie chart, passes in the data and
          // draws it.
          function drawChart() {

            // Create the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Topping');
            data.addColumn('number', 'Slices');
            data.addRows([
              ['Ocupado', <?= $total_reservas ?>],
              ['Desocupado', <?= $total_horarios ?>],
            ]);

            // Set chart options
            var options = {
              'title': 'Taxa de ocupação da sala',
              'width': 500,
              'height': 500
            };

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
          }
        </script>


      </div>
    </div>

  </div>

</body>

</html>