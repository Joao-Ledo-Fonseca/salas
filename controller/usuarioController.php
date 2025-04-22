<?php

require "util.php";
require "../model/usuario.php";

class UsuarioController
{

	function salvar()
	{
		if (isset($_POST['salvar'])) {
			$nome = Util::clearparam($_POST['nome']);
			$email = Util::clearparam($_POST['email']);
			$senha = Util::clearparam($_POST['senha']);
			$nivel = Util::clearparam($_POST['nivel']);
			$id = Util::clearparam($_POST['id']);

			// se nao alterou a senha, nao salvar novamente pois está criptografada
			if (strlen($senha) == 32) {
				$senha = null;
			}

			if (strlen($nome) > 0) {
				$usuario = new Usuario();
				$usuario->salvar($id, $nome, $email, $senha, $nivel);
			}
			
			if ($id == $_SESSION['user_id']) {
				// se alterou o usuario logado, atualiza a sessao
				$this->startSession(array('id' => $id, 'nome' => $nome, 'nivel' => $nivel));
			} 											

			header("Location: usuario_list.php");
			exit();
		}
	}

	function excluir()
	{

		if (isset($_POST['excluir'])) {
			$id = Util::clearparam($_POST['id']);

			$usuario = new Usuario();
			$usuario->excluir($id);

			header("Location: usuario_list.php");
			exit();

		}
	}

	function abrir()
	{

		if (isset($_GET['id']) && is_numeric($_GET['id'])) {
			$usuario = new Usuario();
			return $usuario->abrir($_GET['id']);
		}

	}

	// listagem
	function listarcontroller()
	{

		$usuario = new Usuario();

		$linhas = $usuario->listar();

		$niv = array(0 => 'Usuário', 1 => 'Administrador', 2 => 'Super Administrador');

		$tabela = '';
		foreach ($linhas as $linha) {
			$tabela .= '<tr>
							<td>' . $linha['id'] . '</td>
							<td><a href="usuario_form.php?id=' . $linha['id'] . '">' . $linha['nome'] . '</a></td>
							<td>' . $linha['email'] . '</td>
							<td>' . $niv[$linha['nivel']] . '</td>
						</tr>';

		}

		return $tabela;

	}

	// função de autenticação de usuário
	function autenticarController()
	{
		if (isset($_POST['email']) && isset($_POST['senha'])) {

			$senha = md5($_POST['senha']);
			$email = Util::clearparam($_POST['email']);

			$usuario = new Usuario();
			$row = $usuario->autenticar($email, $senha);

			// encontrou usuario
			if (isset($row[0]['id'])) {
				$this->startSession($row[0]);
				header("Location: index.php"); // pagina inicial
				exit();
			} else {
				return "E-mail ou senha inválidos";
			}

		} else {
			return ''; // nada a fazer
		}
	}

	function startSession(array $r)
	{
		session_start();
		$_SESSION['user_id'] = $r['id'];
		$_SESSION['user_nome'] = $r['nome'];
		$_SESSION['user_nivel'] = $r['nivel'];

	}

}

?>