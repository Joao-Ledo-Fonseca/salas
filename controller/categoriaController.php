<?php

require_once "util.php";
require "../model/categoria.php";

class categoriaController
{

	function salvar()
	{

		if (isset($_POST['salvar'])) {
			$nome = Util::clearparam($_POST['nome']);
			$descricao = Util::clearparam($_POST['descricao']);
			$id = Util::clearparam($_POST['id']);

			$imagem = $_FILES['imagem']['tmp_name'];

			// Verifica se o ficheiro foi enviado
			if ($imagem != "none") {

				//Verifica se o ficheiro é imagem
				if (getimagesize($imagem) !== false) {
					$tamanho_img = $_FILES['imagem']['size'];
					$tipo_img = $_FILES['imagem']['type'];
					$nome_img = $_FILES['imagem']['name'];

					//Lê o ficheiro uploaded
					$fp = fopen($imagem, "rb");
					$conteudo = fread($fp, $tamanho_img);
					$conteudo = addslashes($conteudo);
					fclose($fp);

				} else {
					// Se não for uma imagem, não salva
					$tamanho_img = '';
					$tipo_img = '';
					$nome_img = '';
					$imagem = '';
					$conteudo = '';
				}

				//
				if (strlen($nome) > 0) {

					$categoria = new categoria();
					$categoria->salvar($id, $nome, $descricao, $nome_img, $tamanho_img, $tipo_img, $conteudo);
				}

				header("Location: categoria_list.php");
				exit();
			}
		}
	}

	function excluir()
	{

		if (isset($_POST['excluir'])) {
			$id = Util::clearparam($_POST['id']);

			$categoria = new categoria();
			$categoria->excluir($id);

			header("Location: categoria_list.php");
			exit();

		}

	}

	function abrir()
	{

		if (isset($_GET['id']) && is_numeric($_GET['id'])) {

			$categoria = new categoria();

			return $categoria->abrir($_GET['id']);

		}
	}

	// listagem
	function listarController($tipo = 'tabela')
	{

		$categoria = new categoria();
		$linhas = $categoria->listar();

		if ($tipo == 'tabela') {

			$tabela = '';
			foreach ($linhas as $linha) {

				$tabela .= '<tr><td>' . $linha['id'] . '</td>
								<td><a href="categoria_form.php?id=' . $linha['id'] . '">' . $linha['nome'] . '</a></td>
								<td>' . $linha['descricao'] . '</td>
							</tr>';
			}
		} else {
			$tabela = array();
			foreach ($linhas as $linha) {
				$tabela[] = $linha['nome'];
			}
		}

		return $tabela;
	}


}

?>