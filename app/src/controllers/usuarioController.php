<?php

require_once __DIR__ . '/util.php';
require_once __DIR__ . "/../models/usuario.php";
require_once __DIR__ . '/permissoesController.php';
require_once __DIR__ . '/../integration/SalasSessionAdapter.php';

class UsuarioController
	{
	private Database $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	function salvar(?array $post = null)
		{
		$post = $post ?? $_POST;
		if (isset($post['salvar']) || isset($post['validar'])) {

			$nome = Util::clearparam($post['nome']);
			$email = Util::clearparam($post['email']);
			$senha = isset($post['senha']) ? trim($post['senha']) : null;

			$id = Util::clearparam($post['id']);


			if (isset($post['salvar'])) {
				$telefone = Util::clearparam(isset($post['telefone']) ? $post['telefone'] : '');
				$NIF = Util::clearparam(isset($post['NIF']) ? $post['NIF'] : null);
				$nivel = Util::clearparam((isset($post['nivel']) ? $post['nivel'] : ''));
				}
			else {
				$telefone = '';
				$NIF = '';
				$nivel = 0;
				}

			if ($senha === '') {
				$senha = null;
				}

			$usuario = new Usuario($this->db);

			$resultado = $usuario->findByLoginIdentifier($email);
			if (isset($resultado[0]['id']) && $resultado[0]['id'] != $id) {
				return 0;  // não pode criar um usuário com email ou nome já existente
				}

			// O nome é obrigatório
			if (strlen($nome) > 0) {
				$resultado = $usuario->salvar($id, $nome, $email, $senha, $telefone, $NIF, $nivel);
				}

			// Se a gravação foi feita do ecrã de login
			// Ainda não está logado e não tem sessão -> nada a fazer
			if (isset($post['validar'])) {
				return $resultado;
				}

			if ($id == $_SESSION['user_id']) {
				// se alterou o nome do usuario logado, atualiza o nome na sessao
				$this->startSession(array('id' => $id, 'nome' => $nome, 'nivel' => $nivel));
				}

			header('Location: ' . app_url('?acao=usuario_list'));
			exit();

			}
		}

		function excluir()
	{
		if (isset($_POST['excluir'])) {
			$id = Util::clearparam($_POST['id']);

			$usuario = new Usuario($this->db);
			
			// CORREÇÃO 1: Try/Catch para capturar o erro fatal do PHP moderno
			try {
				$res = $usuario->excluir($id);
			} catch (PDOException $e) {
				// Se a base de dados bloquear devido a reservas associadas, 
				// definimos uma string de erro para simular o comportamento antigo
				$res = "Utilizador não pode ser excluído. Existem reservas em seu nome.";
			}

			if (is_numeric($res)) {
				// CORREÇÃO 2: Atualizar o redirecionamento para a nova rota pública
				header('Location: ' . app_url('?acao=usuario_list'));
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

			header('Location: ' . app_url('?acao=usuario_list'));
			exit();
			}
		return false;
		}

	function abrir()
		{

		if (isset($_GET['id']) && is_numeric($_GET['id'])) {
			$usuario = new Usuario($this->db);
			return $usuario->abrir($_GET['id']);
			}

		}

	// listagem
	function listarcontroller()
		{
		$usuario = new Usuario($this->db);
		return $usuario->listar();
		}

	function startSession(array $r)
		{
		$this->sessionAdapter()->updateIdentity($r);
		}

	// 🎯 NOVO MÉTODO CENTRALIZADO DE LOGOUT
	function logout()
		{
		$this->sessionAdapter()->logout();

		// 4. Redireciona com segurança para a rota pública do login
		header('Location: ' . app_url('?acao=login'));
		exit;
		}

	private function sessionAdapter(): \Salas\Integration\SalasSessionAdapter
		{
		return new \Salas\Integration\SalasSessionAdapter();
		}


	}
?>
