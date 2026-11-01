<?php
use Salas\Core\Support\Csrf;
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
use Salas\Core\Validation\Validator;
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

   <script>window.__APP_THEME__ = <?= json_encode((isset($cfg) && isset($cfg->theme) ? $cfg->theme->key : 'light'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
   <script>window.__APP_THEMES__ = <?= json_encode((isset($cfg) && isset($cfg->availableThemes) ? $cfg->availableThemes : ['light', 'dark']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
   <script src="<?= app_url('/js/theme.js') ?>"></script>

   <?php if (!empty($cfg) && isset($cfg->theme)): ?>
   <style>
     :root {
       <?= $cfg->theme->toCssVariables() ?>
     }
     html { font-size: var(--font-size-base, 14px); }
   </style>
   <?php endif; ?>

   <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
  <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">
  <script src="<?= app_url('/js/lib.js') ?>"></script>


</head>

<body style="overflow: hidden;">

  <!-- menu esquerdo -->
  <?php include __DIR__ . "/MenuEsquerdo.php"; ?>

  <div class="corpo">

    <!-- CABEÇALHO UNIFICADO COM FORMATO CONTAINER (Chevrons Ocultos no Modo Período) -->
    <div class="container">
      <div class="titulo_inicial">
        <div class="toolbar-row">

          <!-- Chevron Esquerdo: Só aparece se NÃO for período -->
          <?php if ($tipo != 'p'): ?>
            <img src="<?= app_url('/img/chevron_left.png') ?>" alt="Anterior" class="nav-button" onclick="alteraData(dia_anterior)" />
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
            <img src="<?= app_url('/img/chevron_right.png') ?>" class="nav-button" alt="Próximo" onclick="alteraData(dia_posterior)" />
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

        <!-- Gráficos e tabela dinâmica -->

        <!-- 2. Grelha de gráficos 2x2 -->
        <div class="charts-grid" style="margin-top: 30px;">
          <div class="chart-box" data-chart="ocupacao">
            <h3>Taxa de ocupação das salas</h3>
            <div class="chart-toggle">
              <label><input type="radio" name="view_ocupacao" value="chart" checked> Gráfico</label>
              <label><input type="radio" name="view_ocupacao" value="table"> Tabela</label>
            </div>
            <div class="chart-view">
              <div id="chart_ocupacao" style="width: 100%; height: 300px;"></div>
            </div>
            <div class="table-view" style="display:none;">
              <table class="chart-table">
                <thead><tr><th>Estado</th><th>Total</th></tr></thead>
                <tbody>
                  <tr><td>Ocupado</td><td><?= (int) $total_reservas ?></td></tr>
                  <tr><td>Desocupado</td><td><?= (int) $total_horarios ?></td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="chart-box" data-chart="uso">
            <h3>Reservas por uso</h3>
            <div class="chart-toggle">
              <label><input type="radio" name="view_uso" value="chart" checked> Gráfico</label>
              <label><input type="radio" name="view_uso" value="table"> Tabela</label>
            </div>
            <div class="chart-view">
              <div id="chart_uso" style="width: 100%; height: 300px;"></div>
            </div>
            <div class="table-view" style="display:none;">
              <table class="chart-table">
                <thead><tr><th>Uso</th><th>Total</th></tr></thead>
                <tbody>
                  <?php if (empty($resumo_reservas)): ?>
                    <tr><td colspan="2">Sem dados.</td></tr>
                  <?php else: ?>
                    <?php foreach ($resumo_reservas as $r): ?>
                      <tr><td><?= htmlspecialchars((string)($r['uso'] ?? 'Sem uso'), ENT_QUOTES, 'UTF-8') ?></td><td><?= (int)($r['total'] ?? 0) ?></td></tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="chart-box" data-chart="data">
            <h3>Reservas por data</h3>
            <div class="chart-toggle">
              <label><input type="radio" name="view_data" value="chart" checked> Gráfico</label>
              <label><input type="radio" name="view_data" value="table"> Tabela</label>
            </div>
            <div class="chart-view">
              <div id="chart_data" style="width: 100%; height: 300px;"></div>
            </div>
            <div class="table-view" style="display:none;">
              <table class="chart-table">
                <thead><tr><th>Data</th><th>Total</th></tr></thead>
                <tbody>
                  <?php if (empty($reservas_por_data)): ?>
                    <tr><td colspan="2">Sem dados.</td></tr>
                  <?php else: ?>
                    <?php foreach ($reservas_por_data as $r): ?>
                      <tr><td><?= htmlspecialchars((string)($r['data'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td><td><?= (int)($r['total'] ?? 0) ?></td></tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="chart-box" data-chart="sala">
            <h3>Reservas por sala</h3>
            <div class="chart-toggle">
              <label><input type="radio" name="view_sala" value="chart" checked> Gráfico</label>
              <label><input type="radio" name="view_sala" value="table"> Tabela</label>
            </div>
            <div class="chart-view">
              <div id="chart_sala" style="width: 100%; height: 300px;"></div>
            </div>
            <div class="table-view" style="display:none;">
              <table class="chart-table">
                <thead><tr><th>Sala</th><th>Total</th></tr></thead>
                <tbody>
                  <?php if (empty($reservas_por_sala)): ?>
                    <tr><td colspan="2">Sem dados.</td></tr>
                  <?php else: ?>
                    <?php foreach ($reservas_por_sala as $r): ?>
                      <tr><td><?= htmlspecialchars((string)($r['sala'] ?? 'Sem sala'), ENT_QUOTES, 'UTF-8') ?></td><td><?= (int)($r['total'] ?? 0) ?></td></tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- 3. Tabela dinâmica -->
        <div class="apar" style="margin-top: 30px; text-align: left;">
          <h3 id="tabela_titulo">Dados do gráfico selecionado</h3>
          <table id="tabela_estatisticas" class="lista_comum" cellpadding="4" cellspacing="4" style="width:100%;">
            <thead></thead>
            <tbody></tbody>
          </table>
        </div>

      </div> <!-- /container -->
    </div> <!-- /zona-scroll -->

    <!-- Biblioteca oficial e estável do Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {
        'packages': ['corechart'],
        'callback': drawAll
      });

      var chartOcupacao, chartUso, chartData, chartSala;

      function drawAll() {
        drawOcupacao();
        drawUso();
        drawData();
        drawSala();
        showTable('ocupacao');
      }

      function drawOcupacao() {
        var container = document.getElementById('chart_ocupacao');
        if (!container) return;
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Estado');
        data.addColumn('number', 'Total');
        data.addRows([
          ['Ocupado', <?= (int) $total_reservas ?>],
          ['Desocupado', <?= (int) $total_horarios ?>]
        ]);
        var options = { 'title': 'Taxa de ocupação da sala', 'height': 300, 'backgroundColor': 'transparent', 'legend': { 'position': 'bottom' } };
        chartOcupacao = new google.visualization.PieChart(container);
        google.visualization.events.addListener(chartOcupacao, 'select', function () { showTable('ocupacao'); });
        chartOcupacao.draw(data, options);
        container.addEventListener('click', function () { showTable('ocupacao'); });
      }

      function drawUso() {
        var container = document.getElementById('chart_uso');
        if (!container) return;
        if (!reservasPorUso || reservasPorUso.length === 0) { container.innerHTML = 'Sem dados.'; return; }
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Uso');
        data.addColumn('number', 'Reservas');
        var rows = reservasPorUso.map(function (r) { return [String(r.uso || 'Sem uso'), Number(r.total) || 0]; });
        data.addRows(rows);
        var options = { 'title': 'Reservas por uso', 'height': 300, 'backgroundColor': 'transparent', 'legend': { 'position': 'none' }, 'hAxis': { 'title': 'Uso' }, 'vAxis': { 'title': 'Reservas', 'minValue': 0 } };
        chartUso = new google.visualization.ColumnChart(container);
        google.visualization.events.addListener(chartUso, 'select', function () { showTable('uso'); });
        chartUso.draw(data, options);
        container.addEventListener('click', function () { showTable('uso'); });
      }

      function drawData() {
        var container = document.getElementById('chart_data');
        if (!container) return;
        if (!reservasPorData || reservasPorData.length === 0) { container.innerHTML = 'Sem dados.'; return; }
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Data');
        data.addColumn('number', 'Reservas');
        var rows = reservasPorData.map(function (r) { return [String(r.data || ''), Number(r.total) || 0]; });
        data.addRows(rows);
        var options = { 'title': 'Reservas por data', 'height': 300, 'backgroundColor': 'transparent', 'legend': { 'position': 'none' }, 'hAxis': { 'title': 'Data' }, 'vAxis': { 'title': 'Reservas', 'minValue': 0 } };
        chartData = new google.visualization.ColumnChart(container);
        google.visualization.events.addListener(chartData, 'select', function () { showTable('data'); });
        chartData.draw(data, options);
        container.addEventListener('click', function () { showTable('data'); });
      }

      function drawSala() {
        var container = document.getElementById('chart_sala');
        if (!container) return;
        if (!reservasPorSala || reservasPorSala.length === 0) { container.innerHTML = 'Sem dados.'; return; }
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Sala');
        data.addColumn('number', 'Reservas');
        var rows = reservasPorSala.map(function (r) { return [String(r.sala || 'Sem sala'), Number(r.total) || 0]; });
        data.addRows(rows);
        var options = { 'title': 'Reservas por sala', 'height': 300, 'backgroundColor': 'transparent', 'legend': { 'position': 'none' }, 'hAxis': { 'title': 'Sala' }, 'vAxis': { 'title': 'Reservas', 'minValue': 0 } };
        chartSala = new google.visualization.ColumnChart(container);
        google.visualization.events.addListener(chartSala, 'select', function () { showTable('sala'); });
        chartSala.draw(data, options);
        container.addEventListener('click', function () { showTable('sala'); });
      }

      function showTable(key) {
        var title = 'Dados do gráfico selecionado';
        var headers = [];
        var rows = [];
        if (key === 'ocupacao') {
          title = 'Taxa de ocupação';
          headers = ['Estado', 'Total'];
          rows = [['Ocupado', <?= (int) $total_reservas ?>], ['Desocupado', <?= (int) $total_horarios ?>]];
        } else if (key === 'uso') {
          title = 'Reservas por uso';
          headers = ['Uso', 'Total'];
          rows = (reservasPorUso || []).map(function (r) { return [String(r.uso || 'Sem uso'), Number(r.total) || 0]; });
        } else if (key === 'data') {
          title = 'Reservas por data';
          headers = ['Data', 'Total'];
          rows = (reservasPorData || []).map(function (r) { return [String(r.data || ''), Number(r.total) || 0]; });
        } else if (key === 'sala') {
          title = 'Reservas por sala';
          headers = ['Sala', 'Total'];
          rows = (reservasPorSala || []).map(function (r) { return [String(r.sala || 'Sem sala'), Number(r.total) || 0]; });
        }
        document.getElementById('tabela_titulo').textContent = title;
        document.querySelectorAll('.chart-box').forEach(function (box) { box.classList.remove('selected'); });
        var selectedBox = document.querySelector('.chart-box[data-chart="' + key + '"]');
        if (selectedBox) selectedBox.classList.add('selected');
        var thead = document.querySelector('#tabela_estatisticas thead');
        var tbody = document.querySelector('#tabela_estatisticas tbody');
        thead.innerHTML = '<tr>' + headers.map(function (h) { return '<th>' + h + '</th>'; }).join('') + '</tr>';
        tbody.innerHTML = rows.map(function (r) { return '<tr>' + r.map(function (c) { return '<td>' + c + '</td>'; }).join('') + '</tr>'; }).join('');
      }

      document.querySelectorAll('.chart-box').forEach(function (box) {
        box.addEventListener('click', function (e) {
          if (e.target.closest('.chart-toggle')) return;
          if (box.dataset.chart) showTable(box.dataset.chart);
        });
      });
      document.querySelectorAll('.chart-toggle input[type="radio"]').forEach(function (r) {
        r.addEventListener('change', function () {
          var box = r.closest('.chart-box');
          var showTable = r.value === 'table';
          box.querySelector('.chart-view').style.display = showTable ? 'none' : 'block';
          box.querySelector('.table-view').style.display = showTable ? 'block' : 'none';
        });
      });
    </script>
  </div>

</body>


<script>
  var appBaseUrl = <?= json_encode(app_url()) ?>;
  var reservasPorUso = <?= json_encode(isset($resumo_reservas) ? $resumo_reservas : [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var reservasPorData = <?= json_encode(isset($reservas_por_data) ? $reservas_por_data : [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var reservasPorSala = <?= json_encode(isset($reservas_por_sala) ? $reservas_por_sala : [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

  // Variáveis PHP passadas para o JavaScript
  var data = '<?= $hoje->format("dmY"); ?>';
  var dia_anterior = '<?= $dia_anterior->format("dmY"); ?>';
  var dia_posterior = '<?= $dia_posterior->format("dmY"); ?>';
  var tipo = '<?= $tipo; ?>';

  jQuery.datetimepicker.setLocale('pt');

  //  CORREÇÃO: Como os Chevrons estão ocultos no período, a função apenas muda a data base semanal/mensal
  /**
   * Método alteraData.
   * @param novaDat $a
   * @return mixed
   */
  function alteraData(novaData) {
    window.location.href = appBaseUrl + "?acao=estatisticas&data=" + novaData + "&tipo=" + tipo;
  }

  // Função que alterna os botões Semanal/Mensal/Período
  /**
   * Método toogletipo.
   * @param tipoBota $o
   * @return mixed
   */
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

  //  CORREÇÃO: Abre o bloco ready que estava em falta para que o código execute no momento certo
  $(document).ready(function () {

    //  OTIMIZAÇÃO: Limpa o estado ativo de todos e adiciona apenas ao filtro selecionado
    $('#semanal, #mensal, #periodo').removeClass('active');

    if (tipo === 'w') $('#semanal').addClass('active');
    if (tipo === 'm') $('#mensal').addClass('active');
    if (tipo === 'p') $('#periodo').addClass('active');

    //  INJEÇÃO DE MENSAGEM: Alerta o utilizador se não existirem reservas no período selecionado
    var totalReservas = <?= (int) $total_reservas; ?>;

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
        onChangeDateTime: function (dp, $input) {
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
        onChangeDateTime: function (dp, $input) {
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
