<?php

require_once "util.php";
require "../model/sala.php";

class salaController
{

	function salvar()
	{
		if (isset($_POST['salvar'])) {
			$id = Util::clearparam($_POST['id']);
			$nome = Util::clearparam($_POST['nome']);
			$descricao = Util::clearparam($_POST['descricao']);

			if (strlen($nome) > 0) {
				$sala = new sala();
				$sala->salvar($id, $nome, $descricao);
			}

			header("Location: sala_list.php");
			exit();
		}
	}

	function excluir()
	{

		if (isset($_POST['excluir'])) {
			$id = Util::clearparam($_POST['id']);

			$sala = new sala();
			$sala->excluir($id);

			header("Location: sala_list.php");
			exit();

		}

	}

	function abrir()
	{

		if (isset($_GET['id']) && is_numeric($_GET['id'])) {
			$sala = new sala();
			return $sala->abrir($_GET['id']);
		}
	}

	// listagem
	function listarController($filtro = 'todas')
	{

		$sala = new sala();
		$linhas = $sala->listar($filtro);

		$tabela = '';
		foreach ($linhas as $linha) {
			$tabela .= '<tr>
							<td>' . $linha['id'] . '</td>
							<td><a href="sala_form.php?id=' . $linha['id'] . '">' . $linha['nome'] . '</a></td>
							<td>' . $linha['descricao'] . '</td>
							<td>' . $linha['categoria'] . '</td>
						</tr>		
							';
		}

		return $tabela;

	}
}

?>