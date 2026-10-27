<?php

require_once "util.php";
require __DIR__ . "/../models/sala.php";

class salaController
	{

	function salvar($categoria_filtro, $activas_filtro)
		{
		if (isset($_POST['salvar'])) {
			$id = Util::clearparam($_POST['id']);
			$nome = Util::clearparam($_POST['nome']);
			$descricao = Util::clearparam($_POST['descricao']);
			$lugares = Util::clearparam($_POST['lugares']);
			$categoria_id = Util::clearparam($_POST['categoria_id']);

			$activa = (isset($_POST['activa']));  //converte para bool			

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
				$sala->salvar($id, $nome, $descricao, $categoria_id, $lugares, $activa, $imagem_id);
				}

			$param["categoria_id"] = $categoria_filtro;
			$param["activas"] = $activas_filtro;

			Util::redirect_POST("sala_list.php", $param);

			//header("Location: sala_list.php?categoria_id=".$categoria_filtro);
			exit();
			}
		return false;
		}

	function excluir($categoria_filtro, $activas_filtro)
		{
		if (isset($_POST['excluir'])) {
			$id = Util::clearparam($_POST['id']);

			$sala = new sala();

			// 🎯 ADICIONE O TRY/CATCH AQUI para capturar o erro do PHP moderno
			try {
				$erro = $sala->excluir($id);
				}
			catch (PDOException $e) {
				// Se a base de dados bloquear devido às chaves estrangeiras, definimos o erro como true
				$erro = true;
				}

			if ($erro) {
				$errormsg = 'Erro ao excluir a sala. Existem reservas.';

				header('Location: ' . app_url('?acao=sala_form&id=' . $id . '&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&errormsg=' . urlencode($errormsg)));
				exit();
				}

			$post_data = array("categoria_id" => $categoria_filtro);
			$post_data["activas"] = $activas_filtro;

			// 🎯 NOTA: Garanta que usa o redirect_POST que corrigimos na classe Util
			$url = "sala_list.php";
			Util::redirect_POST($url, $post_data);

			exit();
			}
		return false;
		}

	function cancelar($categoria_filtro, $activas_filtro)
		{
		if (isset($_POST['cancelar'])) {

			$post_data["categoria_id"] = $categoria_filtro;
			$post_data["activas"] = $activas_filtro;
			$url = "sala_list.php";

			Util::redirect_POST($url, $post_data);
			// header("Location: sala_list.php?categoria_id=" . $categoria_filtro);
			exit();
			}
		return false;
		}

	function nomeSala($sala_id)
		{
		$sala = new sala();
		$sal = $sala->abrir($sala_id);
		return $sal[0]['nome'];
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
	function listarController($filtro_categoria_id = 0, $filtro_activas = 'activas')
		{

		$sala = new sala();
		$linhas = $sala->listar($filtro_categoria_id, $filtro_activas);

		$tabela = '';
		$cat_label = '';
		foreach ($linhas as $linha) {
			$categoria_display = ($cat_label == $linha['categoria'] ? '' : $linha['categoria']);
			$cat_label = $linha['categoria'];

			$tabela .= '<tr>
			<td style="border:none">' . $categoria_display . '</td>
			<td><a href="' . htmlspecialchars(app_url('?acao=sala_form&id=' . (int) $linha['id'] . '&categoria_filtro=' . rawurlencode((string) $filtro_categoria_id) . '&activas=' . rawurlencode((string) $filtro_activas)), ENT_QUOTES, 'UTF-8') . '"  >' . $linha['nome'] . '</a></td>
			<td>' . $linha['descricao'] . '</td>
			<td>' . ($linha['activa'] ? '&#10003;' : '-') . '</td> 
			<td>' . $linha['id'] . '</td>			
			<td></td>
		</tr>';
			// &#10003;
			// &#10005;
			}



		return $tabela;

		}


	}

?>