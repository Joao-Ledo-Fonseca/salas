<div class="chart-box" data-chart="<?= htmlspecialchars((string) $chartCard['key'], ENT_QUOTES, 'UTF-8') ?>">
    <h3><?= htmlspecialchars((string) $chartCard['title'], ENT_QUOTES, 'UTF-8') ?></h3>
    <div class="chart-toggle">
        <label><input type="radio" name="<?= htmlspecialchars((string) $chartCard['radioName'], ENT_QUOTES, 'UTF-8') ?>" value="chart" checked> Gráfico</label>
        <label><input type="radio" name="<?= htmlspecialchars((string) $chartCard['radioName'], ENT_QUOTES, 'UTF-8') ?>" value="table"> Tabela</label>
    </div>
    <div class="chart-view">
        <div id="<?= htmlspecialchars((string) $chartCard['containerId'], ENT_QUOTES, 'UTF-8') ?>" style="width: 100%; height: 300px;"></div>
    </div>
    <div class="table-view" style="display:none;">
        <table class="chart-table">
            <thead>
                <tr>
                    <?php foreach ($chartCard['headers'] as $header): ?>
                        <th><?= htmlspecialchars((string) $header, ENT_QUOTES, 'UTF-8') ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($chartCard['rows'])): ?>
                    <tr><td colspan="<?= count($chartCard['headers']) ?>">Sem dados.</td></tr>
                <?php else: ?>
                    <?php foreach ($chartCard['rows'] as $row): ?>
                        <tr>
                            <?php foreach ($row as $cell): ?>
                                <td><?= htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
