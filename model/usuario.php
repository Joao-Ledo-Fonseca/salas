<?php
require_once "db_mysqli.php";

class Usuario 
{
	private $db = null;

	function __construct()
	{
		$this->db = new Database();
		$this->ensureDefaultSuperAdmin();
	}

	private function ensureDefaultSuperAdmin()
	{
		$sql = 'select count(*) as total from usuario';
		$result = $this->db->query($sql);
		$total = isset($result[0]['total']) ? (int) $result[0]['total'] : 0;

		if ($total === 0) {
			$senhaHash = password_hash('sadmin', PASSWORD_DEFAULT);
			$sql = 'insert into usuario (nome, email, senha, telefone, NIF, nivel) values (?, ?, ?, ?, ?, ?)';
			$this->db->query_insert($sql, array('sadmin', 'sadmin', $senhaHash, '', '', 3));
		}
	}

	function autenticar($email, $senha = null)
	{
		$sql = 'select id, nome, nivel, email, senha from usuario where (email = ? or nome = ?)';
		$resultado = $this->db->query($sql, array($email, $email));

		if (is_null($senha)) {
			return $resultado;
		}

		if (isset($resultado[0]['senha'])) {
			$senhaArmazenada = $resultado[0]['senha'];
			$senhaValida = password_verify($senha, $senhaArmazenada);
			$senhaMd5 = strlen($senhaArmazenada) === 32 && md5($senha) === $senhaArmazenada;

			if ($senhaValida || $senhaMd5) {
				if ($senhaMd5) {
					$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
					$sql = 'update usuario set senha = ? where id = ?';
					$this->db->query_update($sql, array($senhaHash, $resultado[0]['id']));
				}

				return array(
					array(
						'id' => $resultado[0]['id'],
						'nome' => $resultado[0]['nome'],
						'nivel' => $resultado[0]['nivel'],
						'email' => $resultado[0]['email'],
					)
				);
			}
		}

		return array();
	}

	function listar()
	{   
		$sql = 'select * from usuario order by nivel desc;';
		return $this->db->query($sql);
	}

	function abrir($id)
	{   
		$sql = 'select * from usuario where id = ?';
		return $this->db->query($sql, array($id));
	}

	function salvar($id, $nome, $email, $senha, $telefone, $NIF, $nivel)
	{       
		if ($id == 0) {
			if (is_null($senha) || $senha === '') {
				return 0;
			}

			$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
			$sql = 'insert into usuario (nome, email, senha, telefone, NIF, nivel) values (?, ?, ?, ?, ?, ?)';
			return $this->db->query_insert($sql, array($nome, $email, $senhaHash, $telefone, $NIF, $nivel));
		}
		// atualizar
		else {
			if (!is_null($senha) && $senha !== '') {
				$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
				$sql = 'update usuario set nome = ?, email = ?, senha = ?, telefone = ?, NIF = ?, nivel = ? where id = ?';
				return $this->db->query_update($sql, array($nome, $email, $senhaHash, $telefone, $NIF, $nivel, $id));
			}
			$sql = 'update usuario set nome = ?, email = ?, telefone = ?, NIF = ?, nivel = ? where id = ?';
			return $this->db->query_update($sql, array($nome, $email, $telefone, $NIF, $nivel, $id));
		}
	}

	function excluir($id)
	{
		$sql = 'delete from usuario where id = ?';
		return $this->db->query_update($sql, array($id));
	}
}

?>