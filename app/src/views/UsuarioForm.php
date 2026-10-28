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

    <script>
        nomeSessao = <?= json_encode($sessionUserName) ?>;
        nivelSessao = <?= $sessionUserLevel ?>;
        nomeEditar = <?= json_encode((string) $nome) ?>;
        nivelEditar = <?= (int) $nivel ?>;

        // controlo de permissões para usuario_form.php
        function user_self_edit(uSessao, nSessao, uEditar, nEditar) {

            if (uEditar == uSessao || nEditar > nSessao || nSessao == 0) {
                document.getElementById("permissoes").style.display = "none";
                document.getElementById("excluir").style.display = "none";
            }
        }
    </script>

    <title>Perfil de utilizador</title>
</head>


<body onload="user_self_edit(nomeSessao,  nivelSessao, nomeEditar, nivelEditar)">

    <!-- menu esquerdo -->
    <?php include __DIR__ . "/MenuEsquerdo.php"; ?>

    <!-- conteudo -->

    <div class="corpo">

        <h3>Perfil de utilizador</h3>


        <div class="container_conteudo lista_comum">
            <div class=aviso style="color:red"><?= $errormsg ?></div>

            <form name="form1" method="post" action="<?= app_url('?acao=usuario_form') ?>">

                <input type="hidden" name="id" value="<?= (int)$id ?>" />

                <table class="tabela_comum" cellpadding="4" cellspacing="4">

                    <tr>
                        <td width="100"> Nome </td>
                        <td><input type="text" name="nome" value="<?= $nome ?>" required /> </td>
                    </tr>
                    <tr>
                        <!-- <td width="30"></td> -->
                        <td width="100"> E-mail</td>
                        <td> <input type="email" name="email" value="<?= $email ?>" /></td>
                    </tr>
                    <tr>
                        <td width="100"> Senha </td>
                        <td><input type="password" name="senha" value=""
                                placeholder="Deixe em branco para manter a senha atual" /> </td>
                    </tr>

                    <tr>
                        <td width="100"> Telefone </td>
                        <td><input type="text" name="telefone" value="<?= $telefone ?>" /> </td>
                    </tr>

                    <tr>
                        <td width="100"> NIF </td>
                        <td><input type="text" name="NIF" value="<?= $NIF ?>" /> </td>
                    </tr>

                    <tr id="permissoes">
                        <!-- <td width="30"></td> -->
                        <td width="100">Nivel</td>
                        <td>

                            <select style="width:150px" name="nivel" id="nivel">
                                <?php foreach ($levelOptions as $option): ?>
                                    <option value="<?= (int) $option['value'] ?>" <?= ((int) $nivel === (int) $option['value'] ? 'selected' : '') ?>><?= htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                </table>

                <input type="submit" name="salvar" value="Salvar" class="btn1" />
                <input type="submit" name="cancelar" value="Cancelar" class="btn1" id="cancelar" style="display:inline"
                    onclick="cancelaInputsRequired()" />
                <input type="submit" name="excluir" value="Excluir" class="btn1" id="excluir" style="display:inline" />

            </form>
        </div>
    </div>

</body>

</html>
