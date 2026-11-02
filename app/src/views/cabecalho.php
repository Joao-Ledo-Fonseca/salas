<!-- Cabeçalho comum para Relatórios/Estatísticas (Padrão A)
     Documentação: usado pelas views de relatório (ex: Relatorios.php) e estatística (ex: Estatisticas.php).
     Variáveis esperadas: $acaoPagina (string), $tipo, $mostrarNavEsq (bool|false), $mostrarNavDir (bool|false),
     $titulo, $hoje, $inicio, $fim, $dia_anterior, $dia_posterior, $acaoPagina.
     Uso: <?php include __DIR__ . "/cabecalho.php"; ?>
-->
<div class="container">
    <div class="titulo_inicial">
        <div class="toolbar-row">

            <!-- Chevron Esquerdo: Oculto no Modo Período -->
            <?php if (($tipo ?? '') != 'p'): ?>
                <img src="<?= app_url('/img/chevron_left.png') ?>" alt="Anterior" class="nav-button"
                    onclick="alteraData(dia_anterior)" />
            <?php endif; ?>

            <div class="date-center" style="<?= (($tipo ?? '') == 'p') ? 'margin-left: 0; padding-left: 0;' : '' ?>">
                <form id="relatorios_form" method="get" action="<?= app_url() ?>" target="_self" name="form1"
                    style="display:inline;vertical-align: top;">

                    <input type="hidden" name="acao" value="<?= htmlspecialchars($acaoPagina ?? 'relatorios', ENT_QUOTES, 'UTF-8') ?>" />

                    <input type="hidden" name="tipo" id="tipo_input"
                        value="<?= htmlspecialchars($tipo ?? '', ENT_QUOTES, 'UTF-8') ?>" />

                    <?php if (($tipo ?? '') == 'p'):
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
                            value="<?= htmlspecialchars($hoje_pt ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly="readonly">
                        <input type="hidden" name="data" id="data" value="<?= isset($hoje) ? $hoje->format('dmY') : '' ?>" />
                    <?php endif; ?>
                </form>
            </div>

            <!-- Chevron Direito: Oculto no Modo Período -->
            <?php if (($tipo ?? '') != 'p'): ?>
                <img src="<?= app_url('/img/chevron_right.png') ?>" class="nav-button" alt="Próximo"
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