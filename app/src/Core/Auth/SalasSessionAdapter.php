<?php

namespace Salas\Core\Auth;




/**
 * Classe SalasSessionAdapter.
 */
class SalasSessionAdapter
{
	private SessionManager $sessions;

	/**
	 * Método __construct.
	 * @param ?SessionManager $sessions
	 * @return void
	 */
	public function __construct(?SessionManager $sessions = null)
	{
		$this->sessions = $sessions ?? new SessionManager();
	}

	/**
	 * Método start.
	 * @return void
	 */
	public function start(): void
	{
		$this->sessions->start();
	}

	/**
	 * Método isAuthenticated.
	 * @return bool
	 */
	public function isAuthenticated(): bool
	{
		$this->start();

		return $this->sessions->identity() !== null || isset($_SESSION['user_id']);
	}

	/**
	 * Método login.
	 * @param array $identity
	 * @return void
	 */
	public function login(array $identity): void
	{
		$this->sessions->login($identity);
		$this->writeLegacyIdentity($identity);
	}

	/**
	 * Método updateIdentity.
	 * @param array $identity
	 * @return void
	 */
	public function updateIdentity(array $identity): void
	{
		$this->sessions->updateIdentity($identity);
		$this->writeLegacyIdentity($identity);
	}

	/**
	 * Método expireIfInactive.
	 * @param int $timeout
	 * @return bool
	 */
	public function expireIfInactive(int $timeout): bool
	{
		$this->start();

		$legacyIdentity = isset($_SESSION['user_id']) ? $this->legacyIdentity() : null;
		$legacyLastActivity = $_SESSION['ultima_atividade'] ?? null;
		$wasAuthenticated = $this->sessions->identity() !== null || $legacyIdentity !== null;
		$expired = $this->sessions->expireIfInactive(
			$timeout,
			$legacyIdentity,
			is_numeric($legacyLastActivity) ? (int) $legacyLastActivity : null
		);

		if (!$expired && $wasAuthenticated) {
			$_SESSION['ultima_atividade'] = time();
		}

		return $expired;
	}

	/**
	 * Método logout.
	 * @return void
	 */
	public function logout(): void
	{
		$this->sessions->logout();
	}

	/**
	 * Método writeLegacyIdentity.
	 * @param array $identity
	 * @return void
	 */
	private function writeLegacyIdentity(array $identity): void
	{
		$_SESSION['user_id'] = $identity['id'];
		$_SESSION['user_nome'] = $identity['nome'];
		$_SESSION['user_nivel'] = $identity['nivel'];
		$_SESSION['ultima_atividade'] = time();
	}

	/**
	 * Método legacyIdentity.
	 * @return array
	 */
	private function legacyIdentity(): array
	{
		return [
			'id' => $_SESSION['user_id'],
			'nome' => $_SESSION['user_nome'] ?? '',
			'nivel' => $_SESSION['user_nivel'] ?? 0,
		];
	}
}
