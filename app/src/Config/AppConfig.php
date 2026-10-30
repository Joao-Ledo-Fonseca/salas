<?php
namespace Salas\Config;

final class AppConfig
{
	private string $dbHost;
	private string $dbUser;
	private string $dbPassword;
	private string $dbName;
	private int $dbPort;
	private string $urlBasePath;
	private ?string $theme;

	private function __construct(
		string $dbHost,
		string $dbUser,
		string $dbPassword,
		string $dbName,
		int $dbPort,
		string $urlBasePath,
		?string $theme = null
	) {
		$this->dbHost = $dbHost;
		$this->dbUser = $dbUser;
		$this->dbPassword = $dbPassword;
		$this->dbName = $dbName;
		$this->dbPort = $dbPort;
		$this->urlBasePath = $urlBasePath;
		$this->theme = $theme;
	}

	public function dbHost(): string { return $this->dbHost; }
	public function dbUser(): string { return $this->dbUser; }
	public function dbPassword(): string { return $this->dbPassword; }
	public function dbName(): string { return $this->dbName; }
	public function dbPort(): int { return $this->dbPort; }
	public function urlBasePath(): string { return $this->urlBasePath; }
	public function theme(): ?string { return $this->theme; }

	public static function fromFile(string $path, string $scriptName): self
	{
		// Carregar .env se existir (no mesmo diretório do config.json)
		$envPath = dirname($path) . DIRECTORY_SEPARATOR . '.env';
		if (is_file($envPath)) {
			$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			foreach ($lines as $line) {
				$line = trim($line);
				if ($line === '' || str_starts_with($line, '#')) continue;
				if (!str_contains($line, '=')) continue;
				[$k, $v] = explode('=', $line, 2);
				$k = trim($k); $v = trim($v);
				if ($k !== '' && getenv($k) === false) {
					putenv($k . '=' . $v);
					$_ENV[$k] = $v;
				}
			}
		}
		if (!is_file($path)) {
			throw new \RuntimeException('O ficheiro de configuração da aplicação não existe.');
		}

		$json = file_get_contents($path);
		if ($json === false) {
			throw new \RuntimeException('Não foi possível ler o ficheiro de configuração da aplicação.');
		}

		try {
			$settings = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $exception) {
			throw new \RuntimeException('O ficheiro de configuração da aplicação contém JSON inválido.', 0, $exception);
		}

		if (!$settings instanceof \stdClass) {
			throw new \RuntimeException('O ficheiro de configuração da aplicação tem de conter um objeto JSON.');
		}

		$envMap = ['db_host' => 'DB_HOST', 'db_user' => 'DB_USER', 'db_senha' => 'DB_PASSWORD', 'db_banco' => 'DB_NAME'];
		$requiredStrings = [
			'db_host' => 'dbHost',
			'db_user' => 'dbUser',
			'db_senha' => 'dbPassword',
			'db_banco' => 'dbName',
		];
		$values = [];
		foreach ($requiredStrings as $key => $property) {
			$envVal = getenv($envMap[$key]);
			if (is_string($envVal) && $envVal !== '') {
				$values[$property] = $envVal;
				continue;
			}
			if (!isset($settings->$key) || !is_string($settings->$key) || $settings->$key === '') {
				throw new \RuntimeException('Falta uma configuração de base de dados válida: ' . $key . '.');
			}
			$values[$property] = $settings->$key;
		}

		// db_porta via env
		$envPort = getenv('DB_PORT');
		if (is_string($envPort) && $envPort !== '' && ctype_digit($envPort)) {
			$settings->db_porta = (int) $envPort;
		}
		// url_base_path via env
		$envBase = getenv('APP_URL_BASE');
		if (is_string($envBase) && $envBase !== '') {
			$settings->url_base_path = $envBase;
		}

		if (!isset($settings->db_porta) || !is_int($settings->db_porta) || $settings->db_porta < 1 || $settings->db_porta > 65535) {
			throw new \RuntimeException('A configuração db_porta tem de ser um inteiro entre 1 e 65535.');
		}

		$urlBasePath = $settings->url_base_path ?? 'auto';
		if (!is_string($urlBasePath)) {
			throw new \RuntimeException('A configuração url_base_path tem de ser uma string.');
		}

		if ($urlBasePath === 'auto') {
			$scriptDirectory = str_replace('\\', '/', dirname($scriptName));
			$urlBasePath = $scriptDirectory === '/' ? '' : rtrim($scriptDirectory, '/');
		} elseif ($urlBasePath === '/') {
			$urlBasePath = '';
		} elseif ($urlBasePath !== '') {
			$segments = explode('/', trim($urlBasePath, '/'));
			if (
				$urlBasePath[0] !== '/'
				|| str_contains($urlBasePath, '?')
				|| str_contains($urlBasePath, '#')
				|| in_array('.', $segments, true)
				|| in_array('..', $segments, true)
			) {
				throw new \RuntimeException('A configuração url_base_path tem de ser vazia ou um caminho URL absoluto válido.');
			}
			$urlBasePath = '/' . implode('/', $segments);
		}

		$envTheme = getenv('APP_THEME');
		if (is_string($envTheme) && $envTheme !== '') {
			$settings->theme = $envTheme;
		}

		$theme = null;
		if (isset($settings->theme)) {
			if (!is_string($settings->theme) || $settings->theme === '') {
				throw new \RuntimeException('A configuração theme tem de ser uma string não vazia.');
			}
			$theme = $settings->theme;
		}

		return new self(
			$values['dbHost'],
			$values['dbUser'],
			$values['dbPassword'],
			$values['dbName'],
			$settings->db_porta,
			$urlBasePath,
			$theme
		);
	}
}
