<?php
namespace Salas\Controllers;


use Salas\Core\Persistence\Database;
use Salas\Models\Permissoes;

class PermissoesController
    {
	private Database $db;

    // conjunto de permissoes ativas carregadas da BD
    private array $permissions = [];

    // valores default para as permissões da aplicação
    // a aplicação cria-os na BD     
    public $permissoes_defaults = [
        // "nome" => [tipo, permissão User, permissão Gestor, Descrição] 
        "M_Categorias" => ['n', false, true, false, "Autoriza acesso ao Menu de Categorias"],
        "M_Usos" => ['n', false, true, false, "Autoriza a gestão de Usos"],
        "M_Salas" => ['n', false, true, false, "Autoriza acesso ao Menu de Salas"],
        "M_Periodos" => ['n', false, true, false, "Autoriza acesso ao Menu de Períodos"],
        "M_Utilizadores" => ['n', false, true, false, "Autoriza acesso ao Menu de Utilizadores"],
        "M_Permissoes" => ['n', false, true, false, "Autoriza acesso ao Menu de Permissões"],
        "M_Estatisticas" => ['n', false, true, false, "Autoriza acesso ao Menu de Estatísticas"],
        "M_Relatorios" => ['n', false, true, false, "Autoriza acesso ao Menu de Relatórios"],
        "M_Reservas" => ['n', true, true, true, "Autoriza acesso ao formulário de reservas e exclusão de reservas"],
        "M_EliminarReservasUtilizador" => ['n', false, true, true, "Autoriza eliminar todas as reservas de um utilizador"],
        "M_FiltroUtilizador" => ['n', false, true, true, "Autoriza o filtro por utilizador no dashboard"],
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


    public function __construct(Database $db)
    {
        $this->db = $db;
        //carrega as Permissões salvas na BD    
        $permissoes = new Permissoes($this->db);
        $linhas = $permissoes->listar();

        foreach ($linhas as $l) {
            $this->permissions[$l["nome"]] = array($l[$this->niveis[0][0]], $l[$this->niveis[1][0]], $l[$this->niveis[2][0]]);
        }
    }

    function validaPermissao($permissao, $nivel)
        {
        $validade = ($this->niveis[$nivel][0] == "superadmin") || (array_key_exists($permissao, $this->permissions) && $this->permissions[$permissao][$nivel]);

        return $validade;
        }

    function nomeNivel($nivel, $tipoNome = 0)
        {
        if (array_key_exists($nivel, $this->niveis)) {
            if ($tipoNome == 0) {
                return $this->niveis[$nivel][0]; // nome da coluna do nivel na BD 
                }
            else if ($tipoNome == 1) {
                return $this->niveis[$nivel][1]; // abreviatura do nivel
                }
            else if ($tipoNome == 2) {
                return $this->niveis[$nivel][2]; // nome do nivel
                }
            return $this->niveis[$nivel][3];
            }
        return "N/A";
        }

    function requirePermission($permissao)
        {
        if (!isset($_SESSION['user_nivel']) || !$this->validaPermissao($permissao, $_SESSION['user_nivel'])) {
            header('Location: ' . app_url());
            exit;
            }
        }

    // 🎯 NOVO MÉTODO: Retorna ao Dashboard Inicial
    function cancelar()
        {
        if (isset($_POST['cancelar'])) {
            header('Location: ' . app_url());
            exit;
            }
        return false;
        }

    function salvar()
        {
        $permissoes = new Permissoes($this->db);

        if (isset($_POST['salvar'])) {

            // Prepara um array $lista para recolher dados do $POST
            foreach ($this->permissoes_defaults as $nome => $value) {

                $tipo = $value[0];

                if ($value[0] == 'n') {
                    $lista[$nome] = array($tipo, false, false, false);
                    }
                else {
                    $lista[$nome] = array($tipo, false, null, null);
                    }
                }

            // Preenche o array $lista com os valores do $POST
            foreach ($_POST as $post_nome => $post_value) {

                // 🎯 CORREÇÃO CRÍTICA: Se for o botão salvar ou a variável acao, ignora e salta para o próximo
                if ($post_nome === 'salvar' || $post_nome === 'acao') {
                    continue;
                    }

                // para serem distintos os names no post, o name tem um identificador adicional que é necessário retirar
                $post_nome = substr($post_nome, 0, strlen($post_nome) - 1);

                // Se o nome de permissão existe nos defaults
                if (array_key_exists($post_nome, $this->permissoes_defaults)) {

                    if ($post_value == 0) {        // 0 é o utilizador externo
                        $lista[$post_nome][1] = true;
                        }
                    else if ($post_value == 1) { // 1 é o gestor
                        $lista[$post_nome][2] = true;
                        }
                    else if ($post_value == 2) { // 2 é o admin
                        $lista[$post_nome][3] = true;
                        }
                    }
                }

            foreach ($lista as $k => $l) {
                $permissoes->update(null, $k, $l[0], $l[1], $l[2], $l[3], "Update");
                }

            unset($_SESSION['auth_permissions']);
            header('Location: ' . app_url('?acao=permissoes_form'));
            exit;
            }
        return false;
        }
    // Preencher e verificar base de dados
    function validarTabelaPermissoes()
        {

        $permissoes = new Permissoes($this->db);
        $linhas = $permissoes->listar();

        $ctrl_seq[''] = '0';
        foreach ($linhas as $linha) {
            if (!array_key_exists($linha['nome'], $this->permissoes_defaults)) {
                // Verifica permissoes na bd, e elimina as não parametrizadas em $permissoes_defaults
                $permissoes->excluir($linha['nome']);
                }
            else {
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

                }
            else if ($i <> $ctrl_seq[$nome]) {
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

        $permissoes = new Permissoes($this->db);
        $linhas = $permissoes->listar($tipo);

        $tabela = [];


        foreach ($linhas as $linha) {
            $nome = $linha['nome']; // nome da permissão

            if ($tipo == 'n') {

                $linha['descricao'] = $this->permissoes_defaults[$linha['nome']][4];
                $tabela[] = $linha;
                }
            else if ($tipo == 's') {
                $linha['descricao'] = $this->permissoes_defaults[$linha['nome']][4];
                $tabela[] = $linha;
                }
            }

        return $tabela;

        }


    }


?>
