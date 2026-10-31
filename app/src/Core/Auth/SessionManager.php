<?php

namespace Salas\Core\Auth;


/**
 * Classe SessionManager.
 */
class SessionManager
{
	private const IDENTITY_KEY = 'auth_identity';
	private const ACTIVITY_KEY = 'auth_last_activity';

	/**
	 * Método start.
	 * @return void
	 */
	public function start(): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
	}

	/**
	 * Método identity.
	 * @return ?array
	 */
	public function identity(): ?array
	{
		$this->start();

		return isset($_SESSION[self::IDENTITY_KEY]) && is_array($_SESSION[self::IDENTITY_KEY])
			? $_SESSION[self::IDENTITY_KEY]
			: null;
	}

	/**
	 * Método login.
	 * @param array $identity
	 * @return void
	 */
	public function login(array $identity): void
	{
		$this->start();

		if (!session_regenerate_id(true)) {
			throw new \RuntimeException('Não foi possível renovar o identificador da sessão.');
		}

		$this->setIdentity($identity);
	}

	/**
	 * Método updateIdentity.
	 * @param array $identity
	 * @return void
	 */
	public function updateIdentity(array $identity): void
	{
		$this->setIdentity($identity);
	}

	/**
	 * Método touch.
	 * @return void
	 */
	public function touch(): void
	{
		$this->start();

		if ($this->identity() !== null) {
			$_SESSION[self::ACTIVITY_KEY] = time();
		}
	}

	/**
	 * Método lastActivity.
	 * @return ?int
	 */
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

	/**
	 * Método logout.
	 * @return void
	 */
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

    /**
     * Método get.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();

        return $_SESSION[$key] ?? $default;
    }

    /**
     * Método set.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }


	/**
	 * Método setIdentity.
	 * @param array $identity
	 * @return void
	 */
	private function setIdentity(array $identity): void
	{
		$this->start();
		$_SESSION[self::IDENTITY_KEY] = $identity;
		$_SESSION[self::ACTIVITY_KEY] = time();
	}
}
