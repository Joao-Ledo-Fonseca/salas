<?php

require "../model/permissoes.php";

// array global com o conjunto de permissoes activas
$permis_array = array();

class permissoesController
{

    // valores default para as permissões da aplicação
    // a aplicação cria-os na BD     
    public $permissoes_defaults = [
        // "nome" => [tipo, permissão User, permissão Gestor, Descrição] 
        "M_Categorias" => ['n', false, true, false, "Autoriza acesso ao Menu de Categorias"],
        "M_Salas" => ['n', false, true, false, "Autoriza acesso ao Menu de Salas"],
        "M_Periodos" => ['n', false, true, false, "Autoriza acesso ao Menu de Períodos"],
        "M_Utilizadores" => ['n', false, true, false, "Autoriza acesso ao Menu de Utilizadores"],
        "M_Permissoes" => ['n', false, true, false, "Autoriza acesso ao Menu de Permissões"],
        "M_Estatisticas" => ['n', false, true, false, "Autoriza acesso ao Menu de Estatísticas"],
        "M_Relatorios" => ['n', false, true, false, "Autoriza acesso ao Menu de Relatórios"],
        "A_RegNovoUtilizador" => ['s', true, null, null, "Activa o registo de novos utilizadores no ecrã de login. (não implementado)"],
        "A_PerfilUtilizador" => ['s', true, null, null, "Autoriza a edição do próprio Perfil, diretamente no Menu. (não implementado)"]
    ];


    //valores fixos para os níveis de utilizador
    public $niveis = [
        // [ nivel => Nome na BD, Abreviatura, Nome Extenso]
        0 => array("uexterno", "u", "User"),
        1 => array('uinterno', "g", "Gestor"),
        2 => array("admin", "a", "Admin"),
        // Este nivel não tem entradas na BD, programáticamente tem 
        // ====    TODAS AS AUTORIZAÇõES activas ====        
        3 => array("superadmin", "sa", "SuperAdmin")
    ];
    

    function __construct()
    {
        //carrega o array global das Permissões salvas na BD    

        global $permis_array;        

        $permissoes = new Permissoes();    
        $linhas = $permissoes->listar();

        foreach ($linhas as $l) {
            $permis_array[$l["nome"]] = array($l[$this->niveis[0][0]], $l[$this->niveis[1][0]], $l[$this->niveis[2][0]]);
        }

    }

    function validaPermissao($permissao, $nivel)
    {
        global $permis_array;
        return array_key_exists($permissao, $permis_array) || ($this->niveis[$nivel][0] == "superadmin");
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
            foreach ($this->permissoes_defaults as $nome => $value) {

                $tipo = $value[0];

                if ($value[0] = 'n') {
                    $lista[$nome] = array($tipo, false, false, false);
                } else {
                    $lista[$nome] = array($tipo, false, null, null);
                }
            }

            // Preenche o array $lista com os valores do $POST
            foreach ($_POST as $post_nome => $post_value) {
                // para serem distintos os names no post, o name tem um identificador adicional que é necessário retirar
                $post_nome = substr($post_nome, 0, strlen($post_nome) - 1);

                //Se o nome de permissão existe nos defaults
                if (array_key_exists($post_nome, $this->permissoes_defaults)) {

                    if ($post_value == 0) {        // 0 é o utilizador externo
                        $lista[$post_nome][0] = true;
                    } else if ($post_value == 1) { // 1 é o gestor
                        $lista[$post_nome][1] = true;
                    } else if ($post_value == 2) { // 2 é o admin
                        $lista[$post_nome][2] = true;
                    }
                }
            }

            foreach ($lista as $k => $l) {
                $permissoes->update(null, $k, $l[0], $l[0], $l[1], $l[2], "Update");
            }

            header("Location: permissoes_form.php");
            exit;
        }
        return false;
    }

    // Preencher e verificar base de dados
    function validarTabelaPermissoes()
    {

        $permissoes = new Permissoes();
        $linhas = $permissoes->listar();

        $ctrl_seq[''] = '0';
        foreach ($linhas as $linha) {
            if (!array_key_exists($linha['nome'], $this->permissoes_defaults)) {
                // Verifica permissoes na bd, e elimina as não parametrizadas em $permissoes_defaults
                $permissoes->excluir($linha['nome']);
            } else {
                // para as permissoes válidas, regista o numero de seq que está na BD
                $ctrl_seq[$linha['nome']] = $linha['seq'];
            }
        }

        $i = 0;
        foreach ($this->permissoes_defaults as $nome => $value) {
            // atualiza na BD as permissões ausentes ou a sequencia das já existentes  
            if (!array_key_exists($nome, $ctrl_seq)) {
                // se não existe na BD, adiciona
                $permissoes->salvar(
                    $i,
                    $nome,
                    $value[0],
                    $value[1],
                    $value[2],
                    $value[3]
                );
                $ctrlBd[] = [$nome => $i];

            } else if ($i <> $ctrl_seq[$nome]) {
                // se existe mas tem valor de "seq" diferente, renumera                   
                $permissoes->renumerar($i, $nome, );
            }
            $i++;
        }
    }

    // listagem
    function listar($tipo)
    {

        $this->validarTabelaPermissoes();

        $permissoes = new Permissoes();
        $linhas = $permissoes->listar($tipo);

        $tabela = '';


        foreach ($linhas as $linha) {
            $nome = $linha['nome']; // nome da permissão

            if ($tipo == 'n') {
                $tabela .= '<tr class="show">';
                $tabela .= '<td style="display:none"></td>';
                $tabela .= '<td>' . $nome . '</td>';
                $tabela .= '<td><input type="checkbox" onclick="show_salvar()" name="' . $nome . '0" id="' . $nome . '2" value=0 ' . ($linha['uexterno'] ? "checked" : "") . '></td> ';
                $tabela .= '<td><input type="checkbox" onclick="show_salvar()" name="' . $nome . '1" id="' . $nome . '3" value=1 ' . ($linha['uinterno'] ? "checked" : "") . '></td> ';
                $tabela .= '<td><input type="checkbox" onclick="show_salvar()" name="' . $nome . '2" id="' . $nome . '4" value=2 ' . ($linha['admin'] ? "checked" : "") . '></td> ';
                $tabela .= '<td><input type="checkbox"                         name="' . $nome . '3" id="' . $nome . '5" value=3 ' . 'checked' . ' disabled  ></td> ';
                $tabela .= '<td class="hidden"> ' . $this->permissoes_defaults[$linha['nome']][4] . '</td>';
                $tabela .= '<td></td></tr>';
            } else if ($tipo == 's') {
                $tabela .= '<tr class="show">';
                $tabela .= '<td style="display:none"></td>';
                $tabela .= '<td>' . $nome . '  </td>';
                $tabela .= '<td><input type="checkbox" onclick="show_salvar()" name="' . $nome . '0" id="' . $nome . '0" value=0 ' . ($linha['uexterno'] ? "checked" : "") . '></td> ';
                $tabela .= '<td class="hidden"> ' . $this->permissoes_defaults[$linha['nome']][4] . '</td>';
                $tabela .= '<td></td></tr>';
            }
        }

        return $tabela;

    }

}


?>