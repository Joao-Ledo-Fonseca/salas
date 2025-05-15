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

		$sql = ' select * from categoria				 
				 where categoria.id = ' . $id . ';';
		return $this->db->query($sql);
	}

	function salvar($id, $nome, $descricao, $imagem_id)
	{			

		if ($imagem_id == '') {
			$imagem_id = 'NULL';;
		}			

		if ($id == 0) {
			// inserir			
			$sql = ' insert into categoria ( nome, descricao, imagem_id ) 
						values ("' . $nome . '","' . $descricao . '", ' . $imagem_id . ') ';
			return $this->db->query_insert($sql);						 

		} else {
			// atualizar												  
			$sql = ' update categoria set nome = "' . $nome . '", descricao = "' . $descricao . '", imagem_id = ' . $imagem_id . ' where id = ' . $id;			
			
			return $this->db->query_update($sql);;
		}
	}

	function excluir($id)
	{

		$sql = 'delete from categoria where id = ' . $id;
		return $this->db->query_update($sql);
	}

	function total()
	{
		
		$sql = 'select count(id) as total from categoria  ';
		return $this->db->query($sql);

	}

}

?>