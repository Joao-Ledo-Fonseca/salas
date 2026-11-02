<?php
/**
 * Componente ListaComum.php
 *
 * Uso: incluir via include e passar:
 *  - $lista (array de arrays/objetos)
 *  - $colunas (array com 'chave', 'label', opcional 'largura')
 *  - $url_editar (opcional, string com pattern '%id%')
 */
?>
<table cellpadding="4" cellspacing="4">
    <thead>
        <tr>
            <?php foreach ($colunas as $coluna): ?>
                <th <?= isset($coluna['largura']) ? 'width="' . htmlspecialchars((string)$coluna['largura'], ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
                    <?= htmlspecialchars((string)($coluna['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                </th>
            <?php endforeach; ?>
            <?php if (isset($url_editar)): ?>
                <th></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lista as $item): ?>
            <tr>
                <?php foreach ($colunas as $coluna): ?>
                    <?php $chave = $coluna['chave'] ?? null; ?>
                    <td>
                        <?php if ($chave === null): ?>
                            <?= htmlspecialchars((string)(is_array($item) ? ($item['nome'] ?? $item['id'] ?? '') : $item), ENT_QUOTES, 'UTF-8') ?>
                        <?php elseif (isset($url_editar)): ?>
                            <?php if ($coluna === $colunas[0]): ?>
                                <a href="<?= htmlspecialchars(str_replace('%id%', (string)($item['id'] ?? 0), $url_editar), ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars((string)($item[$chave] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php else: ?>
                                <?= htmlspecialchars((string)($item[$chave] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <?= htmlspecialchars((string)($item[$chave] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
                <?php if (isset($url_editar)): ?>
                    <td></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
