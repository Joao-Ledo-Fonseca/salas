<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <script src="<?= app_url('/js/jquery.js') ?>"></script>
    <script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>
    <script src="<?= app_url('/js/dateformat.js') ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">

    <script src="<?= app_url('/js/lib.js') ?>"></script>
   
    <title>Permissoes</title>

</head>


<body>

    <!-- Formulário -->
    <div class="form"></div><br>

    <!-- Menu esquerdo -->
    <?php include __DIR__ . "/MenuEsquerdo.php"; ?>

    <!-- Conteúdo principal -->
    <div class="corpo">
        <h3>Permissões</h3>

        <div class="container_conteudo">
            <!-- 1. O FORM ABRE NO TOPO -->
            <form name="form1" method="post" action="<?= app_url('?acao=permissoes_form') ?>">

                <div class="lista_comum">
                    <!-- Tabela de Níveis -->
                    <table cellpadding="4" cellspacing="4">
                        <thead>
                            <tr>
                                <th style="display:none"></th>
                                <th style="width:200px;">Nome</th>
                                <th style="width:60px;"><?= htmlspecialchars($nivelLabels[0], ENT_QUOTES, 'UTF-8') ?></th>
                                <th style="width:60px;"><?= htmlspecialchars($nivelLabels[1], ENT_QUOTES, 'UTF-8') ?></th>
                                <th style="width:60px;"><?= htmlspecialchars($nivelLabels[2], ENT_QUOTES, 'UTF-8') ?></th>
                                <th style="width:100px;"><?= htmlspecialchars($nivelLabels[3], ENT_QUOTES, 'UTF-8') ?></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tabela_n as $permissao): ?>
                                <?php $nomePermissao = (string) $permissao['nome']; ?>
                                <tr class="show">
                                    <td style="display:none"></td>
                                    <td><?= htmlspecialchars($nomePermissao, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><input type="checkbox" onclick="show_salvar()" name="<?= htmlspecialchars($nomePermissao . '0', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '2', ENT_QUOTES, 'UTF-8') ?>" value="0" <?= !empty($permissao['uexterno']) ? 'checked' : '' ?>></td>
                                    <td><input type="checkbox" onclick="show_salvar()" name="<?= htmlspecialchars($nomePermissao . '1', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '3', ENT_QUOTES, 'UTF-8') ?>" value="1" <?= !empty($permissao['uinterno']) ? 'checked' : '' ?>></td>
                                    <td><input type="checkbox" onclick="show_salvar()" name="<?= htmlspecialchars($nomePermissao . '2', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '4', ENT_QUOTES, 'UTF-8') ?>" value="2" <?= !empty($permissao['admin']) ? 'checked' : '' ?>></td>
                                    <td><input type="checkbox" name="<?= htmlspecialchars($nomePermissao . '3', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '5', ENT_QUOTES, 'UTF-8') ?>" value="3" checked disabled></td>
                                    <td class="hidden"><?= htmlspecialchars((string) $permissao['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Tabela de Configurações Simples -->
                    <table cellpadding="4" cellspacing="4">
                        <thead>
                            <tr>
                                <th style="display:none"></th>
                                <th style="width:200px;">Nome</th>
                                <th style="width:60px;">Activar</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tabela_s as $permissao): ?>
                                <?php $nomePermissao = (string) $permissao['nome']; ?>
                                <tr class="show">
                                    <td style="display:none"></td>
                                    <td><?= htmlspecialchars($nomePermissao, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><input type="checkbox" onclick="show_salvar()" name="<?= htmlspecialchars($nomePermissao . '0', ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($nomePermissao . '0', ENT_QUOTES, 'UTF-8') ?>" value="0" <?= !empty($permissao['uexterno']) ? 'checked' : '' ?>></td>
                                    <td class="hidden"><?= htmlspecialchars((string) $permissao['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div> <!-- Fecha a lista_comum das tabelas -->

                <!-- 🎯 NOVA ESTRUTURA: Esta div principal está SEMPRE VISÍVEL -->
                <div class="lista_comum">
                    <div class="form_sel" style="display: flex; gap: 10px;">

                        <!-- O botão Voltar fica FORA do bloco oculto, logo estará SEMPRE VISÍVEL -->
                        <input type="submit" name="cancelar" value="Voltar" class="btn1" id="cancelar"
                            onclick="cancelaInputsRequired()" />

                        <!-- O botão Salvar fica dentro de um bloco isolado que começa OCULTO -->
                        <div id="salvar" style="display:none">
                            <input type="submit" name="salvar" value="Salvar" class="btn1" />
                        </div>



                    </div>
                </div>

                <!-- 3. O FORM FECHA AQUI -->
            </form>
        </div> <!-- Fecha o container_conteudo -->
    </div>

</body>

</html>
