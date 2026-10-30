<?php
namespace Salas\Core\Auth;

interface UserCredentialRepository
{
    /**
     * @return array{id: int|string, password_hash: string, identity: array}|null
     */
    public function findByIdentifier(string $identifier): ?array;

    public function updatePasswordHash(int|string $id, string $passwordHash): int;
}
?>