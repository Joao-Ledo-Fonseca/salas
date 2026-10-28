<!DOCTYPE html>
<html lang="pt-PT">


<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">   

  <script src="<?= app_url('/js/jquery.js') ?>"></script>
  <script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>
  <script src="<?= app_url('/js/dateformat.js') ?>"></script>

  <!-- <link href="<?= app_url('/css/select2.min.css') ?>" rel="stylesheet" /> -->
  <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
  <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">

  <!-- <script src="<?= app_url('/js/select2.min.js') ?>"></script> -->
  <script src="<?= app_url('/js/lib.js') ?>"></script>

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

          <?php if (empty($resumo_reservas)): ?>
            <tr><td></td><td colspan="2">Nenhum registo encontrado para este período.</td></tr>
          <?php else: ?>
            <?php foreach ($resumo_reservas as $resumo): ?>
              <tr>
                <td></td>
                <td width="300"><?= htmlspecialchars((string) $resumo['disciplina_desc'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $resumo['total'] ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>

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
