<?php

require_once "util.php";
require "../model/periodo.php";

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
			header("Location: periodo_list.php");
			exit();
		}
		return false;
	}

	function cancelar()
	{
		if (isset($_POST['cancelar'])) {

			header("Location: periodo_list.php");
			exit();
		}
		return false;
	}


	function excluir()
	{

		if (isset($_POST['excluir'])) {
			$id = Util::clearparam($_POST['id']);

			$periodo = new Periodo();
			$periodo->excluir($id);

			header("Location: periodo_list.php");
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
			// 
			$tabela .= '<tr><td><a href="periodo_form.php?id=' . $linha['id'] . '">' . $linha['nome'] . '</a></td>
							<td>' . $linha['seq'] . '</td>							
							<td>' . $linha['id'] . '</td>
							<td></td></tr>';
		}

		return $tabela;
	}

}

?>