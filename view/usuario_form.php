<?php

require_once "seguranca.php";
require_once "../controller/usuarioController.php";

$usuarioController = new UsuarioController();

$usuario = $usuarioController->excluir();
$usuario = $usuarioController->salvar();
$usuario = $usuarioController->abrir();

if (isset($usuario[0]))
    extract($usuario[0]);


if (!isset($id)) {
    $nome = '';
    $email = '';
    $senha = '';
    $nivel = 0;
    $id = 0;
}

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <script src="js/jquery.js"></script>
    <script src="js/jquery.datetimepicker.full.js"></script>
    <script src="js/dateformat.js"></script>

    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">
    <script src="js/lib.js"></script>

    <script>
        nomeSessao  = "<?= $_SESSION['user_nome'] ?>";
        nivelSessao = <?= (int) $_SESSION['user_nivel'] ?>;
        nomeEditar  = "<?= $nome ?>";
        nivelEditar = "<?= $nivel ?>";        
    </script>

    <title>Perfil de utilizador</title>
</head>


<body onload="self_user(nomeSessao,  nivelSessao, nomeEditar, nivelEditar)">

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- conteudo -->

    <div class="corpo">

        <h3>Perfil de utilizador</h3>

        <form name="form1" method="post" target="_self">

            <input type="hidden" name="id" value="<?= $id ?>" />

            <table class="tabela_comum" cellpadding="4" cellspacing="4">

                <tr>
                    <td width="100"> Nome </td>
                    <td><input type="text" name="nome" value="<?= $nome ?>" required/> </td>
                </tr>
                <tr>
                    <!-- <td width="30"></td> -->
                    <td width="100"> E-mail</td>
                    <td> <input type="email" name="email" value="<?= $email ?>" /></td>
                </tr>
                <tr>
                    <td width="100"> Senha </td>
                    <td><input type="password" name="senha" value="<?= $senha ?>" /> </td>
                </tr>

                <tr id="permis2">
                    <!-- <td width="30"></td> -->
                    <td width="100">Nivel</td>
                    <td>
                        <select style="width:100px" name="nivel" id="nivel">
                            <option value="0" <?= ($nivel == 0) ? " selected" : '' ?>>Usuario</option>
                            <option value="1" <?= ($nivel == 1) ? " selected" : '' ?>>Admin</option>
                            <option value="2" <?= ($nivel == 2) ? " selected" : '' ?>>SuperAdmin</option>
                        </select>
                    </td>
                </tr>

            </table>

            <input type="submit" name="salvar" value="Salvar" class="btn1" />

            <input type="submit" name="excluir" value="Excluir" class="btn1" id="permis1" style="display:inline" />

        </form>

    </div>

</body>

</html>