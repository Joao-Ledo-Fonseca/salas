<?php
$statusNomes = [1 => 'Reservado', 2 => 'Confirmado', 3 => 'Cancelado'];
?>
<?php if (empty($reservasAssociadas)): ?>
    <p>Nenhuma reserva encontrada.</p>
<?php else: ?>
    <div class="scroll-xy">
        <table class="tabela_comum table-compact" cellpadding="0" cellspacing="0">
            <thead>
                <tr class="border-bottom-999">
                    <th class="p-0-3">ID</th>
                    <?php if ($mostrarUtilizador): ?>
                        <th class="p-0-3">Utilizador</th>
                    <?php endif; ?>
                    <?php if (!$mostrarUtilizador): ?>
                        <th class="p-0-3">Sala</th>
                    <?php endif; ?>
                    <th class="p-0-3">Período</th>
                    <th class="p-0-3">Dia</th>
                    <?php if ($mostrarUtilizador): ?>
                        <th class="p-0-3">Entidade</th>
                    <?php endif; ?>
                    <th class="p-0-3">Uso</th>
                    <th class="p-0-3">Status</th>
                    <th class="p-0-3">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservasAssociadas as $reserva): ?>
                    <tr class="lh-1">
                        <td class="p-0-3"><?= (int) $reserva['id'] ?></td>
                        <?php if ($mostrarUtilizador): ?>
                            <td class="p-0-3"><?= htmlspecialchars((string) $reserva['usuario'], ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endif; ?>
                        <?php if (!$mostrarUtilizador): ?>
                            <td class="p-0-3"><?= htmlspecialchars((string) $reserva['sala'], ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endif; ?>
                        <td class="p-0-3"><?= htmlspecialchars((string) $reserva['periodo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="p-0-3"><?= htmlspecialchars((string) $reserva['dia'], ENT_QUOTES, 'UTF-8') ?></td>
                        <?php if ($mostrarUtilizador): ?>
                            <td class="p-0-3"><?= htmlspecialchars((string) $reserva['entidade_desc'], ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endif; ?>
                        <td class="p-0-3"><?= htmlspecialchars((string) $reserva['uso'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="p-0-3"><?= htmlspecialchars($statusNomes[(int) $reserva['status']] ?? (string) $reserva['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="p-0-3">
                            <?php if ($podeEliminarReserva): ?>
                                <form method="post" action="<?= htmlspecialchars($urlEliminarReserva, ENT_QUOTES, 'UTF-8') ?>" class="inline-form">
                                    <?php foreach ($camposEliminarReserva as $nomeCampo => $valorCampo): ?>
                                        <input type="hidden" name="<?= htmlspecialchars((string) $nomeCampo, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) $valorCampo, ENT_QUOTES, 'UTF-8') ?>" />
                                    <?php endforeach; ?>
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Salas\Core\Support\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
                                    <input type="hidden" name="eliminar_reserva" value="1" />
                                    <input type="hidden" name="reserva_id" value="<?= (int) $reserva['id'] ?>" />
                                    <button type="submit" class="btn1 btn-small">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <hr class="hr-compact" />
    </div>
<?php endif; ?>
