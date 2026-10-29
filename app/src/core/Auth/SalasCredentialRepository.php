<?php

namespace Salas\Core\Auth;


class SalasCredentialRepository implements CredentialRepository
{
	private \Salas\Models\Usuario $usuario;

	public function __construct(\Salas\Models\Usuario $usuario)
	{
		$this->usuario = $usuario;
	}

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

	public function updatePasswordHash(int|string $id, string $passwordHash): void
	{
		$this->usuario->updatePasswordHash($id, $passwordHash);
	}
}
