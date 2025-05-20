<?php

require_once "util.php";
require "../model/sala.php";

class salaController
{

	function salvar($categoria_filtro)
	{
		if (isset($_POST['salvar'])) {
			$id = Util::clearparam($_POST['id']);
			$nome = Util::clearparam($_POST['nome']);
			$descricao = Util::clearparam($_POST['descricao']);
			$lugares = Util::clearparam($_POST['lugares']);
			if (isset($_POST['activa'])) {
				$activa = Util::clearparam($_POST['activa']);
				$activa = ($_POST['activa'] == "activa" ? true : false);
			} else {
				$activa = false;
			}

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
				$sala->salvar($id, $nome, $descricao, $categoria_id, $lugares, $activa, $imagem_id);
			}

			$param = array("categoria_id"=>$categoria_filtro);
			Util::redirect_POST("sala_list.php", $param );
			//header("Location: sala_list.php?categoria_id=".$categoria_filtro);
			exit();
		}
		return false;
	}

	function excluir($categoria_filtro)
	{
		if (isset($_POST['excluir'])) {
			$id = Util::clearparam($_POST['id']);

			$sala = new sala();
			$erro = $sala->excluir($id);

			if ($erro) {
				$errormsg = 'Erro ao excluir a sala. Existem reservas.';

				header("Location: sala_form.php?id=" . $id . "&categoria_filtro=" . $categoria_filtro . "&errormsg=" . $errormsg);
				exit();
			}

			$post_data = array("categoria_id"=>$categoria_filtro);
			$url = "sala_list.php";
			
			Util::url_POST($url, $post_data);

			// header("Location: sala_list.php?categoria_id=" . $categoria_filtro);
			exit();
		}
		return false;
	}

	function cancelar($categoria_filtro)
	{
		if (isset($_POST['cancelar'])) {

			$post_data["categoria_id"] = $categoria_filtro ;			
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
	function listarController($filtro_categoria_id = 0 ,$filtro_activas='todas')
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
			<td><a href="sala_form.php?id=' . $linha['id'] . '&categoria_filtro='. $filtro_categoria_id.'">' . $linha['nome'] . '</a></td>
			<td>' . $linha['descricao'] . '</td>
			<td>' . ($linha['activa']?'&#10003;':'-') . '</td> 
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