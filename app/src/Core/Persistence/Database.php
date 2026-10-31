<?php
namespace Salas\Core\Persistence;

use PDO;

use Salas\Config\AppConfig;

/**
 * Classe Database.
 */
class Database
{
	private AppConfig $config;
	private $link = null;

	/**
	 * Método __construct.
	 * @param AppConfig $config
	 * @return void
	 */
	public function __construct(AppConfig $config)
	{
		$this->config = $config;
	}

	/**
	 * Método execute.
	 * @param string $sql
	 * @param array $params
	 * @return \PDOStatement
	 */
	private function execute(string $sql, array $params = []): \PDOStatement
	{
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute($params);

		return $stmt;
	}

	/**
	 * Método query.
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function query(string $sql, array $params = []): array
	{
		return $this->execute($sql, $params)->fetchAll();
	}

	/**
	 * Método query_insert.
	 * @param string $sql
	 * @param array $params
	 * @return int
	 */
	public function query_insert(string $sql, array $params = []): int
	{
		$this->execute($sql, $params);

		return (int) $this->connection()->lastInsertId();
	}

	/**
	 * Método query_update.
	 * @param string $sql
	 * @param array $params
	 * @return int
	 */
	public function query_update(string $sql, array $params = []): int
	{
		return $this->execute($sql, $params)->rowCount();
	}

	/**
	 * Método connection.
	 * @return mixed
	 */
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
    /**
     * Método beginTransaction.
     * @return void
     */
    public function beginTransaction(): void
    {
        $this->connection()->beginTransaction();
    }

    /**
     * Método rollBack.
     * @return void
     */
    public function rollBack(): void
    {
        if ($this->connection()->inTransaction()) {
            $this->connection()->rollBack();
        }
    }


}
?>
