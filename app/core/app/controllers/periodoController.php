<?php

require_once "util.php";
require __DIR__. "/../models/periodo.php";

class periodoController
{

	function salvar()
	{
		if (isset($_POST['salvar'])) {
			$id = Util::clearparam($_POST['id']);
			$seq = Util::clearparam($_POST['seq']);
			$nome = Util::clearparam($_POST['nome']);


			if (strlen($nome) > 0) {
				$periodo = new Periodo();
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

			$periodo = new Periodo();
            
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
		$periodo = new Periodo();
		$per = $periodo->abrir($periodo_id);		
		return $per[0]['nome'];
	}

	function abrir()
	{
		if (isset($_GET['id']) && is_numeric($_GET['id'])) {

			$id = Util::clearparam($_GET['id']);
			$periodo = new Periodo();
			return $periodo->abrir($id);
		}
	}

	// listagem
	function listarcontroller()
	{
		$periodo = new Periodo();
		$linhas = $periodo->listar();

		$tabela = '';
		foreach ($linhas as $linha) {
			// 🎯 CORREÇÃO 4: Atualizar o link da tabela para a rota central
			$tabela .= '<tr><td><a href="' . htmlspecialchars(app_url('?acao=periodo_form&id=' . (int) $linha['id']), ENT_QUOTES, 'UTF-8') . '">' . $linha['nome'] . '</a></td>
							<td>' . $linha['seq'] . '</td>							
							<td>' . $linha['id'] . '</td>
							<td></td></tr>';
		}

		return $tabela;
	}
}
?>