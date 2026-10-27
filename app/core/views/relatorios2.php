<!DOCTYPE html>
<html lang="pt-PT">

<?php

define('REQUIRED_PERMISSION', 'M_Relatorios');
require_once "seguranca.php";
require_once __DIR__ . "/../app/controllers/dashboardController.php";

$dateFormat = $fm = 'M Y';
$tipo = 'm'; // w - week ; m - month

if (isset($_GET['tipo'])) {   // 
    $tipo = $_GET['tipo'];  //- week ; m - month; p - periodo     
    switch ($tipo) {
        case 'w':
            $dateFormat = $fm = 'W - Y (M)';
            // $tipo = 'w';
            break;
        case 'p':
            $dateFormat = $fm = 'd/m/Y';
            //$tipo = 'p';
            break;
        case 'm':
            $dateFormat = $fm = 'M Y';
            // $tipo = 'm'; // w - week ; m - month
            break;
        }
    }


if ($tipo == 'p') {
    // período: usar data_inicio e data_fim
    if (isset($_GET['data_inicio'])) {
        $inicio = date_create_from_format("dmY", $_GET['data_inicio']);
        }
    else {
        $inicio = new DateTime();
        }
    if (isset($_GET['data_fim'])) {
        $fim = date_create_from_format("dmY", $_GET['data_fim']);
        }
    else {
        $fim = new DateTime();
        }
    $hoje = $inicio; // compatibilidade com código existente
    $hoje_pt = traduz_data($inicio->format('d/m/Y') . ' - ' . $fim->format('d/m/Y'), 'pt');
    }
else {
    if (isset($_GET['data'])) {
        $hoje = date_create_from_format("dmY", $_GET['data']);
        }
    else {
        $hoje = new DateTime();
        }
    // exibir sempre dd/mm/YYYY para consistência
    $hoje_pt = $hoje->format('d/m/Y');
    }

// configurar dias
$dia_anterior = date_create_from_format('dmY', $hoje->format("dmY"));
$dia_anterior->modify(($tipo == 'm' ? '-1 month' : '-1 week'));
// $dia_anterior_pt = traduz_data($dia_anterior->format($dateFormat), 'pt');

$dia_posterior = date_create_from_format('dmY', $hoje->format("dmY"));
$dia_posterior->modify(($tipo == 'm' ? '+1 month' : '+1 week'));
// $dia_posterior_pt = traduz_data($dia_posterior->format($dateFormat), 'pt');



function traduz_data($date, $lang = 'en', &$fm = '')
    {
    $meses_pt = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $meses_en = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    if ($lang == 'en') {
        if (str_contains($fm, 'W ')) {
            //retira a semana, se o formato a tiver
            $date = substr($date, 3);
            $fm = substr($fm, 2);
            }
        return str_replace($meses_pt, $meses_en, $date);
        }
    else {
        return str_replace($meses_en, $meses_pt, $date);
        }

    }

// Prepara a lista para a data escolhida
$dsc = new dashboardController();
$reservas = $dsc->listaReservasController($hoje, $tipo, $tipo === 'p' ? $fim : null);

?>


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- jQuery -->
    <script src="<?= app_url('/js/jquery.js') ?>"></script>

    <!-- DateTimePicker -->
    <script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">

    <script src="<?= app_url('/js/lib.js') ?>"></script>


    <!-- DataTables (compatible versions) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- DataTables date plugin -->
    <script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/date-uk.js"></script>

    <!-- DataTables Buttons (compatible) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">   

    <title>Relatorios</title>

</head>

<body>

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- conteudo -->
    <div class="corpo" style="overflow: scroll; height: 98%;">

        <div>

            <div class="container">
                <div class="titulo_inicial">
                    <div class="toolbar-row">

                        <!-- 🎯 Chevron Esquerdo: Oculto no Modo Período -->
                        <?php if ($tipo != 'p'): ?>
                            <img src="<?= app_url('/img/chevron_left.png') ?>" width="40" height="40" alt="Anterior" class="nav-button"
                                onclick="alteraData(dia_anterior)" />
                        <?php endif; ?>

                        <div class="date-center" style="<?= $tipo == 'p' ? 'margin-left: 0; padding-left: 0;' : '' ?>">
                            <form id="relatorios_form" method="get" action="<?= app_url() ?>" target="_self" name="form1"
                                style="display:inline;vertical-align: top;">

                                <!-- 🎯 Garante que o index.php sabe que deve recarregar a página relatorios2 -->
                                <input type="hidden" name="acao" value="relatorios2" />

                                <input type="hidden" name="tipo" id="tipo_input"
                                    value="<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>" />

                                <?php if ($tipo == 'p'):
                                    $inicio_pt = isset($inicio) ? $inicio->format('d/m/Y') : date('d/m/Y');
                                    $fim_pt = isset($fim) ? $fim->format('d/m/Y') : date('d/m/Y');
                                    ?>
                                    <span class="date-range">
                                        <input type="text" name="data_inicio_pt" id="data_inicio_pt"
                                            class="date-input date-range-input"
                                            value="<?= htmlspecialchars($inicio_pt, ENT_QUOTES, 'UTF-8') ?>"
                                            readonly="readonly">
                                        <input type="text" name="data_fim_pt" id="data_fim_pt"
                                            class="date-input date-range-input"
                                            value="<?= htmlspecialchars($fim_pt, ENT_QUOTES, 'UTF-8') ?>"
                                            readonly="readonly">
                                    </span>
                                    <input type="hidden" name="data_inicio" id="data_inicio"
                                        value="<?= isset($inicio) ? $inicio->format('dmY') : date('dmY') ?>" />
                                    <input type="hidden" name="data_fim" id="data_fim"
                                        value="<?= isset($fim) ? $fim->format('dmY') : date('dmY') ?>" />
                                <?php else: ?>
                                    <input type="text" name="data_pt" id="data_pt" class="date-input"
                                        value="<?= htmlspecialchars($hoje_pt, ENT_QUOTES, 'UTF-8') ?>" readonly="readonly">
                                    <input type="hidden" name="data" id="data" value="<?= $hoje->format("dmY") ?>" />
                                <?php endif; ?>
                            </form>
                        </div>

                        <!-- 🎯 Chevron Direito: Oculto no Modo Período -->
                        <?php if ($tipo != 'p'): ?>
                            <img src="<?= app_url('/img/chevron_right.png') ?>" width="40" height="40" class="nav-button" alt="Próximo"
                                onclick="alteraData(dia_posterior)" />
                        <?php endif; ?>

                    </div>

                    <span style="float:right">
                        <button type="button" id="semanal" class="btn1" onclick="toogletipo('w')">Semanal</button>
                        <button type="button" id="mensal" class="btn1" onclick="toogletipo('m')">Mensal</button>
                        <button type="button" id="periodo" class="btn1" onclick="toogletipo('p')">Período</button>
                    </span>
                </div>
            </div>

            <div id="mensagem"> </div>

            <div class="container">

                <h3> Reservas </h3>

                <table class="lista_comum compact" id="print" cellpadding="4" cellspacing="4">
                    <thead>
                        <tr>
                            <th> Categoria </th>
                            <th> Sala</th>
                            <th> Nome </th>
                            <th> Data</th>
                            <th> Periodo </th>
                            <th> Status </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <?php
                            $diaReserva = date_create_from_format('Y-m-d', $reserva['dia']);
                            $diaFormatado = $diaReserva ? $diaReserva->format('d/m/Y') : '';
                            $dataLink = $diaFormatado !== '' ? rawurlencode($diaFormatado) : '';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $reserva['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $reserva['sala'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td width="300"><?= htmlspecialchars((string) $reserva['disciplina_desc'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><a href="<?= htmlspecialchars(app_url('?data=' . $dataLink), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($diaFormatado, ENT_QUOTES, 'UTF-8') ?></a></td>
                                <td><?= htmlspecialchars((string) $reserva['periodo'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $reserva['status_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (empty($reservas)): ?>
                    <div class="no-data-message">Sem reservas encontradas para o período selecionado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

<?PHP

switch ($tipo) {
    case 'w':
        $titulo = '"Reservas - Semana ' . $hoje->format("W/Y") . '"';
        break;
    case 'm':
        $titulo = '"Reservas - ' . $hoje->format("M Y") . '"';
        break;
    case 'p':
        $titulo = '"Reservas - ' . $inicio->format("d/m/Y") . ' - ' . $fim->format("d/m/Y") . '"';
        break;
    default:
        $titulo = '"Reservas - ' . $hoje->format("d/m/Y") . '"';
    }

?>

<script>
  var appBaseUrl = <?= json_encode(app_url()) ?>;

  // 1. Definição das Variáveis Globais (Passadas do PHP)
  var data = '<?= $hoje->format("dmY"); ?>';
  var dia_anterior = '<?= $dia_anterior->format("dmY"); ?>';
  var dia_posterior = '<?= $dia_posterior->format("dmY"); ?>';
  var format = '<?= $dateFormat; ?>';
  var tipo = '<?= $tipo; ?>';

  jQuery.datetimepicker.setLocale('pt');

  // 2. Funções Globais de Navegação e Cliques
  function atualizaTela(o) {
    var dataVal = $(o).val();
    window.location.href = appBaseUrl + "?acao=relatorios2&data=" + dataVal + "&tipo=" + tipo;
  }

  function alteraData(novaData) {
    window.location.href = appBaseUrl + "?acao=relatorios2&data=" + novaData + "&tipo=" + tipo;
  }

  function toogletipo(tipoBotao) {
    if (tipoBotao === 'p') {
      var url = appBaseUrl + '?acao=relatorios2&tipo=p';
      if ($('#data_inicio').length && $('#data_fim').length) {
        url += '&data_inicio=' + encodeURIComponent($('#data_inicio').val()) + '&data_fim=' + encodeURIComponent($('#data_fim').val());
      }
      window.location.href = url;
    } else {
      window.location.href = appBaseUrl + "?acao=relatorios2&data=" + data + "&tipo=" + tipoBotao;
    }
  }

  // 3. Inicialização Centralizada do DOM
  $(document).ready(function () {
    
    // 🎯 OTIMIZAÇÃO E UNIFORMIZAÇÃO: Gere o estado ativo dos botões
    $('#semanal, #mensal, #periodo').removeClass('active');
    if (tipo === 'w') $('#semanal').addClass('active');
    if (tipo === 'm') $('#mensal').addClass('active');
    if (tipo === 'p') $('#periodo').addClass('active');

    // Inicialização do DateTimePicker para datas únicas (Semanal / Mensal)
    if ($('#data_pt').length) {
      $('#data_pt').datetimepicker({
        timepicker: false,
        format: 'd/m/Y',
        closeOnDateSelect: true,
        appendTo: 'body',
        scrollInput: false,
        onChangeDateTime: function (dp, $input) {
          var value = $input.val();
          var parts = value.split('/');
          if (parts.length === 3) {
            var dmY = parts.join('');
            $('#data').val(dmY);
            window.location.href = appBaseUrl + '?acao=relatorios2&data=' + encodeURIComponent(dmY) + '&tipo=' + tipo;
          }
        }
      });
    }

    // Inicialização dos DateTimePickers para o Modo Período (Início e Fim)
    if ($('#data_inicio_pt').length && $('#data_fim_pt').length) {
      $('#data_inicio_pt').datetimepicker({
        timepicker: false,
        format: 'd/m/Y',
        closeOnDateSelect: true,
        appendTo: 'body',
        scrollInput: false,
        onChangeDateTime: function (dp, $input) {
          var parts = $input.val().split('/');
          if (parts.length === 3) {
            var dmY = parts.join('');
            $('#data_inicio').val(dmY);
            setTimeout(function () {
              $('#relatorios_form').submit();
            }, 200);
          }
        }
      });

      $('#data_fim_pt').datetimepicker({
        timepicker: false,
        format: 'd/m/Y',
        closeOnDateSelect: true,
        appendTo: 'body',
        scrollInput: false,
        onChangeDateTime: function (dp, $input) {
          var parts = $input.val().split('/');
          if (parts.length === 3) {
            var dmY = parts.join('');
            $('#data_fim').val(dmY);
            setTimeout(function () {
              $('#relatorios_form').submit();
            }, 200);
          }
        }
      });
    }
  });
</script>


<script>
    titulo = <?= $titulo ?>;

    $(document).ready(function () {
        var table = $('#print').DataTable({
            searching: true,
            ordering: true,
            order: [[3, 'desc']],
            paging: true,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100, 200],
            autoWidth: false,
            deferRender: true,
            dom: 'Bfrtip',
            columnDefs: [
                { targets: 0, width: '80px' },
                { targets: 1, width: '80px' },
                { targets: 2, width: '100px' },
                { targets: 3, type: 'date-uk', width: '80px' },
                { targets: 4, width: '50px' },
                { targets: 5, width: '50px' }
            ],
            buttons: [
                {
                    extend: 'colvis',
                    text: 'Colunas'
                },
                {
                    extend: 'collection',
                    text: 'Exporta',
                    buttons: [
                        'copy',
                        {
                            extend: 'csv',
                            title: "Reserva de Salas",
                            filename: "*"
                        },
                        {
                            extend: 'excel',
                            title: "Reserva de Salas",
                            sheetName: "Reserva de Salas.xls"
                        },
                        {
                            extend: 'print',
                            title: titulo,
                            header: false
                        },
                        {
                            extend: 'pdf',
                            title: "Reserva de Salas",
                            filename: "*",
                            header: false
                        }
                    ]
                }
            ],
            language: {
                url: '<?= app_url('/datatables/pt-PT.json') ?>'
            }
        });
    });

</script>

</html>
