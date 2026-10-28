<?php

namespace Salas\Integration;

use Salas\Auth\SessionManager;

require_once __DIR__ . '/../auth/SessionManager.php';

class SalasSessionAdapter
{
	private SessionManager $sessions;

	public function __construct(?SessionManager $sessions = null)
	{
		$this->sessions = $sessions ?? new SessionManager();
	}

	public function start(): void
	{
		$this->sessions->start();
	}

	public function isAuthenticated(): bool
	{
		$this->start();

		return $this->sessions->identity() !== null || isset($_SESSION['user_id']);
	}

	public function login(array $identity): void
	{
		$this->sessions->login($identity);
		$this->writeLegacyIdentity($identity);
	}

	public function updateIdentity(array $identity): void
	{
		$this->sessions->updateIdentity($identity);
		$this->writeLegacyIdentity($identity);
	}

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

	public function logout(): void
	{
		$this->sessions->logout();
	}

	private function writeLegacyIdentity(array $identity): void
	{
		$_SESSION['user_id'] = $identity['id'];
		$_SESSION['user_nome'] = $identity['nome'];
		$_SESSION['user_nivel'] = $identity['nivel'];
		$_SESSION['ultima_atividade'] = time();
	}

	private function legacyIdentity(): array
	{
		return [
			'id' => $_SESSION['user_id'],
			'nome' => $_SESSION['user_nome'] ?? '',
			'nivel' => $_SESSION['user_nivel'] ?? 0,
		];
	}
}
