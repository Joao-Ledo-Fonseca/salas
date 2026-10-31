<?php
namespace Salas\Models;

use Salas\Core\Persistence\Database;

/**
 * Classe Periodo.
 */
class Periodo
{
	private $db = null;

	/**
	 * Método __construct.
	 * @param Database $db
	 * @return void
	 */
	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	/**
	 * Método listar.
	 * @return mixed
	 */
	public function listar()
	{
		
		
		$sql = ' select * from periodo order by seq;' ;
		return $this->db->query($sql);
	}
	
	/**
	 * Método abrir.
	 * @param mixed $id
	 * @return mixed
	 */
	public function abrir($id)
	{
		$sql = 'select * from periodo where id = ?';
		return $this->db->query($sql, array($id));
	}
	
	/**
	 * Método salvar.
	 * @param mixed $id
	 * @param mixed $nome
	 * @param mixed $seq
	 * @return mixed
	 */
	public function salvar($id, $nome, $seq)
	{
		$seq = (int) $seq;

		// inserir
		if($id == 0)
		{
			$sql = 'insert into periodo (nome, seq) values (?, ?)';
			return $this->db->query_insert($sql, array($nome, $seq));
		}
		else
		{ 
			// atualizar
			$sql = 'update periodo set nome = ?, seq = ? where id = ?';
			return $this->db->query_update($sql, array($nome, $seq, $id));

		}
	}
	
	/**
	 * Método excluir.
	 * @param mixed $id
	 * @return mixed
	 */
	public function excluir($id)
	{
		$sql = 'delete from periodo where id = ?'; 
		return $this->db->query_update($sql, array($id));
	}
	
	
	/**
	 * Método name.
	 * @param mixed $id
	 * @return mixed
	 */
	public function name($id)
	{
		$rows = $this->abrir($id);
		return isset($rows[0]['nome']) ? (string) $rows[0]['nome'] : '';
	}

	/**
	 * Método total.
	 * @return mixed
	 */
	public function total()
	{
		
		$sql = 'select count(id) as total from periodo  ';
		return $this->db->query($sql);
		
	}




}

?>
