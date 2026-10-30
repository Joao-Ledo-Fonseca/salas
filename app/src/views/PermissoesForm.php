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

 <?php $title = 'Permissões'; ?>

<h3>Permissões</h3>

<form name="form1" method="post" action="<?= app_url('?acao=permissoes_form') ?>">
    <div class="container_conteudo" class="scroll-y-perm">

        <div class="lista_comum">
            <table cellpadding="4" cellspacing="4">
                <thead>
                    <tr>
                        <th class="d-none"></th>
                        <th class="w-200">Nome</th>
                        <th class="w-60"><?= htmlspecialchars($nivelLabels[0], ENT_QUOTES, 'UTF-8') ?></th>
                        <th class="w-60"><?= htmlspecialchars($nivelLabels[1], ENT_QUOTES, 'UTF-8') ?></th>
                        <th class="w-60"><?= htmlspecialchars($nivelLabels[2], ENT_QUOTES, 'UTF-8') ?></th>
                        <th class="w-100"><?= htmlspecialchars($nivelLabels[3], ENT_QUOTES, 'UTF-8') ?></th>
                        <th></th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tabela_n as $permissao): ?>
                        <?php $nomePermissao = (string) $permissao['nome']; ?>
                        <tr class="show">
                            <td class="d-none"></td>
                            <td><?= htmlspecialchars($nomePermissao, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><input type="checkbox" onclick="show_salvar()" name="<?= htmlspecialchars($nomePermissao . '0', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '2', ENT_QUOTES, 'UTF-8') ?>" value="0" <?= !empty($permissao['uexterno']) ? 'checked' : '' ?>></td>
                            <td><input type="checkbox" onclick="show_salvar()" name="<?= htmlspecialchars($nomePermissao . '1', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '3', ENT_QUOTES, 'UTF-8') ?>" value="1" <?= !empty($permissao['uinterno']) ? 'checked' : '' ?>></td>
                            <td><input type="checkbox" onclick="show_salvar()" name="<?= htmlspecialchars($nomePermissao . '2', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '4', ENT_QUOTES, 'UTF-8') ?>" value="2" <?= !empty($permissao['admin']) ? 'checked' : '' ?>></td>
                            <td><input type="checkbox" name="<?= htmlspecialchars($nomePermissao . '3', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '5', ENT_QUOTES, 'UTF-8') ?>" value="3" checked disabled></td>
                            <td><span class="hidden"><?= htmlspecialchars((string) $permissao['descricao'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td class="w-10"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <table cellpadding="4" cellspacing="4">
                <thead>
                    <tr>
                        <th class="d-none"></th>
                        <th class="w-200">Nome</th>
                        <th class="w-60">Activar</th>
                        <th></th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tabela_s as $permissao): ?>
                        <?php $nomePermissao = (string) $permissao['nome']; ?>
                        <tr class="show">
                            <td class="d-none"></td>
                            <td><?= htmlspecialchars($nomePermissao, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><input type="checkbox" onclick="show_salvar()" name="<?= htmlspecialchars($nomePermissao . '0', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '0', ENT_QUOTES, 'UTF-8') ?>" value="0" <?= !empty($permissao['uexterno']) ? 'checked' : '' ?>></td>
                            <td><span class="hidden"><?= htmlspecialchars((string) $permissao['descricao'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td class="w-10"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="lista_comum">
        <div class="form_sel flex gap-10">
            <input type="submit" name="cancelar" value="Voltar" class="btn1" id="cancelar" onclick="cancelaInputsRequired()" />
            <div id="salvar" class="d-none">
                <input type="submit" name="salvar" value="Salvar" class="btn1" />
            </div>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
        </div>
    </div>

</form>
