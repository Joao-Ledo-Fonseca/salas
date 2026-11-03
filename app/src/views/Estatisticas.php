<?php
$title = 'Estatísticas';
$bodyStyle = 'overflow: hidden;';
?>

  <?php
  // Esta view usa o cabeçalho padrão A para estatísticas (cabecalho.php).
  $acaoPagina = 'estatisticas';
  include __DIR__ . "/components/cabecalho.php";
  ?>

    <div id="mensagem"> </div>

    <!-- ESTRUTURA UNIFICADA: Apenas uma zona de scroll e um container -->
    <div class="zona-scroll">
      <div class="container">

        <div class="charts-grid" style="margin-top: 30px;">
          <?php
          $cartoesGrafico = [
              [
                  'key' => 'ocupacao',
                  'title' => 'Taxa de ocupação das salas',
                  'radioName' => 'view_ocupacao',
                  'containerId' => 'chart_ocupacao',
                  'headers' => ['Estado', 'Total'],
                  'rows' => [
                      ['Ocupado', (int) $total_reservas],
                      ['Desocupado', (int) $total_horarios],
                  ],
              ],
              [
                  'key' => 'uso',
                  'title' => 'Reservas por uso',
                  'radioName' => 'view_uso',
                  'containerId' => 'chart_uso',
                  'headers' => ['Uso', 'Total'],
                  'rows' => array_map(
                      static fn (array $row): array => [(string) ($row['uso'] ?? 'Sem uso'), (int) ($row['total'] ?? 0)],
                      $resumo_reservas ?? []
                  ),
              ],
              [
                  'key' => 'data',
                  'title' => 'Reservas por data',
                  'radioName' => 'view_data',
                  'containerId' => 'chart_data',
                  'headers' => ['Data', 'Total'],
                  'rows' => array_map(
                      static fn (array $row): array => [(string) ($row['data'] ?? ''), (int) ($row['total'] ?? 0)],
                      $reservas_por_data ?? []
                  ),
              ],
              [
                  'key' => 'sala',
                  'title' => 'Reservas por sala',
                  'radioName' => 'view_sala',
                  'containerId' => 'chart_sala',
                  'headers' => ['Sala', 'Total'],
                  'rows' => array_map(
                      static fn (array $row): array => [(string) ($row['sala'] ?? 'Sem sala'), (int) ($row['total'] ?? 0)],
                      $reservas_por_sala ?? []
                  ),
              ],
          ];
          foreach ($cartoesGrafico as $chartCard):
              include __DIR__ . '/components/CartaoGrafico.php';
          endforeach;
          ?>
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
