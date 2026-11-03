<?php
/**
 * Componente ListaComum.php
 *
 * Uso: incluir via include e passar:
 *  - $lista (array de linhas)
 *  - $colunas (array com 'chave', 'label', opcionais 'largura', 'formato' e 'url')
 *  - $url_editar (opcional, URL padrão para a primeira coluna, com pattern '%id%')
 *  - $classe_tabela (opcional)
 */
?>
<?php
$temLinks = isset($url_editar);
if (!$temLinks) {
    foreach ($colunas as $coluna) {
        if (isset($coluna['url'])) {
            $temLinks = true;
            break;
        }
    }
}
?>
<table<?= !empty($classe_tabela) ? ' class="' . htmlspecialchars((string) $classe_tabela, ENT_QUOTES, 'UTF-8') . '"' : '' ?> cellpadding="4" cellspacing="4">
    <thead>
        <tr>
            <?php foreach ($colunas as $coluna): ?>
                <th <?= isset($coluna['largura']) ? 'width="' . htmlspecialchars((string)$coluna['largura'], ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
                    <?= htmlspecialchars((string)($coluna['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                </th>
            <?php endforeach; ?>
            <?php if ($temLinks): ?>
                <th></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lista as $item): ?>
            <tr>
                <?php foreach ($colunas as $coluna): ?>
                    <?php $chave = $coluna['chave'] ?? null; ?>
                    <?php
                    $valor = $chave === null
                        ? (is_array($item) ? ($item['nome'] ?? $item['id'] ?? '') : $item)
                        : ($item[$chave] ?? '');
                    if (($coluna['formato'] ?? '') === 'int') {
                        $valor = (int) $valor;
                    }
                    $url = $coluna['url'] ?? (
                        isset($url_editar) && $coluna === $colunas[0]
                            ? $url_editar
                            : null
                    );
                    $valorEscapado = htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
                    ?>
                    <td>
                        <?php if ($url !== null): ?>
                            <a href="<?= htmlspecialchars(str_replace('%id%', (string) (int) ($item['id'] ?? 0), (string) $url), ENT_QUOTES, 'UTF-8') ?>"><?= $valorEscapado ?></a>
                        <?php else: ?>
                            <?= $valorEscapado ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
                <?php if ($temLinks): ?>
                    <td></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
