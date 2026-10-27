<?php
require_once __DIR__ . "/db_pdo.php";

class Usuario 
{
	private $db = null;

	function __construct()
	{
		$this->db = new Database();
		$this->ensureDefaultSuperAdmin();
	}

	private function ensureDefaultSuperAdmin()
	{
		$sql = 'select count(*) as total from usuario';
		$result = $this->db->query($sql);
		$total = isset($result[0]['total']) ? (int) $result[0]['total'] : 0;

		if ($total === 0) {
			$senhaHash = password_hash('sadmin', PASSWORD_DEFAULT);
			$sql = 'insert into usuario (nome, email, senha, telefone, NIF, nivel) values (?, ?, ?, ?, ?, ?)';
			$this->db->query_insert($sql, array('sadmin', 'sadmin', $senhaHash, '', '', 3));
		}
	}

	function findByLoginIdentifier($identifier)
	{
		$sql = 'select id, nome, nivel, email, senha from usuario where (email = ? or nome = ?)';
		return $this->db->query($sql, array($identifier, $identifier));
	}

	function autenticar($identifier, $password = null)
	{
		if (is_null($password)) {
			return $this->findByLoginIdentifier($identifier);
		}

		require_once __DIR__ . '/../auth/Authenticator.php';
		require_once __DIR__ . '/../integration/SalasCredentialRepository.php';

		$authenticator = new \Salas\Auth\Authenticator(
			new \Salas\Integration\SalasCredentialRepository($this)
		);
		$identity = $authenticator->authenticate((string) $identifier, (string) $password);

		return $identity === null ? [] : [$identity];
	}

	function updatePasswordHash($id, $passwordHash)
	{
		$sql = 'update usuario set senha = ? where id = ?';
		return $this->db->query_update($sql, array($passwordHash, $id));
	}

	function listar()
	{   
		$sql = 'select * from usuario order by nivel desc;';
		return $this->db->query($sql);
	}

	function abrir($id)
	{   
		$sql = 'select * from usuario where id = ?';
		return $this->db->query($sql, array($id));
	}

	function salvar($id, $nome, $email, $senha, $telefone, $NIF, $nivel)
	{       
		if ($id == 0) {
			if (is_null($senha) || $senha === '') {
				return 0;
			}

			$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
			$sql = 'insert into usuario (nome, email, senha, telefone, NIF, nivel) values (?, ?, ?, ?, ?, ?)';
			return $this->db->query_insert($sql, array($nome, $email, $senhaHash, $telefone, $NIF, $nivel));
		}
		// atualizar
		else {
			if (!is_null($senha) && $senha !== '') {
				$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
				$sql = 'update usuario set nome = ?, email = ?, senha = ?, telefone = ?, NIF = ?, nivel = ? where id = ?';
				return $this->db->query_update($sql, array($nome, $email, $senhaHash, $telefone, $NIF, $nivel, $id));
			}
			$sql = 'update usuario set nome = ?, email = ?, telefone = ?, NIF = ?, nivel = ? where id = ?';
			return $this->db->query_update($sql, array($nome, $email, $telefone, $NIF, $nivel, $id));
		}
	}

	function excluir($id)
	{
		$sql = 'delete from usuario where id = ?';
		return $this->db->query_update($sql, array($id));
	}
}

?>