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
			$categoria_id = Util::clearparam($_POST['categoria_id']);
			$imagem_id = Util::clearparam($_POST['imagem_id']);

			$img_file = $_FILES['imagem']['tmp_name'];

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
				$sala = new sala();
				$sala->salvar($id, $nome, $descricao, $categoria_id, $imagem_id);
			}

			header("Location: sala_list.php");
			exit();
		}
		return false;
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
		return false;
	}

	function cancelar()
	{
		if (isset($_POST['cancelar'])) {

			header("Location: sala_list.php");
			exit();
		}
		return false;
	}

	function abrir()
	{
		if (isset($_GET['id']) && is_numeric($_GET['id'])) {
			$sala = new sala();
			$sal = $sala->abrir($_GET['id']);

			$imagem_id = $sal[0]['imagem_id'];

			if ($imagem_id != NULL) {
				$imagem = new imagem();
				$img = $imagem->abrir($imagem_id);
			}
			if (!isset($img[0])) {
				$img[0] = array('id' => 0, 'nome_imagem' => '', 'tamanho_imagem' => '', 'tipo_imagem' => '', 'imagem' => '');
			}

			return array_merge($img[0], $sal[0]);
		}
	}

	// listagem
	function listarController($filtro = 'todas')
	{

		$sala = new sala();
		$linhas = $sala->listar($filtro);

		$tabela = '';
		$cat_activa = '';
		foreach ($linhas as $linha) {
			$categoria_display = ($cat_activa == $linha['categoria'] ? '' : $linha['categoria']);
			$cat_activa = $linha['categoria'];

			$tabela .= '<tr>
			<td style="border:none">' . $categoria_display . '</td>
			<td><a href="sala_form.php?id=' . $linha['id'] . '">' . $linha['nome'] . '</a></td>
			<td>' . $linha['descricao'] . '</td>
			<td>' . $linha['id'] . '</td>
			<td></td>
		</tr>';

		}

		return $tabela;

	}
}

?>