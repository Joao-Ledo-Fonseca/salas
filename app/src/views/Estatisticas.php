<?php
use Salas\Infrastructure\Support\Csrf;
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
use Salas\Infrastructure\Support\Util;
use Salas\Infrastructure\Validation\Validator;
use Salas\Application\Services\AuthorizationService;
?>

<!DOCTYPE html>
<html lang="pt-PT">


<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>Relatórios</title>

  <script src="<?= app_url('/js/jquery.js') ?>"></script>
  <script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>
  <script src="<?= app_url('/js/dateformat.js') ?>"></script>

  <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
  <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">
  <script src="<?= app_url('/js/lib.js') ?>"></script>


</head>

<body>

  <!-- menu esquerdo -->
  <?php include __DIR__ . "/MenuEsquerdo.php"; ?>

  <div class="corpo">

    <!-- CABEÇALHO UNIFICADO COM FORMATO CONTAINER (Chevrons Ocultos no Modo Período) -->
    <div class="container">
      <div class="titulo_inicial">
        <div class="toolbar-row">

          <!-- Chevron Esquerdo: Só aparece se NÃO for período -->
          <?php if ($tipo != 'p'): ?>
            <img src="<?= app_url('/img/chevron_left.png') ?>" width="40" height="40" alt="Anterior" class="nav-button" onclick="alteraData(dia_anterior)" />
          <?php endif; ?>

          <div class="date-center" style="<?= $tipo == 'p' ? 'margin-left: 0; padding-left: 0;' : '' ?>">
            <form id="relatorios_form" method="get" action="<?= app_url() ?>" target="_self" name="form1" style="display:inline;vertical-align: top;">
              <input type="hidden" name="acao" value="estatisticas" />
              <input type="hidden" name="tipo" id="tipo_input" value="<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>" />

              <?php if ($tipo == 'p'):
                $inicio_pt = isset($inicio) ? $inicio->format('d/m/Y') : date('d/m/Y');
                $fim_pt = isset($fim) ? $fim->format('d/m/Y') : date('d/m/Y');
                ?>
                <span class="date-range">
                  <input type="text" name="data_inicio_pt" id="data_inicio_pt" class="date-input date-range-input" value="<?= htmlspecialchars($inicio_pt, ENT_QUOTES, 'UTF-8') ?>" readonly="readonly">
                  <input type="text" name="data_fim_pt" id="data_fim_pt" class="date-input date-range-input" value="<?= htmlspecialchars($fim_pt, ENT_QUOTES, 'UTF-8') ?>" readonly="readonly">
                </span>
                <input type="hidden" name="data_inicio" id="data_inicio" value="<?= isset($inicio) ? $inicio->format('dmY') : date('dmY') ?>" />
                <input type="hidden" name="data_fim" id="data_fim" value="<?= isset($fim) ? $fim->format('dmY') : date('dmY') ?>" />
              <?php else: ?>
                <input type="text" name="data_pt" id="data_pt" class="date-input" value="<?= htmlspecialchars($hoje_pt, ENT_QUOTES, 'UTF-8') ?>" readonly="readonly">
                <input type="hidden" name="data" id="data" value="<?= $hoje->format("dmY") ?>" />
              <?php endif; ?>
            </form>
          </div>

          <!-- Chevron Direito: Só aparece se NÃO for período -->
          <?php if ($tipo != 'p'): ?>
            <img src="<?= app_url('/img/chevron_right.png') ?>" width="40" height="40" class="nav-button" alt="Próximo" onclick="alteraData(dia_posterior)" />
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

    <!-- ESTRUTURA UNIFICADA: Apenas uma zona de scroll e um container -->
    <div class="zona-scroll">
      <div class="container">

        <!-- 1. Bloco de Cima: Listagem por Disciplina -->
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

        <!-- 2. Bloco de Baixo: Gráfico + Box de Informação Permanente (Isolado por Estilos Fixos) -->
        <div class="apar" style="margin-top: 30px; text-align: left;">
          <h3> Taxa de ocupação das salas </h3>
          
          <!-- Usamos uma tabela pura sem classes para o CSS global não interferir nas colunas -->
          <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 900px; border: none; background: transparent; border-collapse: collapse;">
            <tr>
              <!-- Coluna Esquerda: Trancada milimetricamente para o Gráfico de 500px respirar -->
              <td style="width: 520px; min-width: 520px; max-width: 520px; border: none; background: transparent; text-align: left; padding: 0; vertical-align: top;">
                <div id="chart_div" style="width: 500px; height: 400px; overflow: hidden;"></div>
              </td>
              
<!-- Coluna Direita: Alinha a Box verticalmente ao CENTRO do gráfico -->
              <td style="border: none; background: transparent; padding: 0; vertical-align: middle; padding-left: 20px;">
                <div class="info-box" style="width: 100%; max-width: 350px; padding: 15px; border: 1px solid #ccc; border-radius: 4px; background-color: #f5f5f5; font-family: inherit; font-size: 13px; color: #333; box-sizing: border-box; text-align: left; margin: 0;">
                  <strong style="display: block; margin-bottom: 8px; font-size: 14px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Informação do Período:</strong>
                  <ul style="margin: 0; padding-left: 20px; line-height: 22px; list-style-type: square;">
                    <li><strong>Filtro Ativo:</strong> <?= htmlspecialchars($tipo === 'w' ? 'Semanal' : ($tipo === 'p' ? 'Período' : 'Mensal'), ENT_QUOTES, 'UTF-8') ?></li>
                    <li><strong>Data/Intervalo:</strong> <?= htmlspecialchars($hoje_pt, ENT_QUOTES, 'UTF-8') ?></li>
                    <li><strong>Total de Reservas:</strong> <?= (int)$total_reservas ?></li>
                    <li><strong>Total de Horários Disponíveis:</strong> <?= (int)$total_horarios ?></li>
                  </ul>
                </div>
              </td>
            </tr>
          </table>
        </div>

      </div> <!-- /container -->
    </div> <!-- /zona-scroll -->

    <!-- Biblioteca oficial e estável do Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {
        'packages': ['corechart'],
        'callback': drawChart
      });

      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
          ['Ocupado', <?= (int) $total_reservas ?>],
          ['Desocupado', <?= (int) $total_horarios ?>]
        ]);

        var options = {
          'title': 'Taxa de ocupação da sala',
          'width': 500,
          'height': 400
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </div>

</body>


<script>
  var appBaseUrl = <?= json_encode(app_url()) ?>;

  // Variáveis PHP passadas para o JavaScript
  var data = '<?= $hoje->format("dmY"); ?>';
  var dia_anterior = '<?= $dia_anterior->format("dmY"); ?>';
  var dia_posterior = '<?= $dia_posterior->format("dmY"); ?>';
  var tipo = '<?= $tipo; ?>';

  jQuery.datetimepicker.setLocale('pt');

  // 🎯 CORREÇÃO: Como os Chevrons estão ocultos no período, a função apenas muda a data base semanal/mensal
  function alteraData(novaData) {
    window.location.href = appBaseUrl + "?acao=estatisticas&data=" + novaData + "&tipo=" + tipo;
  }

  // Função que alterna os botões Semanal/Mensal/Período
  function toogletipo(tipoBotao) {
    if (tipoBotao === 'p') {
      var url = appBaseUrl + '?acao=estatisticas&tipo=p';
      if ($('#data_inicio').length && $('#data_fim').length) {
        url += '&data_inicio=' + encodeURIComponent($('#data_inicio').val()) + '&data_fim=' + encodeURIComponent($('#data_fim').val());
      }
      window.location.href = url;
    } else {
      window.location.href = appBaseUrl + "?acao=estatisticas&data=" + data + "&tipo=" + tipoBotao;
    }
  }

  // 🎯 CORREÇÃO: Abre o bloco ready que estava em falta para que o código execute no momento certo
  $(document).ready(function () {

    // 🎯 OTIMIZAÇÃO: Limpa o estado ativo de todos e adiciona apenas ao filtro selecionado
    $('#semanal, #mensal, #periodo').removeClass('active');

    if (tipo === 'w') $('#semanal').addClass('active');
    if (tipo === 'm') $('#mensal').addClass('active');
    if (tipo === 'p') $('#periodo').addClass('active');

    // 🎯 INJEÇÃO DE MENSAGEM: Alerta o utilizador se não existirem reservas no período selecionado
    var totalReservas = <?= (int) $total_reservas; ?>;

    // Inicialização do DateTimePicker para datas únicas (Semanal / Mensal)
    if ($('#data_pt').length) {
      $('#data_pt').datetimepicker({
        timepicker: false,
        format: 'd/m/Y',
        closeOnDateSelect: true,
        appendTo: 'body',
        scrollInput: false,
        onChange\DateTime: function (dp, $input) {
          var value = $input.val();
          var parts = value.split('/');
          if (parts.length === 3) {
            var dmY = parts.join('');
            $('#data').val(dmY);
            window.location.href = appBaseUrl + '?acao=estatisticas&data=' + encodeURIComponent(dmY) + '&tipo=' + tipo;
          }
        }
      });
    }

    // Configuração para o DateTimePicker do modo período (Início)
    if ($('#data_inicio_pt').length) {
      $('#data_inicio_pt').datetimepicker({
        timepicker: false,
        format: 'd/m/Y',
        closeOnDateSelect: true,
        appendTo: 'body',
        scrollInput: false,
        onChange\DateTime: function (dp, $input) {
          var parts = $input.val().split('/');
          if (parts.length === 3) {
            var dmY = parts.join('');
            $('#data_inicio').val(dmY);
            var url = appBaseUrl + '?acao=estatisticas&tipo=p&data_inicio=' + dmY;
            if ($('#data_fim').val()) url += '&data_fim=' + $('#data_fim').val();
            window.location.href = url;
          }
        }
      });
    }

    // Configuração para o DateTimePicker do modo período (Fim)
    if ($('#data_fim_pt').length) {
      $('#data_fim_pt').datetimepicker({
        timepicker: false,
        format: 'd/m/Y',
        closeOnDateSelect: true,
        appendTo: 'body',
        scrollInput: false,
        onChange\DateTime: function (dp, $input) {
          var parts = $input.val().split('/');
          if (parts.length === 3) {
            var dmY = parts.join('');
            $('#data_fim').val(dmY);
            var url = appBaseUrl + '?acao=estatisticas&tipo=p';
            if ($('#data_inicio').val()) url += '&data_inicio=' + $('#data_inicio').val();
            url += '&data_fim=' + dmY;
            window.location.href = url;
          }
        }
      });
    }
  });
</script>

</html>
