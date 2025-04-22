<?php

require "../model/permissoes.php";



$permis_array = array();

class permissoesController
{

    //defaul values for permissions
    public $permis = [
        "M_Salas" => [false, true],
        "M_Periodos" => [false, true],
        "M_Utilizadores" => [false, true],
        "M_Permissoes" => [false, true],
        "M_Estatisticas" => [false, true],
        "M_Relatorios" => [true, true],
    ];

    //function carregaTabelaPermissoes()
    function __construct()
    {

        global $permis_array;

        $permissoes = new Permissoes();
        $linhas = $permissoes->listar();

        foreach ($linhas as $linha) {
            $permis_array[$linha["nome"]] = array($linha['user'], $linha['admin']);
        }

        // var_dump($linhas);
        // var_dump($permis_array);
        // exit;

    }


    function salvar()
    {
        $permissoes = new Permissoes();


        if (isset($_POST['salvar'])) {

            // Prepara um array $lista para recolher dados do $POST
            foreach ($this->permis as $key => $Value) {
                $lista[$key] = array(false, false);
            }

            // Preenche o array $lista com os valores do $POST
            foreach ($_POST as $key => $value) {
                // para serem distintos os names no post, o name tem um identificador adicional que é necessário retirar
                $key = substr($key, 0, strlen($key) - 1);

                if (array_key_exists($key, $this->permis)) {
                    $value = $value - 2;
                    if ($value == 0) {
                        $lista[$key][0] = true;
                        if (!array_key_exists(1, $lista[$key])) {
                            $lista[$key][1] = false;
                        }
                    } else {
                        $lista[$key][1] = true;
                        if (!array_key_exists(0, $lista[$key])) {
                            $lista[$key][0] = false;
                        }

                    }
                }
            }


            foreach ($lista as $k => $l) {
                $permissoes->salvar(null, $k, $l[0], $l[1], "U");
            }

            header("Location: permissoes_form.php");
            exit;

        }
    }

    // Preencher e verificar base de dados
    function validarTabelaPermissoes()
    {
        $permissoes = new Permissoes();
        $linhas = $permissoes->listar();

        $permis_temp_nome = array();
        $permis_temp_seq = array();


        // Verifica permissoes na bd e elimina as não existentes
        foreach ($linhas as $linha) {
            if (!array_key_exists($linha['nome'], $this->permis)) {
                $permissoes->excluir($linha['nome']);
            } else {
                $permis_temp_nome[] = $linha['nome'];
                $permis_temp_seq[] = $linha['seq'];
            }
        }


        // addiciona na BD as permissões não existentes
        $i = 0;
        foreach ($this->permis as $key => $Value) {
            if (!in_array($key, $permis_temp_nome)) {
                $permissoes->salvar($i, $key, $this->permis[$key][0], $this->permis[$key][1], "I");
                $permis_temp_seq[] = $i;

                // se existe mas tem valor de seq diferente, renumera    
            } else if ($i <> $permis_temp_seq[$i]) {
                $permissoes->salvar($i, $key, $this->permis[$key][0], $this->permis[$key][1], "R");
            }
            $i++;
        }

    }

    // listagem
    function listar()
    {

        $this->validarTabelaPermissoes();

        $permissoes = new Permissoes();
        $linhas = $permissoes->listar();

        $tabela = '';

        foreach ($linhas as $linha) {

            $tabela .= '<tr><td>' . $linha[1] . '  </td>';

            for ($i = 2; $i <= 3; $i++) {
                $tabela .= '<td><input type="checkbox" onclick="show_salvar()" name="' . $linha[1] . $i . '" id="' . $linha[1] . $i . '" value=' . $i . ' ' . ($linha[$i] ? "checked" : "") . '>';
                // $tabela .= '<label for="' . $linha[1] . '" >' . $linha[1] . '</label>' 
                $tabela .= '</td> ';
            }
            //$tabela .= '<td width="30"> </td>';
            //$tabela .= '<td width="100"> </td>';
            $tabela .= '</tr>';
        }

        return $tabela;

    }


    function validaPermissao($permissao, $nivel)
    {
        global $permis_array;
        $valido = false;

        if ($nivel == 2) {
            $valido = true;
        } else {
            if (array_key_exists($permissao, $permis_array)) {
                $valido = $permis_array[$permissao][$nivel];

            }
        }

        //var_dump($permis_array);
        //var_dump($permis_array[$permissao][$nivel]);
        //exit;

        return $valido;
    }

}


?>