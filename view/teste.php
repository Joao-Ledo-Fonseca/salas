<!DOCTYPE html>
<html lang="pt-PT">

<?php
$cor_base = "#602030";
$cor_texto_base = "#fff";
$cor_base_destaque = "#401A20";
$cor_main = "#ffffff";
$cor_texto_main = "#000000";
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 8pt;
        }

        .menu {
            background-color: <?= $cor_base ?>;
            color: <?= $cor_texto_base ?>;
            width: 200px;
            height: 100vh;
            padding: 10px 0 0 0;
            z-index: 1001;
            float: left;
            display: block;
            position: fixed;
            left: 0;
            top: 0;
            overflow: hidden;
            
        }

        .menu-inner {
            padding: 400px 0 0;
        }


        .menu .closebtn {
            font-size: 30px;
            cursor: pointer;
            color: #fff;
            position: absolute;
            top: 10px;
            right: 15px;
            z-index: 1101;
            display: none;
        }
        .menu ul {
            padding: 0;
            margin: 0;
        }
        .menu li {
            padding: 15px;
            margin: 0;
            color: #FFFFFF;
            list-style: none;
            transition: background-color 0.7s, color 0.7s;
            background-color: <?= $cor_base_destaque ?>;
            cursor: pointer;
        }
        .menu li:hover {
            background-color: #D4D4D4;
            color: <?= $cor_base_destaque ?>;
        }
        .menu a {
            color: #FFFFFF;
            text-decoration: none;
        }
        .menu li:hover a {
            color: <?= $cor_base_destaque ?>;
        }

        .corpo {
            padding: 20px;
            margin-left: 200px;
            display: block;
        }

        .hamburger {
            font-size: 30px;
            cursor: pointer;
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 1100;
            color: #222;
            background: #fff;
            border-radius: 5px;
            padding: 5px 10px;
            transition: opacity 0.3s;
            display: none;
        }

        .hamburger.hide {
            opacity: 0;
            pointer-events: none;
        }


        @media (max-width: 900px) {
            .menu {
                position: fixed;
                left: -220px;
                top: 0;
                height: 100vh;
                width: 200px;
                transition: left 0.3s;
                float: none;
            }
            .menu.open {
                left: 0;
            }
            .menu .closebtn {
                display: block;
            }
            .hamburger {
                display: block;
            }
            .corpo {
                margin-left: 0;
                padding-top: 60px; /* <-- Adicione esta linha */            
            }
        }

        table { 
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid <?= $cor_base ?>;
            border-collapse: collapse;
        }
        th {
            background-color: <?= $cor_base ?>;
            color: <?= $cor_texto_base ?>;
        }


        .btn1 {
            background-color: <?= $cor_base ?>;
            color: <?= $cor_texto_base ?>;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            /* font-size: 14pt; */
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .btn1:hover {
            background-color: <?= $cor_base_destaque ?>;
            color: <?= $cor_texto_base ?>;            
        }

    </style>


    <script>
        function openNav() {
            var menu = document.getElementsByClassName("menu")[0];
            var hamburger = document.getElementsByClassName("hamburger")[0];
            menu.classList.add("open");
            if (hamburger) hamburger.classList.add("hide");
        }
        function closeNav() {
            var menu = document.getElementsByClassName("menu")[0];
            var hamburger = document.getElementsByClassName("hamburger")[0];
            menu.classList.remove("open");
            if (hamburger) hamburger.classList.remove("hide");
        }
        window.onclick = function (event) {
            var menu = document.getElementsByClassName("menu")[0];
            var hamburger = document.getElementsByClassName("hamburger")[0];
            if (window.innerWidth <= 900 && menu.classList.contains("open")) {
                if (!menu.contains(event.target) && event.target !== hamburger) {
                    closeNav();
                }
            }
        }
    </script>
</head>
<body>
    <!-- Menu hamburger -->
    <div class="hamburger" onclick="openNav()">&#9776;</div>
    <div class="menu">
        <span class="closebtn" onclick="closeNav()">&times;</span>
        <div class="menu-inner">
        <ul>
            <li>Batatas</li>
            <li>Batatas</li>
            <li>Batatas</li>
            <li>Batatas</li>
        </ul>
        </div>
    </div>
    <div class="corpo">
        <h3>Conteúdo Principal</h3>
        <div class="nav">
            <input type="button" value="Novo" class="btn1" onclick="alert('Novo item')">
        </div>
        <div class="content">
            <table class="table">
                <thead>
                    <tr>
                        <th>Coluna 1</th>
                        <th>Coluna 2</th>
                        <th>Coluna 3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Dados 1</td>
                        <td>Dados 2</td>
                        <td>Dados 3</td>
                    </tr>
                    <tr>
                        <td>Dados 1</td>
                        <td>Dados 2</td>
                        <td>Dados 3</td>
                    </tr>
                    <tr>
                        <td>Dados 1</td>
                        <td>Dados 2</td>
                        <td>Dados 3</td>
                    </tr>
                    <tr>
                        <td>Dados 1</td>
                        <td>Dados 2</td>
                        <td>Dados 3</td>
                    </tr>
                    <tr>
                        <td>Dados 1</td>
                        <td>Dados 2</td>
                        <td>Dados 3</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Este é o conteúdo principal da página.</p>
        <style>
            .blocos {
                width: 30%; 
                min-width: 200px;                 
                background-color: #888;
                float:left; 
                margin-right: 10px;
                margin-bottom: 10px; /* Espaçamento entre os blocos */
                padding: 10px;
                display: inline-block;
                height: 400px;
                overflow: hidden;
            }
            .blocos p {
                font-size:30px;
                font-weight: bold;
                color: <?= $cor_texto_base ?>;
                margin: 0;
                padding: 0;
                width: 200px;                
            }
        </style>
        <div class="blocos">
            <p>Este é um parágrafo dentro de uma div com 30% da largura da página.</p>
        </div> 
        <div class="blocos">
            <p>Este é um parágrafo dentro de uma div com 30% da largura da página.</p>
        </div>        
        <div class="blocos">
            <p>Este é um parágrafo dentro de uma div com 30% da largura da página.</p>
        </div>     
        <div style="clear: both;"></div>
        <div  style="background-color: <?= $cor_base ?>; padding: 10px; margin-top: 10px; ">
            E aqui retomamos
        </div>
    </div>
</body>
</html>