<!DOCTYPE html>   
<html lang="pt-PT">

<?php

require_once "seguranca.php";
require_once "../controller/dashboardController.php";

$dateFormat = $fm = 'M Y';
$tipo = 'm'; // w - week ; m - month

if (isset($_GET['tipo'])) {   // 
    $tipo = $_GET['tipo'];  //- week ; m - month; p - periodo     
    switch ($tipo) {
        case 'w':
            $dateFormat = $fm = 'W - Y (M)';
            // $tipo = 'w';
            break;
        case 'p':
            $dateFormat = $fm = 'd/m/Y';
            //$tipo = 'p';
            break;
        case 'm':
            $dateFormat = $fm = 'M Y';
            // $tipo = 'm'; // w - week ; m - month
            break;
    }
}


if (isset($_GET['data'])) {
    $hoje = date_create_from_format("dmY", $_GET['data']);
} else {
    $hoje = new DateTime();
}
$hoje_pt = traduz_data($hoje->format($dateFormat), 'pt');

// configurar dias
$dia_anterior = date_create_from_format('dmY', $hoje->format("dmY"));
$dia_anterior->modify(($tipo == 'm' ? '-1 month' : '-1 week'));
// $dia_anterior_pt = traduz_data($dia_anterior->format($dateFormat), 'pt');

$dia_posterior = date_create_from_format('dmY', $hoje->format("dmY"));
$dia_posterior->modify(($tipo == 'm' ? '+1 month' : '+1 week'));
// $dia_posterior_pt = traduz_data($dia_posterior->format($dateFormat), 'pt');



function traduz_data($date, $lang = 'en', &$fm = '')
{
    $meses_pt = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $meses_en = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    if ($lang == 'en') {
        if (str_contains($fm, 'W ')) {
            //retira a semana, se o formato a tiver
            $date = substr($date, 3);
            $fm = substr($fm, 2);
        }
        return str_replace($meses_pt, $meses_en, $date);
    } else {
        return str_replace($meses_en, $meses_pt, $date);
    }

}

// Prepara a lista para a data escolhida
$dsc = new dashboardController();
$tabela_reservas = $dsc->listaReservasController($hoje, $tipo);


?>


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">   

    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!--<script src="https://code.jquery.com/jquery-3.7.1.js"></script> -->

    <!-- DateTimePicker -->
    <script src="js/jquery.datetimepicker.full.js"></script>
    <!--
  <script src="js/dateformat.js"></script>
-->
    <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">

    <!--    
  <script src="js/select2.min.js"></script>
  <link href="css/select2.min.css" rel="stylesheet" />
-->

    <script src="js/lib.js"></script>


    <!--- datatables from CDN 
  <link href="https://cdn.datatables.net/v/dt/moment-2.29.4/jszip-3.10.1/dt-2.3.0/b-3.2.3/b-colvis-3.2.3/b-html5-3.2.3/b-print-3.2.3/datatables.min.css" rel="stylesheet" integrity="sha384-Wo+/WGQ6+/Khk047S6EY+8UvjM4mcZbOCzRh1sLsZDrMs7fhjn06y0YqF6Rjy71P" crossorigin="anonymous">
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/moment-2.29.4/jszip-3.10.1/dt-2.3.0/b-3.2.3/b-colvis-3.2.3/b-html5-3.2.3/b-print-3.2.3/datatables.min.js" integrity="sha384-BlisAJT2ihy1yQ53ZmFNK+ukjiK9ATCvZNvGMQAqs5P6beHrE1Cd0zmUu8TcZZVc" crossorigin="anonymous"></script>  
-->

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">

    <!-- DataTables date pluggin -->
    <script src="https://cdn.datatables.net/plug-ins/2.2.2/sorting/date-uk.js"></script>
    <!-- DataTables Button Plugins -->
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.colVis.min.js"></script>

    <link rel=" stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.min.css">

    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->

    <script>

        // variavel com a data

        data = '<?= $hoje->format("dmY"); ?>';
        var dia_anterior = '<?= $dia_anterior->format("dmY"); ?>';
        var dia_posterior = '<?= $dia_posterior->format("dmY"); ?>';

        var format = '<?= $dateFormat; ?>';
        var tipo = '<?= $tipo; ?>';

        // configura a area visivel do formulario
        // $(window).on('scroll resize load', getVisible);

        // configura o calendario
        jQuery.datetimepicker.setLocale('pt');

        $(window).load(function () {
            $('#data').datetimepicker({
                timepicker: false,
                format: 'dmY',
            });
        });

        // Funções de eventos
        function atualizaTela(o) {
            // pegar os dados da data atual e recarregar a tela        
            var data = $(o).val();
            window.location.href = "relatorios2.php?data=" + data + "&tipo=" + tipo;
        }

        function alteraData(data) {
            window.location.href = "relatorios2.php?data=" + data + "&tipo=" + tipo;
        }

        $(document).ready(function () {
            jQuery('#data_pt').click(function () {
                jQuery('#data').datetimepicker('show'); //support hide,show and destroy command                    

            });
        });

        function toogletipo(tipo) {
            window.location.href = "relatorios2.php?data=" + data + "&tipo=" + tipo;
        }

        $(document).ready(function () {
            if (tipo == 'w') {
                $('#semanal').addClass('active');
                $('#mensal').removeClass('active');
                $('#periodo').removeClass('active');
            };
            if (tipo == 'm') {
                $('#semanal').removeClass('active');
                $('#mensal').addClass('active');
                $('#periodo').removeClass('active');
            };
            if (tipo == 'p') {
                $('#semanal').removeClass('active');
                $('#mensal').removeClass('active');
                $('#periodo').addClass('active');
            };
        });

    </script>

    <title>Relatorios</title>

</head>

<body>

    <!-- menu esquerdo -->
    <?php include "menu_esquerdo.php"; ?>

    <!-- conteudo -->
    <div class="corpo" style="overflow: scroll; height: 98%;">

        <div>

            <div class="container">
                <div class="titulo_inicial">

                    <img src="img/chevron_left.png" width="40" height="40" alt="" onclick="alteraData(dia_anterior)" />

                    <form method="get" action="relatori2.php" target="_self" name="form1"
                        style="display:inline;vertical-align: top;">
                        <input type="text" name="data_pt" id="data_pt" value="<?= $hoje_pt ?>" style="text-align:center"
                            readonly="readonly">
                        <input type="text" name="data" id="data" value="<?= $hoje->format("dmY") ?>"
                            onchange="atualizaTela(this)" style="visibility:hidden;position:absolute;z-index:-1; " />
                    </form>
                    <img src="./img/chevron_right.png" width="40" height="40" alt=""
                        onclick="alteraData(dia_posterior)" />

                    <span style="float:right">
                        <!-- <?= $tipo == 'w' ? 'Semanal ' : 'Mensal ' ?> -->
                        <button type="button" id="semanal" class="btn1" onclick="toogletipo('w')">Semanal</button>
                        <button type="button" id="mensal" class="btn1" onclick="toogletipo('m')">Mensal</button>
                        <button type="button" id="periodo" class="btn1" onclick="toogletipo('p')">Período</button>
                    </span>
                </div>
            </div>

            <div id="mensagem"> </div>

            <div class="container">

                <h3> Reservas </h3>

                <table class="lista_comum compact" id="print" cellpadding="4" cellspacing="4">
                    <thead>
                        <tr>
                            <th> Categoria </th>
                            <th> Sala</th>
                            <th> Nome </th>
                            <th> Data</th>
                            <th> Periodo </th>
                            <th> Status </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= $tabela_reservas ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

<?PHP

switch ($tipo) {
    case 'w':
        $titulo = '"Reservas - Semana ' . $hoje->format("W/Y") . '"';
        break;
    case 'm':
        $titulo = '"Reservas - ' . $hoje->format("M Y") . '"';
        break;
    case 'p':
        $titulo = '"Reservas - ' . $hoje->format("d/m/Y") . '"';
        break;
    default:
        $titulo = '"Reservas - ' . $hoje->format("d/m/Y") . '"';
}

?>
<script>
    titulo = <?= $titulo ?>;

    $(document).ready(function () {
        var table = new DataTable('#print',
            {
                searching: true,
                ordering: true,
                order: [3, 'desc'],
                paging: true,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100, 200],
                autoWidth: false,
                columnDefs:
                    [
                        { targets: [0], width: '80px' },
                        { targets: [1], width: '80px' },
                        { targets: [2], width: '100px' },
                        { type: 'date-uk', targets: [3], width: '80px' },
                        { targets: [4], width: '50px' },
                        { targets: [5], width: '50px' },
                    ],
                layout:
                {
                    topStart: {
                        buttons:
                            [
                                {
                                    extend: 'colvis',
                                    text: 'Colunas',
                                }
                            ]
                    },
                    bottomStart: 'pageLength',
                    bottom2: 'info',
                    bottom3:
                    {
                        buttons:
                            [
                                {
                                    extend: 'collection',
                                    text: 'Exporta',
                                    buttons: ['copy',
                                        {
                                            extend: 'csv',
                                            title: "Reserva de Salas",
                                            filename: "*"
                                        },
                                        {
                                            extend: 'excel',
                                            title: "Reserva de Salas",
                                            sheetName: "Reserva de Salas.xls"
                                        },
                                        {
                                            extend: 'print',
                                            title: titulo,
                                            header: false
                                        },
                                        {
                                            extend: 'pdf',
                                            title: "Reserva de Salas",
                                            filename: "*",
                                            header: false
                                        }
                                    ]
                                },
                            ]
                    },
                },
                language: {
                    url: './datatables/pt-PT.json',
                },
            }
        );
    });

</script>

</html>