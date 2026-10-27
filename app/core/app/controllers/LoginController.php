<?php

require_once __DIR__ . '/../models/usuario.php';
require_once __DIR__ . '/../auth/Authenticator.php';
require_once __DIR__ . '/../integration/SalasCredentialRepository.php';
require_once __DIR__ . '/../integration/SalasSessionAdapter.php';
require_once __DIR__ . '/util.php';
require_once __DIR__ . '/usuarioController.php';

class LoginController
{
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
			$usuarioController = new UsuarioController();
			$result = $usuarioController->salvar($post);
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

		$usuario = new Usuario();
		$authenticator = new \Salas\Auth\Authenticator(
			new \Salas\Integration\SalasCredentialRepository($usuario)
		);
		$identity = $authenticator->authenticate(
			Util::clearparam($post['email_l']),
			$post['senha_l']
		);

		if ($identity === null) {
			return 'invalid';
		}

		(new \Salas\Integration\SalasSessionAdapter())->login($identity);

		return 'authenticated';
	}
}
