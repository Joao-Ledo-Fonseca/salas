<?php

require_once "seguranca.php";
require_once "../controller/dashboardController.php";
$dsc = new dashboardController();


// Disciplina que mais reserva
$tabela_reservas = $dsc->listaReservasController();


?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  <script src="js/jquery.js"></script>
  <script src="js/jquery.datetimepicker.full.js"></script>
  <script src="js/dateformat.js"></script>

  <!-- <link href="css/select2.min.css" rel="stylesheet" /> -->
  <link rel="stylesheet" type="text/css" href="css/estilo.css">
  <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

  <!-- <script src="js/select2.min.js"></script> -->
  <script src="js/lib.js"></script>

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

  <!-- DataTables -->
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">

  <!-- DataTables date pluggin -->
  <script src="https://cdn.datatables.net/plug-ins/2.2.2/sorting/date-uk.js"></script>
  <!-- DataTables Button Plugins -->
  <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.dataTables.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.colVis.min.js"></script>
  <link rel=" stylesheet" type="text/css"
    href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.min.css">

  <title>Relatorios</title>
</head>

<body>

  <!-- menu esquerdo -->
  <?php include "menu_esquerdo.php"; ?>

  <!-- conteudo -->
  <div class="corpo" style="overflow: scroll; height: 98%;">

    <div class="container">

      <div class="apar print">

        <h3> Reservas </h3>

        <table id="print" class="lista_comum" cellpadding="4" cellspacing="4">
          <thead>
            <tr>
              <th> </th>
              <th> Sala</th>
              <th> Nome </th>
              <th> Data</th>
              <th> Periodo </th>
              <th> Status </th>
            </tr>
          </thead>
          <tbody>
            <?= $tabela_reservas ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

<script>

  $(document).ready(function () {
    var table = new DataTable('#print', {
      paging: true,
      searching: true,
      ordering: true,
      order: [3, 'desc'],
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      info: true,
      autoWidth: true,
      columnDefs: [
        { targets: [0], visible: false, searchable: false },
        { targets: [1], width: '80px' },
        { targets: [2], width: '300px' },
        { type: 'date-uk', targets: [3], width: '80px' },
        { targets: [4], width: '100px' },
        { targets: [5], width: '80px' },
      ],
      layout: {
        bottomStart: {
          buttons: ['copy', 'csv', 'excel', 'print', 'pdf', 'colvis']
        }
      },
      language: {
        url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/pt-PT.json',
      },
    });
  });

</script>

</html>