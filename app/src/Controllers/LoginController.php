<?php
namespace Salas\Controllers;

use Salas\Core\Auth\AuthContext;
use Salas\Core\Auth\Authenticator;
use Salas\Core\Auth\AuthorizationService;
use Salas\Core\Persistence\Database;
use Salas\Models\Usuario;
use Salas\Core\Validation\Validator;

/**
 * Classe LoginController.
 */
class LoginController
{
private Database $db;

/**
 * Método __construct.
 * @param Database $db
 * @return void
 */
public function __construct(Database $db)
{
$this->db = $db;
}

/**
 * Método handle.
 * @param array $query
 * @param array $post
 * @return array
 */
public function handle(array $query, array $post): array
{
$errormsg = '';
$result = null;
$redirect = null;
$authorization = new AuthorizationService($this->db);
$registoPublico = $authorization->registoPublicoAtivo();

if (isset($post['entrar'])) {
$result = $this->authenticate($post);
if ($result === 'authenticated') {
$redirect = app_url();
} elseif ($result === 'invalid') {
$errormsg = 'E-mail ou senha inválidos';
}
} elseif (isset($post['validar'])) {
if (!$registoPublico) {
	$errormsg = 'O registo público de novos utilizadores está desativado.';
} else {
	$UsuarioController = new UsuarioController($this->db);
	$result = $UsuarioController->salvar($post);
	$registoCriado = is_int($result) && $result > 0;
	$errormsg = $registoCriado
		? 'Utilizador criado com sucesso!'
		: 'Nome, e-mail ou senha inválidos ou já existentes!';
}
} elseif (!$registoPublico && isset($query['registar'])) {
$errormsg = 'O registo público de novos utilizadores está desativado.';
}

$registoCriado = isset($post['validar']) && is_int($result) && $result > 0;

return [
'errormsg' => $errormsg,
'validou' => $registoCriado,
'registoPublico' => $registoPublico,
'registar' => $registoPublico && (
isset($query['registar'])
|| (isset($post['validar']) && !$registoCriado)
),
'retornou' => isset($post['cancelar']) || isset($post['OK']),
'redirect' => $redirect,
];
}

/**
 * Método authenticate.
 * @param array $post
 * @return string
 */
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

$usuario = new Usuario($this->db);
$authenticator = new Authenticator($usuario);

$identity = $authenticator->authenticate(
Validator::string($post['email_l']),
$post['senha_l']
);

if ($identity === null) {
return 'invalid';
}

$auth = new AuthContext();
$auth->login($identity);

return 'authenticated';
}
}
