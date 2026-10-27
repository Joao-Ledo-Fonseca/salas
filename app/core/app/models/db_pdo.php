<?php

class Database
{
	private PDO $link;

	function __construct()
	{
		global $cfg;

		$dsn = sprintf(
			'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
			$cfg->db_host,
			$cfg->db_porta,
			$cfg->db_banco
		);

		$this->link = new PDO($dsn, $cfg->db_user, $cfg->db_senha, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		]);
	}

	private function execute(string $sql, array $params = []): PDOStatement
	{
		$stmt = $this->link->prepare($sql);
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

		return (int) $this->link->lastInsertId();
	}

	function query_update($sql, $params = array())
	{
		return $this->execute($sql, $params)->rowCount();
	}
}
?>