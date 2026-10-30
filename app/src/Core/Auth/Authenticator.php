<?php

namespace Salas\Core\Auth;

use Salas\Core\Auth\CredentialRepository;

class Authenticator
{
	private UserCredentialRepository $credentials;

	public function __construct(UserCredentialRepository $credentials)
	{
		$this->credentials = $credentials;
	}

	public function authenticate(string $identifier, string $password): ?array
	{
		$credential = $this->credentials->findByIdentifier($identifier);
		if ($credential === null) {
			return null;
		}

		if (
			!isset($credential['id'], $credential['password_hash'], $credential['identity'])
			|| !(is_int($credential['id']) || is_string($credential['id']))
			|| !is_string($credential['password_hash'])
			|| !is_array($credential['identity'])
		) {
			throw new \UnexpectedValueException('O repositório de credenciais devolveu um registo inválido.');
		}

		$storedHash = $credential['password_hash'];
		$passwordIsValid = password_verify($password, $storedHash);
		$isLegacyMd5 = strlen($storedHash) === 32 && hash_equals($storedHash, md5($password));

		if (!$passwordIsValid && !$isLegacyMd5) {
			return null;
		}

		if ($isLegacyMd5) {
			$this->credentials->updatePasswordHash(
				$credential['id'],
				password_hash($password, PASSWORD_DEFAULT)
			);
		}

		return $credential['identity'];
	}
}
