<?php
namespace Salas\Controllers;

use Salas\Application\Services\AuthService;
use Salas\Infrastructure\Persistence\Database;


class LoginController
{
	private Database $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	public function handle(array $query, array $post): array
	{
		$errormsg = '';
		$result = null;
		$redirect = null;

		if (isset($post['entrar'])) {
			$result = $this->authenticate($post);
			if ($result === 'authenticated') {
				$redirect = app_url();
			} elseif ($result === 'invalid') {
				$errormsg = 'E-mail ou senha inválidos';
			}
		} elseif (isset($post['validar'])) {
			$UsuarioController = new UsuarioController($this->db);
			$result = $UsuarioController->salvar($post);
			$errormsg = is_numeric($result)
				? 'Utilizador criado com sucesso!'
				: 'Nome ou email inválidos ou já existentes!';
		}

		return [
			'errormsg' => $errormsg,
			'validou' => isset($post['validar']) && is_numeric($result),
			'registar' => isset($query['registar'])
				|| (isset($post['validar']) && !is_numeric($result)),
			'retornou' => isset($post['cancelar']) || isset($post['OK']),
			'redirect' => $redirect,
		];
	}

	private function authenticate(array $post): string
	{
		if (
			!isset($post['email_l'], $post['senha_l'])
			|| !is_string($post['email_l'])
			|| !is_string($post['senha_l'])
			|| trim($post['email_l']) === ''
		) {
			return 'invalid';
		}

		$authService = new AuthService($this->db);
		$identity = $authService->authenticate($post['email_l'], $post['senha_l']);

		if ($identity === null) {
			return 'invalid';
		}

		$authService->login($identity);

		return 'authenticated';
	}
}
