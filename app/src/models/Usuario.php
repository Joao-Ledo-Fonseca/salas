<?php
namespace Salas\Models;

use Salas\Infrastructure\Persistence\Database;

class Usuario 
{
	private $db = null;

	function __construct(Database $db)
	{
		$this->db = $db;
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


		$authenticator = new \Salas\Infrastructure\Auth\Authenticator(
			new \Salas\Infrastructure\Auth\SalasCredentialRepository($this)
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
