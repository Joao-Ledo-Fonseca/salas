<?php
require_once "db_mysqli.php";

class sala
{
	private $db = null;

	function __construct()
	{
		$this->db = new Database();
	}

	function listar($filtro = 'todas')
	{
		
		$sql = ' select sala.*, categoria.nome as categoria from sala 
				 left join categoria on sala.categoria_id = categoria.id
				 where categoria.nome = "' . $filtro . '" or "' . $filtro . '" = "todas"
					order by categoria.nome, sala.nome;';
		return $this->db->query($sql);
	}

	function abrir($id)
	{		

		$sql = ' select * from sala 
					where id = ' . $id . ';';		
		return $this->db->query($sql);

	}

	function salvar($id, $nome, $descricao, $categoria_id, $imagem_id)	
	{
			
		if ($imagem_id == '') {
			$imagem_id = 'NULL';;
		}

		if ($id == 0) {
			// inserir
			$sql = ' insert into sala ( nome, descricao, categoria_id, imagem_id ) 
							values ( "' . $nome . '","' . $descricao . '", "' . $categoria_id . '", ' . $imagem_id . ') ';
							
			return $this->db->query_insert($sql);			
		} else {
			// atualizar
			$sql = ' update sala set nome = "' . $nome . '", descricao = "' . $descricao . '", categoria_id= "'. $categoria_id . '", imagem_id = ' . $imagem_id . ' where id = ' . $id;
			
			return $this->db->query_update($sql);
		}

	}

	function excluir($id)
	{	
		$sql = 'delete from sala where id = ' . $id;
		return $this->db->query_update($sql);
	}

	function total()
	{		
		$sql = 'select count(id) as total from sala  ';
		return $this->db->query($sql);

	}

}

?>