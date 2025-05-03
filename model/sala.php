<?php
require_once "db_mysqli.php";

class sala
{
 
	function listar($filtro='todas')
	{
		
		$db = new Database();
		
		$sql = ' select sala.*, categoria.nome as categoria 
				 from sala 
				 left join categoria on sala.categoria_id = categoria.id
				 where categoria.nome = "'.$filtro.'" or "'.$filtro.'" = "todas"
					order by sala.nome;' ;
		return $db->query($sql);
	}
	
	function abrir($id)
	{
		
		$db = new Database();
		
		$sql = ' select * from sala where id = '. $id; ;
		return $db->query($sql);

	}
	
	function salvar($id, $nome, $descricao)
	{
	
		$db = new Database();
		
		// inserir
		if($id == 0)
		{
			$sql = ' insert into sala ( nome, descricao ) values ("'.$nome.'","'. $descricao .'") ';
			return $db->query_insert($sql);
		}
		else
		{ 
			// atualizar
			$sql = ' update sala set nome = "'.$nome.'", descricao = "' . $descricao .'" where id = ' .$id;			
			return $db->query_update($sql);						

		}

	}
	
	function excluir($id)
	{
		$db = new Database();
		$sql = 'delete from sala where id = '.$id; 
		return $db->query_update($sql);
	}
	
	function total()
	{
		$db = new Database();
		$sql = 'select count(id) as total from sala  ';
		return $db->query($sql);
		
	}
	
}

?>