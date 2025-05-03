<?php

require "../model/permissoes.php";

// array global com o conjunto de permissoes activas
$permis_array = array();

class permissoesController
{

    //valores defaul para as permissions da aplicação    
    public $permissoes_defaults = [
        // "nome" => [User, Gestor, Descrição] 
        "M_Categorias" => [true, false, "Activa o Menu de Categorias"],
        "M_Salas" => [true, false. "Activa o Menu de Salas"],
        "M_Periodos" => [true, false, "Activa o Menu de Períodos"],
        "M_Utilizadores" => [true, false, "Activa o Menu de Utilizadores"],
        "M_Permissoes" => [true, false, "Activa o Menu de Permissões"], 
        "M_Estatisticas" => [true, false, "Activa o Menu de Estatísticas"],
        "M_Relatorios" => [true, false, "Activa o Menu de Relatórios"],
        "A_RegNovoUtilizador" => [true, null, "Activa o registo de novos utilizadores no ecrã de login."], 
    ];

    public $niveis = [
        0 => array("uexterno","u","User"),  
        1 => array('uinterno',"g","Gestor"),
        2 => array("admin","a","Admin"),
        3 => array("superadmin","sa","SuperAdmin")   //este nivel não tem entradas na BD, e tem TODAS as AUTORIZAÇõES activas.
    ];
    

    //carrega o array global das Permissões salvas na BD
    function __construct()
    {

        global $permis_array;
        
        $permissoes = new Permissoes();
        $linhas = $permissoes->listar();

        foreach ($linhas as $linha) {
            $permis_array[$linha["nome"]] =  
               array($linha[$this->niveis[0][0]], $linha[$this->niveis[1][0]], $linha[$this->niveis[2][0]] ) ;
        }
    }

    function validaPermissao($permissao, $nivel)
    {
        global $permis_array;

        if ($this->niveis[$nivel][0] == "superadmin") {
            $valido = true;
        } else {
            if (array_key_exists($permissao, $permis_array)) {
                $valido = $permis_array[$permissao][$nivel];
            } else {
                $valido = false;
            }
        }
        return $valido;
    }

    function nomeNivel($nivel, $tipoNome = 0)    
    {
        if (array_key_exists($nivel, $this->niveis)) {
            if ($tipoNome == 0) {
                return $this->niveis[$nivel][0]; // nome da coluna do nivel na BD 
            } else if ($tipoNome == 1) {
                return $this->niveis[$nivel][1]; // abreviatura do nivel
            } else if ($tipoNome == 2) {
                return $this->niveis[$nivel][2]; // nome do nivel
            }
            return $this->niveis[$nivel][3];
        } else {
            return "N/A";
        }
    }


    function salvar()
    {
        $permissoes = new Permissoes();

        if (isset($_POST['salvar'])) {

            // Prepara um array $lista para recolher dados do $POST
            foreach ($this->permissoes_defaults as $key => $Value) {
                $lista[$key] = array(false, false, false);
            }

            // Preenche o array $lista com os valores do $POST
            foreach ($_POST as $post_key => $post_value) {
                // para serem distintos os names no post, o name tem um identificador adicional que é necessário retirar
                $post_key = substr($post_key, 0, strlen($post_key) - 1);

                //Se o nome de permissão existe nos defaults
                if (array_key_exists($post_key, $this->permissoes_defaults)) {
                    $post_value = $post_value - 2; // o valor do post é 2,3 ou 4, e o array $lista tem os indices 0,1 ou 2.

                    if ($post_value == 0) {        // 0 é o utilizador externo
                        $lista[$post_key][0] = true;                        
                    } else if ($post_value == 1) { // 1 é o gestor
                        $lista[$post_key][1] = true;
                    } else if ($post_value == 2) { // 2 é o admin
                        $lista[$post_key][2] = true;                        
                    } 
                }
            }

            foreach ($lista as $k => $l) {
                $permissoes->salvar(null, $k, $l[0], $l[1], $l[2], "Update");
            }

            header("Location: permissoes_form.php");
            exit;
        }
    }

    // Preencher e verificar base de dados
    function validarTabelaPermissoes()
    {

        // $permis_temp_nome = array();
        // $permis_temp_seq = array();


        $permissoes = new Permissoes();
        $linhas = $permissoes->listar();

        // Verifica permissoes na bd e elimina as não existentes
        foreach ($linhas as $linha) {
            if (!array_key_exists($linha['nome'], $this->permissoes_defaults)) {
                $permissoes->excluir($linha['nome']);
            } else {
                $ctrlBd[$linha['nome']] = $linha['seq'];                
            }
        }


        // addiciona na BD as permissões ausentes
        $i = 0;
        foreach ($this->permissoes_defaults as $default_key => $default_Value) {            
            if (!in_array($default_key, $ctrlBd)) {  
                // se não existe na BD, adiciona
                $permissoes->salvar($i, $default_key, 
                        $this->permissoes_defaults[$default_key][0], $this->permissoes_defaults[$default_key][1], 
                        true, "Insert");
                $ctrlBd[] = [$default_key=>$i];             
            } else if ($i <> $ctrlBd[$default_key]) {
                // se existe mas tem valor de "seq" diferente, renumera   
                $permissoes->salvar($i, $default_key, 
                    $this->permissoes_defaults[$default_key][0], $this->permissoes_defaults[$default_key][1], 
                    true, "Renumera");
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

            for ($i = 2; $i <= 4; $i++) {
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

}


?>