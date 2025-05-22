<?php

require "util.php";
require "../model/usuario.php";
require "../Controller/permissoesController.php";

class UsuarioController
{

	function salvar()
	{
		if (isset($_POST['salvar']) || isset($_POST['validar'])) {

			$nome = Util::clearparam($_POST['nome']);
			$email = Util::clearparam($_POST['email']);
			$senha = Util::clearparam($_POST['senha']);

			$id = Util::clearparam($_POST['id']);


			if (isset($_POST['salvar'])) {
				$telefone = Util::clearparam(isset($_POST['telefone']) ? $_POST['telefone'] : '');
				$NIF = Util::clearparam(isset($_POST['NIF']) ? $_POST['NIF'] : null);
				$nivel = Util::clearparam((isset($_POST['nivel']) ? $_POST['nivel'] : ''));
			} else {
				$telefone = '';
				$NIF = '';
				$nivel = 0;
			}
			
			// se nao alterou a senha, nao salvar novamente pois está criptografada
			if (strlen($senha) == 32) {
				$senha = null;
			}

			$usuario = new Usuario();

			$resultado = $usuario->autenticar($email, $senha);
			if (isset($resultado[0]['id'])) {
				return 0;  // não pode criar um usuário que já está criado
			}

			// O nome é obrigatório
			if (strlen($nome) > 0) {				
				$resultado = $usuario->salvar($id, $nome, $email, $senha, $telefone, $NIF, $nivel);
			}

			// Se a gravação foi feita do ecrã de login
			// Ainda não está logado e não tem sessão -> nada a fazer
			if (isset($_POST['validar'])) {
				return $resultado;
			}
			
			if ($id == $_SESSION['user_id']) {
				// se alterou o nome do usuario logado, atualiza o nome na sessao
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
			$res = $usuario->excluir($id);
			
			if (is_numeric($res)) {
				header("Location: usuario_list.php");
				exit();
			} else {
				return $res;
			}
		}
		return false;
	}

	function cancelar()
	{
		if (isset($_POST['cancelar'])) {

			header("Location: usuario_list.php");
			exit();
		}
		return false;
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
		$permissoes = new PermissoesController();
		$usuario = new Usuario();


		$linhas = $usuario->listar();

		$tabela = '';
		foreach ($linhas as $linha) {
			$tabela .= '<tr>							
							<td><a href="usuario_form.php?id=' . $linha['id'] . '">' . $linha['nome'] . '</a></td>
							<td>' . $linha['email'] . '</td>
							<td>' . $permissoes->nomeNivel($linha['nivel'], 2) . '</td>
							<td>' . $linha['id'] . '</td>
							<td></td>
						</tr>';

		}
		return $tabela;
	}

	// função de autenticação de usuário
	function autenticarController()
	{
		if (isset($_POST['email_l']) && isset($_POST['senha_l'])) {
			
			if (!empty(trim($_POST['email_l']))) {

				$senha = md5($_POST['senha_l']);
				$email = Util::clearparam($_POST['email_l']);

				$usuario = new Usuario();
				$row = $usuario->autenticar($email, $senha);

				// encontrou usuario
				if (isset($row[0]['id'])) {

					$this->startSession($row[0]);
					header("Location: index.php"); // pagina inicial
					exit();
				} else {
					return 0; // usuario não encontrado
				}
			} else {
				return 0; // usuario não encontrado
			}

		} else {
			return 1; // nada a fazer
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