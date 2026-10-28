<!DOCTYPE html>   
<html lang="pt-PT">

<?php

?>


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

</head>
<title>Cadastro de periodos</title>

<body>

    <!-- menu esquerdo -->
    <?php include __DIR__ . "/MenuEsquerdo.php"; ?>

    <!-- conteudo -->
    <div class="corpo">

        <h3> Cadastro de Períodos </h3>

        <div class="container_conteudo lista_comum">


            <form name="form1" method="post" action="<?= app_url('?acao=periodo_form') ?>">

                <input type="hidden" name="id" value="<?= $id ?>" />

                <table class="tabela_comum" cellpadding="4" cellspacing="4">
                    <tr>
                        <td width="100"> Nome </td>
                        <td><input type="text" name="nome" value="<?= $nome ?>" maxlength="30" size="30" width="30px" /> </td>
                        
                    </tr>
                    <tr>
                        <td width="100"> Seq </td>
                        <td><input type="number" name="seq" value="<?= $seq ?>" /> </td>
                        
                    </tr>
                </table>

                <br>
                <div class="form_sel lista_comum">

                    <input type="submit" name="salvar" value="Salvar" class="btn1" />
                    <input type="submit" name="excluir" value="Excluir" class="btn1" />
                    <input type="submit" name="cancelar" value="Cancelar" class="btn1" id="cancelar"
                            style="display:inline" onclick="cancelaInputsRequired()" />
                </div>
            </form>

        </div>
    </div>

</body>

</html>
