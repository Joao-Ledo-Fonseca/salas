<?php

require_once "util.php";
require "../model/categoria.php";
require "../model/imagem.php";

class categoriaController
{

	function salvar()
	{
		if (isset($_POST['salvar'])) {
			$id = Util::clearparam($_POST['id']);
			$nome = Util::clearparam($_POST['nome']);
			$descricao = Util::clearparam($_POST['descricao']);
			$imagem_id = Util::clearparam($_POST['imagem_id']);


			$img_file = $_FILES['imagem']['tmp_name'];

			// if ($img_file != "") {

			$imagem_lida = Util::imagemUpload($img_file);

			if (is_array($imagem_lida)) {
				$imagem = new imagem();

				// Se já existe imagem, exclui
				if ($imagem_id != 0 && $imagem_id != '') {
					$imagem->excluir($imagem_id);
				}

				// Zero no id da imagem, para inserir a nova imagem
				$imagem_id = 0;
				$imagem_id = $imagem->salvar($imagem_id, $imagem_lida['nome_img'], $imagem_lida['tamanho_img'], $imagem_lida['tipo_img'], $imagem_lida['conteudo']);

			}


			if (strlen($nome) > 0) {
				$categoria = new categoria();
				$categoria->salvar($id, $nome, $descricao, $imagem_id);
			}

			header("Location: categoria_list.php");
			exit();

		}
		return false;
	}

	function excluir()
	{

		if (isset($_POST['excluir'])) {
			$id = Util::clearparam($_POST['id']);
			$imagem_id = Util::clearparam($_POST['imagem_id']);

			$categoria = new categoria();
			$erro = $categoria->excluir($id);

			if (empty($erro)) {
				$imagem = new imagem();
				$imagem->excluir($imagem_id);
			}

			if ($erro) {
				$errormsg = 'Erro ao excluir a categoria!';

				header("Location: categoria_form.php?id=" . $id . "&errormsg=" . $errormsg);
				exit();
			}

			header("Location: categoria_list.php");
			exit();

		}
		return false;
	}

	function cancelar()
	{
		if (isset($_POST['cancelar'])) {

			header("Location: categoria_list.php");
			exit();
		}
		return false;
	}

	function abrir()
	{
		if (isset($_GET['id']) && is_numeric($_GET['id'])) {
			$categoria = new categoria();
			$cat = $categoria->abrir($_GET['id']);

			$imagem_id = $cat[0]['imagem_id'];

			if ($imagem_id != NULL) {
				$imagem = new imagem();
				$img = $imagem->abrir($imagem_id);
			}
			if (!isset($img[0])) {
				$img[0] = array('id' => 0, 'nome_imagem' => '', 'tamanho_imagem' => '', 'tipo_imagem' => '', 'imagem' => '');
			}

			return array_merge($img[0], $cat[0]);
		}
		return false;
	}

	// listagem
	function listarController($tipo = 'tabela')
	{

		$categoria = new categoria();
		$linhas = $categoria->listar();

		if ($tipo == 'tabela') {
			$categorias = '';
			foreach ($linhas as $linha) {

				$categorias .= '<tr>								
								<td><a href="categoria_form.php?id=' . $linha['id'] . '">' . $linha['nome'] . '</a></td>
								<td>' . $linha['descricao'] . '</td>
								<td>' . $linha['id'] . '</td>
								<td></td>
							</tr>';
			}
		} else {
			$categorias = $linhas;
		}

		return $categorias;
	}

	function optionsCategoria($todas = false, $selected_nome = '', $selected_id = '')
	{
		$categoria = new categoria();
		$linhas = $categoria->listar();

		$options = '';

		// Adiciona a opção "Todas" se necessário
		if ($todas) {
			$options .= '<option value="todas"' . ($selected_nome === "todas" ? " selected" : "") . '>Todas</option>';
		}

		// Gera as opções com base nas categorias
		foreach ($linhas as $linha) {
			$selected = ($linha['id'] == $selected_id || $linha['nome'] == $selected_nome) ? ' selected' : '';

			$value = $todas ? $linha['nome'] : $linha['id'];
			$options .= '<option value="' . $value . '"' . $selected . '>' . $linha['nome'] . '</option>';
		}

		return $options;
	}

}

?>