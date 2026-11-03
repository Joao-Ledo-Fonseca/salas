<!-- cabecalho2.php - Para listas e forms de entidades (ex: Sala, Usuario, Categoria, Periodo, Uso) -->
<div class="cabecalho2-container container">
    <div class="cabecalho2-linha">
        <!-- Título da página -->
        <h3 class="cabecalho2-titulo">
            <?= htmlspecialchars($titulo ?? 'Lista', ENT_QUOTES, 'UTF-8') ?>
        </h3>

        <!-- Área de ações (botão Novo, filtros, etc.) -->
        <div class="cabecalho2-acoes">
            <!-- Views colocam aqui: botão "Novo", seletores, radiobuttons, etc. -->
        </div>
    </div>
</div>