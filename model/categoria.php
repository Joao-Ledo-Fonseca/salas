<?php
require_once "db_mysqli.php";

class categoria 
{

	private $db = null;

	function __construct()
	{
		$this->db = new Database();
	}

	function listar()
	{	

		$sql = ' select id, nome, descricao from categoria order by nome;';
		return $this->db->query($sql);
	}

	function abrir($id)
	{
		$sql = 'select * from categoria where categoria.id = ?';
		return $this->db->query($sql, array($id));
	}

	function salvar($id, $nome, $descricao, $imagem_id)
	{            

		if ($imagem_id === '') {
			$imagem_id = null;
		}            

		if ($id == 0) {
			// inserir            
			$sql = 'insert into categoria (nome, descricao, imagem_id) values (?, ?, ?)';
			return $this->db->query_insert($sql, array($nome, $descricao, $imagem_id));

		} else {
			// atualizar				  
			$sql = 'update categoria set nome = ?, descricao = ?, imagem_id = ? where id = ?';
			return $this->db->query_update($sql, array($nome, $descricao, $imagem_id, $id));
		}
	}

	function excluir($id)
	{
		$sql = 'delete from categoria where id = ?';
		return $this->db->query_update($sql, array($id));
	}

	function total()
	{
		
		$sql = 'select count(id) as total from categoria  ';
		return $this->db->query($sql);

	}

}

?>