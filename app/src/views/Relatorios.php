<?php
$title = 'Relatórios';
$bodyStyle = 'overflow: hidden;';
$contentStyle = 'overflow: scroll; height: 98%;';
$extraHead = implode("\n", [
    '<style>html { font-size: var(--font-size-base, 14px); }</style>',
    '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">',
    '<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">',
    '<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>',
    '<script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/date-uk.js"></script>',
    '<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>',
    '<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.dataTables.min.js"></script>',
    '<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>',
    '<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>',
    '<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>',
    '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>',
    '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>',
    '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>',
]);
?>

            <?php
            // Esta view usa o cabeçalho padrão A para relatórios (cabecalho.php)
            $acaoPagina = 'relatorios';
            include __DIR__ . "/components/cabecalho.php";
            ?>

            <div id="mensagem"> </div>

            <div class="container">

                <h3> Reservas </h3>

                <table class="lista_comum compact" id="print" cellpadding="4" cellspacing="4">
                    <thead>
                        <tr>
                            <th> Categoria </th>
                            <th> Sala</th>
                            <th> Nome </th>
                            <th> Reservado por </th>
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
                                <td width="300"><?= htmlspecialchars((string) $reserva['entidade_desc'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($reserva['reservado_por'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
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
  /**
   * Método atualizaTela.
   * @param mixed $o
   * @return mixed
   */
  function atualizaTela(o) {
    var dataVal = $(o).val();
    window.location.href = appBaseUrl + "?acao=relatorios&data=" + dataVal + "&tipo=" + tipo;
  }

  /**
   * Método alteraData.
   * @param novaDat $a
   * @return mixed
   */
  function alteraData(novaData) {
    window.location.href = appBaseUrl + "?acao=relatorios&data=" + novaData + "&tipo=" + tipo;
  }

  /**
   * Método toogletipo.
   * @param tipoBota $o
   * @return mixed
   */
  function toogletipo(tipoBotao) {
    if (tipoBotao === 'p') {
      var url = appBaseUrl + '?acao=relatorios&tipo=p';
      if ($('#data_inicio').length && $('#data_fim').length) {
        url += '&data_inicio=' + encodeURIComponent($('#data_inicio').val()) + '&data_fim=' + encodeURIComponent($('#data_fim').val());
      }
      window.location.href = url;
    } else {
      window.location.href = appBaseUrl + "?acao=relatorios&data=" + data + "&tipo=" + tipoBotao;
    }
  }

  // 3. Inicialização Centralizada do DOM
  $(document).ready(function () {
    
    //  OTIMIZAÇÃO E UNIFORMIZAÇÃO: Gere o estado ativo dos botões
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
            window.location.href = appBaseUrl + '?acao=relatorios&data=' + encodeURIComponent(dmY) + '&tipo=' + tipo;
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
            order: [[4, 'desc']],
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
                { targets: 3, width: '100px' },
                { targets: 4, type: 'date-uk', width: '80px' },
                { targets: 5, width: '50px' },
                { targets: 6, width: '50px' }
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
