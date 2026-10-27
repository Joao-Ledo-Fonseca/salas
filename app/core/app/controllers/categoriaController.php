<?php

require_once "util.php";
require __DIR__. "/../models/categoria.php";
require __DIR__. "/../models/imagem.php";

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

			header('Location: ' . app_url('?acao=categoria_list'));
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
        
        // 🎯 CORREÇÃO 1: Proteger contra o erro fatal do PHP moderno
        try {
            $erro = $categoria->excluir($id);
        } catch (PDOException $e) {
            // Se a base de dados bloquear devido a salas associadas, define o erro como true
            $erro = true; 
        }

        if (empty($erro)) {
            $imagem = new imagem();
            $imagem->excluir($imagem_id);
        }

        if ($erro) {
            $errormsg = 'Erro ao excluir a categoria! Existem salas na categoria.';

            // 🎯 CORREÇÃO 2: Atualizar para a rota pública e aplicar o urlencode
            header('Location: ' . app_url('?acao=categoria_form&id=' . $id . '&errormsg=' . urlencode($errormsg)));
            exit();
        }

        header('Location: ' . app_url('?acao=categoria_list'));
        exit();
    }
    return false;
}

	function cancelar()
	{
		if (isset($_POST['cancelar'])) {

			header('Location: ' . app_url('?acao=categoria_list'));
			exit();
		}
		return false;
	}

	function abrir()
	{
		if (isset($_GET['id']) && is_numeric($_GET['id'])) {

			$id = Util::clearparam($_GET['id']);
			$categoria = new categoria();
			$cat = $categoria->abrir($id);

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
	function listarController()
	{
		$categoria = new categoria();
		return $categoria->listar();
	}

	function listarOpcoesController()
	{
		$categoria = new categoria();
		return $categoria->listar();
	}

}

?>
