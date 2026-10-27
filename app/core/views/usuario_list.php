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
    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/jquery.datetimepicker.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>">

    <title>Lista de Utilizadores</title>
    
</head>

<body>

    <!-- form -->
    <div class="form"></div>

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>
    
    <!-- conteudo -->
    <div class="corpo">

        <h3>Lista de Utilizadores</h3>
        
        <div class="lista_comum container_top">
            <form class="form_sel" name="form1" method="post" target="_self">
                <div class="form_sel">
                    <input type="button" name="novo" value="Novo" class="btn1" onclick="abre('usuario_form.php')" />
                </div>
            </form>
        </div>
        
        <div class="container_conteudo">

            <table class="lista_comum" cellpadding="4" cellspacing="4">
                <thead>
                    <tr>
                        <th width="150px">Nome</th>
                        <th width="150px">E-mail</th>
                        <th width="100px">Nível</th>
                        <th width="50px">ID</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($lista as $usuario): ?>
                        <tr>
                            <td><a href="<?= htmlspecialchars(app_url('?acao=usuario_form&id=' . (int) $usuario['id']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $usuario['nome'], ENT_QUOTES, 'UTF-8') ?></a></td>
                            <td><?= htmlspecialchars((string) $usuario['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $usuario['nivel_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int) $usuario['id'] ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>

            </table>
        </div>
    </div>

</body>

</html>
