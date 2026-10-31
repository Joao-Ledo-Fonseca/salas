<?php

namespace Salas\Core\Auth;


/**
 * Classe SalasCredentialRepository.
 */
class SalasCredentialRepository implements CredentialRepository
{
	private \Salas\Models\Usuario $usuario;

	/**
	 * Método __construct.
	 * @param \Salas\Models\Usuario $usuario
	 * @return void
	 */
	public function __construct(\Salas\Models\Usuario $usuario)
	{
		$this->usuario = $usuario;
	}

	/**
	 * Método findByIdentifier.
	 * @param string $identifier
	 * @return ?array
	 */
	public function findByIdentifier(string $identifier): ?array
	{
		$rows = $this->usuario->findByLoginIdentifier($identifier);
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
	 * Método updatePasswordHash.
	 * @param int|string $id
	 * @param string $passwordHash
	 * @return int
	 */
	public function updatePasswordHash(int|string $id, string $passwordHash): int
	{
		return $this->usuario->updatePasswordHash($id, $passwordHash);
	}
}
