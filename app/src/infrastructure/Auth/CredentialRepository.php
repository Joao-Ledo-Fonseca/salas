<?php

namespace Salas\Infrastructure\Auth;


interface CredentialRepository
{
	/**
	 * Identity contains only non-credential fields safe to retain in a session.
	 *
	 * @return array{id: int|string, password_hash: string, identity: array}|null
	 */
	public function findByIdentifier(string $identifier): ?array;

	public function updatePasswordHash(int|string $id, string $passwordHash): void;
}
