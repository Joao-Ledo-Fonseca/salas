<?php

require_once "config.php";
require_once "../controller/usuarioController.php";

$usuarioController = new UsuarioController();


if (isset($_POST['entrar'])) {
    $result = $usuarioController->autenticarController();
    $errormsg = ($result == 0 ? 'E-mail ou senha inválidos' : '');

} else {
    $errormsg = '';
    if (isset($_POST['validar'])) {
        $result = $usuarioController->salvar();
        $errormsg = ($result == 0 ? 'Nome ou email já existe!' : 'Utilizador criado com sucesso!');
    };
};


$validar = ((isset($_POST['validar']) && $result > 0) ? true : false);
$cancelar = (isset($_POST['cancelar']) || isset($_POST['OK']) ? true : false);
$registar = ((isset($_GET['registar']) || (isset($_POST['validar']) && $result == 0)) ? true : false);

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <script src="js/jquery.js"></script>
    <script src="js/lib.js"></script>

    <link rel="stylesheet" type="text/css" href="css/estilo.css" />

    <style type="text/css">
        body,
        td,
        th {
            font-family: "Open Sans", sans-serif;
        }

        .link {
            text-decoration: none;
            color: #2C73A8;
            font-size: 14px;
            font-weight: bold;
            text-align: right;
        }

        
    </style>
    <title>Sistema de Reservas de Salas</title>
</head>

<body>
    <div id="content">
        <div id="esquerdo">
            <img src="img/login.jpg" width="478" height="320" />

            <div id="aviso">
                <h3>Clique, Informe e Reserve</h3>
                A forma mais pr&aacute;tica e simples de reservar salas de aula, laborat&oacute;rios e outros
                espa&ccedil;os.
            </div>
        </div>
        <div id="direito">
            <br />
            <br />
            <img src="img/logo.png" alt="Sistema de Reservas de Salas de Aula"
                title="Sistema de Reservas de Salas de Aula" width="220" height="72" />
            <br />
            <br />
            versão <strong>1.3</strong>&nbsp;<br />
            <!-- <span style="color:#900"><?php echo $errormsg; ?></span><br /> -->

            <div id="direito_log">
                <form action="login.php" method="post" name="Formulario">

                    <span style="color:#900"><?php echo $errormsg; ?></span><br />

                    <input type="text" name="email" id="email" placeholder="E-mail" autocomplete="on" /><br /><br />
                    <input type="password" name="senha" id="senha" placeholder="Senha" autocomplete="on" /><br /><br />


                    <!--
                    <<span class="tooltip">
                        <input type="submit" id="entrar" name="entrar" value="Entrar" class="btn1" />
                        <?= ((strlen($errormsg) > 0) ? '<span class="tooltiptext">Use um Email e Senha válidos!</span>' : ""); ?>
                    </span>
                    -->

                    <input type="submit" id="entrar" name="entrar" value="Entrar" class="btn1" />

                    <!--
                    <span class="tooltip">
                        <input type="submit" id="registar" name="registar" value="Registar" class="btn1" style="display:none">
                        <span class="tooltiptext">Registar novo utilizador</span>
                    </span>
                    -->

                    <br /><br /><br />

                    <div class="link">
                        <span class="tooltip">
                            <a id="registar" name="registar" value="Registar" class="link" style="display:none"
                                href="./login.php?registar=registar">&#10132; Registar novo utilizador</a>
                            <span class="tooltiptext">Crie a sua nova conta de utilizador</span>
                        </span>
                    </div>


                </form>
            </div>

            <div id="direito_reg" name="novo" style="display:none">
                <form action="login.php" method="post" name="Formulario2">

                    <span style="color:#900"><?php echo $errormsg; ?></span><br />

                    <input type="hidden" name="id" value=0 />
                    <input type="text" name="nome" id="nome_r" placeholder="Nome de Utilizador" required />
                    <br />
                    <br />
                    <input type="text" name="email" id="email_r" placeholder="E-mail" autocomplete="on" required />
                    <br />
                    <br />
                    <input type="password" name="senha" id="senha_r" placeholder="Senha" autocomplete="on" />
                    <br />
                    <br />

                    <input type="submit" id="validar" name="validar" value="Validar" class="btn1">
                    <input type="submit" id="cancelar" name="cancelar" value="Cancelar" class="btn1"
                        onclick="cancelaInputsRequired()">
                    <input type="submit" id="ok" name="OK" value="OK" class="btn1" style="display:none"
                        onclick="cancelaInputsRequired()">
                </form>

            </div>
        </div>
    </div>
    <div id="rodape">
        Sistema de Reserva de salas é <a href="https://pt.wikipedia.org/wiki/GNU_General_Public_License">GNU General
            Public License</a>
    </div>
</body>


<script>


    $(document).ready(
        function () {
            msglen = <?= strlen($errormsg) ?>;
            console.log(msglen);
            if (msglen > 0) {
                $("[name='registar']").show();
            };
        });

    $(document).ready(
        function () {
            var x = <?= json_encode($cancelar) ?>;
            if (x) {
                $("#direito_log").show();
                $("direito_reg").hide();
            };
        });

    $(document).ready(
        function () {
            var x = <?= json_encode($registar) ?>;
            if (x) {
                $("#direito_log").hide();
                $("#direito_reg").show();
            };
        });

    $(document).ready(
        function () {
            var x = <?= json_encode($validar) ?>;
            if (x) {
                $("#direito_log").hide();
                $("#direito_reg").show();

                $("#ok").show();
                $("#validar").hide();
                $("#cancelar").hide();
            };
        });


</script>

</html>