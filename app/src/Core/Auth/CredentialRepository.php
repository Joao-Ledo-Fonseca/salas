<?php

namespace Salas\Core\Auth;


/**
 * Interface CredentialRepository.
 */
interface CredentialRepository
{
	/**
	 * Identity contains only non-credential fields safe to retain in a session.
	 *
	 * @return array{id: int|string, password_hash: string, identity: array}|null
	 */
	public function findByIdentifier(string $identifier): ?array;

	/**
	 * Método updatePasswordHash.
	 * @param int|string $id
	 * @param string $passwordHash
	 * @return int
	 */
	public function updatePasswordHash(int|string $id, string $passwordHash): int;
}
