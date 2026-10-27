<!DOCTYPE html>   
<html lang="pt-PT">

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <script src="<?= app_url('/js/jquery.js') ?>"></script>
    <script src="<?= app_url('/js/lib.js') ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?= app_url('/css/estilo.css') ?>" />

    
    <title>Sistema de Reservas de Salas</title>
</head>

<body class="pagina-login">

    <div style="margin: auto; display: block; clear: both">
        <div id="content">

            <div id="esquerdo">
                <img src="<?= app_url('/img/login.jpg') ?>" class="img_login" />
                <!-- <img src="<?= app_url('/img/login.jpg') ?>" class="img_login" width="478" height="320" /> -->
                <div id="aviso">
                    <h3>Clique, Informe e Reserve</h3>
                    A forma mais pr&aacute;tica e simples de reservar salas de aula, laborat&oacute;rios e outros
                    espa&ccedil;os.
                </div>
            </div>



            <div id="direito">
                <div>

                    <br />
                    <img src="<?= app_url('/img/logo.png') ?>" alt="Sistema de Reservas de Salas de Aula"
                        title="Sistema de Reservas de Salas de Aula" width="220" height="72" />
                    <br />
                    <br />
                    versão <strong>1.3</strong>&nbsp;<br />
                </div>
                <!-- <span style="color:#900"><?php echo $errormsg; ?></span><br /> -->

                <div id="direito_log">

                    <form action="<?= app_url('?acao=login') ?>" method="post" name="Formulario">

                        <span style="color:#900"><?= $loginView['errormsg'] ?></span><br /><br />

                        <input type="text" name="email_l" id="email" placeholder="Nome de utilizador ou e-mail" autocomplete="on" /><br /><br />
                        <input type="password" name="senha_l" id="senha" placeholder="Senha"
                            autocomplete="on" /><br /><br />
                        
                        <input type="submit" id="entrar" name="entrar" value="Entrar" class="btn1" />

                        <!--
                        <<span class="tooltip">
                        <input type="submit" id="entrar" name="entrar" value="Entrar" class="btn1" />
                        <?= ((strlen($errormsg) > 0) ? '<span class="tooltiptext">Use um Email e Senha válidos!</span>' : ""); ?>
                        </span>
                        -->

                        

                        <!--
                        <span class="tooltip">
                        <input type="submit" id="registar" name="registar" value="Registar" class="btn1" style="display:none">
                        <span class="tooltiptext">Registar novo utilizador</span>
                        </span>
                        -->

                        <br /><br /><br />

                        <div class="link">
                            <span class="tooltip">
                                <a style="display:none" id="registar" name="registar" value="Registar" class="link" 
                                    href="<?= htmlspecialchars(app_url('?acao=login&registar=registar'), ENT_QUOTES, 'UTF-8') ?>">&#10132; Registar novo utilizador</a>
                                <span class="tooltiptext">Crie a sua nova conta de utilizador</span>
                            </span>
                        </div>


                    </form>
                </div>

                <div id="direito_reg" name="novo" style="display:none">
                    <form action="<?= app_url('?acao=login') ?>" method="post" name="Formulario2">

                        <span style="color:#900"><?= $loginView['errormsg'] ?></span><br /><br/>

                        <input type="hidden" name="id" value=0 />
                        <input type="text" name="nome" id="nome" placeholder="Nome de Utilizador" required />
                        <br />
                        <br />
                        <input type="email" name="email" id="email" placeholder="e-mail" autocomplete="on" required />
                        <br />
                        <br />
                        <input type="password" name="senha" id="senha" placeholder="Senha" autocomplete="on" />
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

    </div>
</body>


<script>


    $(document).ready(
        function () {
            msglen = <?= strlen($loginView['errormsg']) ?>;
            console.log(msglen);
            if (msglen > 0) {
                $("[name='registar']").show();
            };
        });

    $(document).ready(
        function () {
            var x = <?= json_encode($loginView['retornou']) ?>;
            if (x) {
                $("#direito_log").show();
                $("#direito_reg").hide();
            };
        });

    $(document).ready(
        function () {
            var x = <?= json_encode($loginView['registar']) ?>;
            if (x) {
                $("#direito_log").hide();
                $("#direito_reg").show();
            };
        });

    $(document).ready(
        function () {
            var x = <?= json_encode($loginView['validou']) ?>;
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