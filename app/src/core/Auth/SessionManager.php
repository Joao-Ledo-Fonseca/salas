<?php

namespace Salas\Core\Auth;


class SessionManager
{
	private const IDENTITY_KEY = 'auth_identity';
	private const ACTIVITY_KEY = 'auth_last_activity';

	public function start(): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
	}

	public function identity(): ?array
	{
		$this->start();

		return isset($_SESSION[self::IDENTITY_KEY]) && is_array($_SESSION[self::IDENTITY_KEY])
			? $_SESSION[self::IDENTITY_KEY]
			: null;
	}

	public function login(array $identity): void
	{
		$this->start();

		if (!session_regenerate_id(true)) {
			throw new \RuntimeException('Não foi possível renovar o identificador da sessão.');
		}

		$this->setIdentity($identity);
	}

	public function updateIdentity(array $identity): void
	{
		$this->setIdentity($identity);
	}

	public function touch(): void
	{
		$this->start();

		if ($this->identity() !== null) {
			$_SESSION[self::ACTIVITY_KEY] = time();
		}
	}

	public function lastActivity(): ?int
	{
		$this->start();
		$lastActivity = $_SESSION[self::ACTIVITY_KEY] ?? null;

		return is_numeric($lastActivity) ? (int) $lastActivity : null;
	}

	public function expireIfInactive(
		int $timeout,
		?array $legacyIdentity = null,
		?int $legacyLastActivity = null
	): bool {
		$identity = $this->identity();
		$isAuthenticated = $identity !== null || $legacyIdentity !== null;
		$lastActivity = $legacyLastActivity ?? $this->lastActivity();

		if ($isAuthenticated && $lastActivity !== null && time() - $lastActivity > $timeout) {
			$this->logout();
			return true;
		}

		if ($isAuthenticated) {
			if ($identity === null && $legacyIdentity !== null) {
				$this->updateIdentity($legacyIdentity);
			}

			$this->touch();
		}

		return false;
	}

	public function logout(): void
	{
		$this->start();
		session_unset();

		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly']
			);
		}

		session_destroy();
	}

	private function setIdentity(array $identity): void
	{
		$this->start();
		$_SESSION[self::IDENTITY_KEY] = $identity;
		$_SESSION[self::ACTIVITY_KEY] = time();
	}
}
