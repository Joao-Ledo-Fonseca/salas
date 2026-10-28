<!DOCTYPE html>
<html lang="pt-PT">


<?php

?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">       

    <!-- Scripts -->
    <script src="<?= app_url('/js/jquery.js') ?>"></script>
    <script src="<?= app_url('/js/jquery.datetimepicker.full.js') ?>"></script>
    <script src="<?= app_url('/js/dateformat.js') ?>"></script>
    <script src="<?= app_url('/js/lib.js') ?>"></script>

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">
    <link rel="stylesheet" href="<?= app_url('/css/estilo.css') ?>">

    <title>Cadastro de Salas</title>
</head>

<body>
    <!-- Formulário -->
    <div class="form"></div>

    <!-- Menu esquerdo -->
    <?php include __DIR__ . "/MenuEsquerdo.php"; ?>

    <!-- Conteúdo principal -->
    <div class="corpo">
        <h3>Cadastro de Salas</h3>

        <!--
        <div class="lista_comum container_top">            
            <form class="form_sel" name="form1" method="post" action="<?= app_url('?acao=sala_list') ?>">
                <div style="float:left;">
                    <input type="button" name="novo" value="Novo" class="btn1" 
                           onclick="abre('sala_form.php?categoria_filtro=<?= $categoria_id ?>')" />
                </div>
                   
                <span style="float:right;">
                    <label for="categoria"><b>Categorias</b></label>
                    <select id="categoria" style="width:200px;" name="categoria_id" onchange="this.form.submit()">                                            
                        
                        <option value="0" <?= $categoria_id === 0 ? 'selected' : '' ?>>Todas</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= (int) $categoria['id'] ?>" <?= ((int) $categoria['id'] === $categoria_id ? 'selected' : '') ?>><?= htmlspecialchars((string) $categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                        
                    </select>
                    
                </span>

                <div style="float:right; width:140px">                    
                    <input type="radio" id="activa" name="activas" value="activas" <?= ($activas=="activas"?'checked':'') ?> onchange="this.form.submit()">
                    <label for="activa">Só activas</label><br>
                    <input type="radio" id="inactiva" name="activas" value="inactivas" <?= ($activas=="inactivas"?'checked':'') ?> onchange="this.form.submit()">
                    <label for="inactiva">Inactivas</label><br>
                    <input type="radio" id="todas" name="activas" value="todas" <?= ($activas=="todas"?'checked':'') ?> onchange="this.form.submit()">
                    <label for="todas">Todas</label><br>                    
                </div>
            </form>
        </div>  -->

        <div class="lista_comum container_top">            
            <form class="form_sel" name="form1" method="post" action="<?= app_url('?acao=sala_list') ?>">
                
                <!-- 1. Bloco do Botão Novo (Esquerda - Intacto) -->
                <div style="float:left;">
                    <input type="button" name="novo" value="Novo" class="btn1" 
                           onclick="abre('sala_form.php?categoria_filtro=<?= $categoria_id ?>')" />
                </div>
                   
                <!-- 2. Bloco do Dropdown de Categorias (Direita) -->
                <span style="float:right; margin-left: 15px;">
                    <!-- 🎯 CORREÇÃO: Removido o label "Categorias", adicionada a classe do Funil e alargado para 160px livres para o texto respirar ao lado do ícone -->
                    <select id="categoria" class="dashboard-category-select" style="width:160px;" name="categoria_id" onchange="this.form.submit()">                                            
                        <option value="0" <?= $categoria_id === 0 ? 'selected' : '' ?>>Todas</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= (int) $categoria['id'] ?>" <?= ((int) $categoria['id'] === $categoria_id ? 'selected' : '') ?>><?= htmlspecialchars((string) $categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </span>

                <!-- 3. Bloco dos RadioButtons (Direita - Estrutura Intacta) -->
                <div style="float:right; width:140px">                    
                    <input type="radio" id="activa" name="activas" value="activas" <?= ($activas=="activas"?'checked':'') ?> onchange="this.form.submit()">
                    <!-- 🎯 CORREÇÃO: Envolvido o texto em <strong> e adicionada a classe de 13px -->
                    <label Lothar for="activa" class="dashboard-radio-text"><strong>Só activas</strong></label><br>
                    
                    <input type="radio" id="inactiva" name="activas" value="inactivas" <?= ($activas=="inactivas"?'checked':'') ?> onchange="this.form.submit()">
                    <!-- 🎯 CORREÇÃO: Envolvido o texto em <strong> e adicionada a classe de 13px -->
                    <label for="inactiva" class="dashboard-radio-text"><strong>Inactivas</strong></label><br>
                    
                    <input type="radio" id="todas" name="activas" value="todas" <?= ($activas=="todas"?'checked':'') ?> onchange="this.form.submit()">
                    <!-- 🎯 CORREÇÃO: Envolvido o texto em <strong> e adicionada a classe de 13px -->
                    <label for="todas" class="dashboard-radio-text"><strong>Todas</strong></label><br>                    
                </div>

            </form>
        </div>

        <div class="container_conteudo">
            <table class="lista_comum" cellpadding="4" cellspacing="4">
                <thead>
                    <tr>
                        <th width="150px">Categoria</th>
                        <th width="150px">Nome</th>
                        <th width="200px">Descrição</th>
                        <th width="200px">Activa</th>
                        <th width="50px">ID</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>

                    <?php $categoriaAnterior = null; ?>
                    <?php foreach ($lista as $sala): ?>
                        <?php $categoriaAtual = (string) $sala['categoria']; ?>
                        <tr>
                            <td style="border:none"><?= $categoriaAtual === $categoriaAnterior ? '' : htmlspecialchars($categoriaAtual, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><a href="<?= htmlspecialchars(app_url('?acao=sala_form&id=' . (int) $sala['id'] . '&categoria_filtro=' . rawurlencode((string) $categoria_id) . '&activas=' . rawurlencode((string) $activas)), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $sala['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                            <td><?= htmlspecialchars((string) $sala['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= !empty($sala['activa']) ? '&#10003;' : '-' ?></td>
                            <td><?= (int) $sala['id'] ?></td>
                            <td></td>
                        </tr>
                        <?php $categoriaAnterior = $categoriaAtual; ?>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
