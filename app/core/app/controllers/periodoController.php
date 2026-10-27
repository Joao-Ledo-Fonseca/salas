<?php

require_once __DIR__ . '/util.php';
require_once __DIR__. "/../models/periodo.php";

class periodoController
{
	private Database $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	function salvar()
	{
		if (isset($_POST['salvar'])) {
			$id = Util::clearparam($_POST['id']);
			$seq = Util::clearparam($_POST['seq']);
			$nome = Util::clearparam($_POST['nome']);


			if (strlen($nome) > 0) {
				$periodo = new Periodo($this->db);
				$periodo->salvar($id, $nome, $seq);
			}
            
            // 🎯 CORREÇÃO 1: Redirecionar para a nova rota pública
			header('Location: ' . app_url('?acao=periodo_list'));
			exit();
		}
		return false;
	}

	function cancelar()
	{
		if (isset($_POST['cancelar'])) {

            // 🎯 CORREÇÃO 2: Redirecionar para a nova rota pública
			header('Location: ' . app_url('?acao=periodo_list'));
			exit();
		}
		return false;
	}


	function excluir()
	{
		if (isset($_POST['excluir'])) {
			$id = Util::clearparam($_POST['id']);

			$periodo = new Periodo($this->db);
            
            // 🎯 CORREÇÃO 3: Try/Catch contra erro fatal se houver reservas no período
            try {
                $periodo->excluir($id);
            } catch (PDOException $e) {
                $errormsg = 'Erro ao excluir o período! Existem reservas associadas.';
                header('Location: ' . app_url('?acao=periodo_form&id=' . $id . '&errormsg=' . urlencode($errormsg)));
                exit();
            }

            // Se correr bem, volta para a lista pública
			header('Location: ' . app_url('?acao=periodo_list'));
			exit();
		}
		return false;
	}

	function nomePeriodo($periodo_id)
	{
		$periodo = new Periodo($this->db);
		$per = $periodo->abrir($periodo_id);		
		return $per[0]['nome'];
	}

	function abrir()
	{
		if (isset($_GET['id']) && is_numeric($_GET['id'])) {

			$id = Util::clearparam($_GET['id']);
			$periodo = new Periodo($this->db);
			return $periodo->abrir($id);
		}
	}

	// listagem
	function listarcontroller()
	{
		$periodo = new Periodo($this->db);
		return $periodo->listar();
	}
}
?>
