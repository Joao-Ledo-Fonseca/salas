<?php
namespace Salas\Core\Persistence;

use PDO;

use Salas\Config\AppConfig;

class Database
{
	private AppConfig $config;
	private $link = null;

	public function __construct(AppConfig $config)
	{
		$this->config = $config;
	}

	private function execute(string $sql, array $params = []): \PDOStatement
	{
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute($params);

		return $stmt;
	}

	function query($sql, $params = array())
	{
		return $this->execute($sql, $params)->fetchAll();
	}

	function query_insert($sql, $params = array())
	{
		$this->execute($sql, $params);

		return (int) $this->connection()->lastInsertId();
	}

	function query_update($sql, $params = array())
	{
		return $this->execute($sql, $params)->rowCount();
	}

	private function connection()
	{
		if ($this->link === null) {
			$dsn = sprintf(
				'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
				$this->config->dbHost(),
				$this->config->dbPort(),
				$this->config->dbName()
			);

			$this->link = new PDO($dsn, $this->config->dbUser(), $this->config->dbPassword(), [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			]);
		}

		return $this->link;
	}
}
?>
