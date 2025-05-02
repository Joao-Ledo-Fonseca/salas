<?php

require_once "seguranca.php";
require_once "../controller/usuarioController.php";

$usuarioController = new UsuarioController();
$pc = new PermissoesController();


$usuario = $usuarioController->cancelar();
$usuario = $usuarioController->excluir();
$usuario = $usuarioController->salvar();
$usuario = $usuarioController->abrir();

if (isset($usuario[0]))
    extract($usuario[0]);


if (!isset($id)) {
    $nome = '';
    $email = '';
    $senha = '';
    $telefone = '';
    $NIF = '';
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
        nomeSessao = "<?= $_SESSION['user_nome'] ?>";
        nivelSessao = <?= (int) $_SESSION['user_nivel'] ?>;
        nomeEditar = "<?= $nome ?>";
        nivelEditar = "<?= $nivel ?>";

        // controlo de permissões para usuario_form.php
        function self_user(uSessao, nSessao, uEditar, nEditar) {

            if (uEditar == uSessao || nEditar > nSessao || nSessao == 0) {
                document.getElementById("permissoes").style.display = "none";
                document.getElementById("excluir").style.display = "none";
            }
        }
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
                    <td><input type="text" name="nome" value="<?= $nome ?>" required /> </td>
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

                        <select style="width:100px" name="nivel" id="nivel">
                            <?php
                            $numNiveis = count($pc->niveis) - 1;
                            $highNivel = min($numNiveis, $_SESSION['user_nivel']);

                            for ($i = 0; $i <= $highNivel; $i++) { // para um utilizador só aparecem niveis iguais ou inferiores ao seu nivel
                                if ($nivel == $i) {
                                    echo "<option value='$i' selected>" . $pc->nomeNivel($i, 2) . "</option>";
                                } else {
                                    echo "<option value='$i'>" . $pc->nomeNivel($i, 2) . "</option>";
                                }
                            };
                            ?>
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

</body>

</html>