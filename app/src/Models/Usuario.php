<?php
namespace Salas\Models;

use Salas\Core\Persistence\Database;
use PDOException;

/**
 * Classe Usuario.
 */
class Usuario implements \Salas\Core\Auth\UserCredentialRepository 
{
	private $db = null;

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
	 * Método findByLoginIdentifier.
	 * @param string $identifier
	 * @return ?array
	 */
	public function findByLoginIdentifier(string $identifier): ?array
	{
		$sql = 'select id, nome, nivel, email, senha from usuario where (email = ? or nome = ?)';
		return $this->db->query($sql, array($identifier, $identifier));
	}

	public function existsByEmailOrName(string $email, string $name, int|string|null $excludeId = null): bool
	{
		$sql = 'select id from usuario where (email = ? or nome = ?)';
		$params = [$email, $name];

		if ($excludeId !== null && (int) $excludeId > 0) {
			$sql .= ' and id <> ?';
			$params[] = $excludeId;
		}

		return $this->db->query($sql, $params) !== [];
	}

	/**
	 * Método findByIdentifier.
	 * @param string $identifier
	 * @return ?array
	 */
	public function findByIdentifier(string $identifier): ?array
	{
		$rows = $this->findByLoginIdentifier($identifier);
		if (!isset($rows[0])) {
			return null;
		}

		$row = $rows[0];

		return [
			'id' => $row['id'],
			'password_hash' => $row['senha'],
			'identity' => [
				'id' => $row['id'],
				'nome' => $row['nome'],
				'nivel' => $row['nivel'],
				'email' => $row['email'],
			],
		];
	}

	/**
	 * Método autenticar.
	 * @param mixed $identifier
	 * @param mixed $password
	 * @return mixed
	 */
	public function autenticar($identifier, $password = null)
	{
		if (is_null($password)) {
			return $this->findByLoginIdentifier($identifier);
		}


		$authenticator = new \Salas\Core\Auth\Authenticator($this);
		$identity = $authenticator->authenticate((string) $identifier, (string) $password);

		return $identity === null ? [] : [$identity];
	}

	/**
	 * Método updatePasswordHash.
	 * @param int|string $id
	 * @param string $passwordHash
	 * @return int
	 */
	public function updatePasswordHash(int|string $id, string $passwordHash): int
	{
		$sql = 'update usuario set senha = ? where id = ?';
		return $this->db->query_update($sql, array($passwordHash, $id));
	}

	/**
	 * Método listar.
	 * @return mixed
	 */
	public function listar()
	{   
		$sql = 'select * from usuario order by nivel desc;';
		return $this->db->query($sql);
	}

	/**
	 * Método abrir.
	 * @param mixed $id
	 * @return mixed
	 */
	public function abrir($id)
	{   
		$sql = 'select * from usuario where id = ?';
		return $this->db->query($sql, array($id));
	}

	/**
	 * Método salvar.
	 * @param mixed $id
	 * @param mixed $nome
	 * @param mixed $email
	 * @param mixed $senha
	 * @param mixed $telefone
	 * @param mixed $NIF
	 * @param mixed $nivel
	 * @return mixed
	 */
	public function salvar($id, $nome, $email, $senha, $telefone, $NIF, $nivel)
	{       
		if ($id == 0) {
			if (is_null($senha) || $senha === '') {
				return 0;
			}

			$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
			$sql = 'insert into usuario (nome, email, senha, telefone, NIF, nivel) values (?, ?, ?, ?, ?, ?)';
			try {
				return $this->db->query_insert($sql, array($nome, $email, $senhaHash, $telefone, $NIF, $nivel));
			} catch (PDOException $e) {
				if ($this->isDuplicateKey($e)) {
					return 0;
				}
				throw $e;
			}
		}
		// atualizar
		else {
			if (!is_null($senha) && $senha !== '') {
				$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
				$sql = 'update usuario set nome = ?, email = ?, senha = ?, telefone = ?, NIF = ?, nivel = ? where id = ?';
				try {
					return $this->db->query_update($sql, array($nome, $email, $senhaHash, $telefone, $NIF, $nivel, $id));
				} catch (PDOException $e) {
					if ($this->isDuplicateKey($e)) {
						return 0;
					}
					throw $e;
				}
			}
			$sql = 'update usuario set nome = ?, email = ?, telefone = ?, NIF = ?, nivel = ? where id = ?';
			try {
				return $this->db->query_update($sql, array($nome, $email, $telefone, $NIF, $nivel, $id));
			} catch (PDOException $e) {
				if ($this->isDuplicateKey($e)) {
					return 0;
				}
				throw $e;
			}
		}
	}

	private function isDuplicateKey(PDOException $exception): bool
	{
		return (int) ($exception->errorInfo[1] ?? 0) === 1062;
	}

	/**
	 * Método excluir.
	 * @param mixed $id
	 * @return mixed
	 */
	public function excluir($id)
	{
		$sql = 'delete from usuario where id = ?';
		return $this->db->query_update($sql, array($id));
	}

	/**
	 * Método seedSuperAdmin.
	 * @param Database $db
	 * @return void
	 */
	public static function seedSuperAdmin(Database $db): void
	{
		$result = $db->query('select count(*) as total from usuario');
		$total = isset($result[0]['total']) ? (int) $result[0]['total'] : 0;
		if ($total === 0) {
			$senhaHash = password_hash('sadmin', PASSWORD_DEFAULT);
			$db->query_insert(
				'insert into usuario (nome, email, senha, telefone, NIF, nivel) values (?, ?, ?, ?, ?, ?)',
				['sadmin', 'sadmin', $senhaHash, '', '', 3]
			);
		}
	}




}

?>
