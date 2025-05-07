<?php
require_once "db_mysqli.php";

class categoria 
{

	function listar()
	{
		$db = new Database();

		$sql = ' select id, nome, descricao from categoria order by nome;';
		return $db->query($sql);
	}

	function abrir($id)
	{
		$db = new Database();

		$sql = ' select * from categoria				 
				 where categoria.id = ' . $id . ';';
		return $db->query($sql);
	}

	function salvar($id, $nome, $descricao, $imagem_id)
	{
		
		$db = new Database();

		if ($imagem_id == '') {
			$imagem_id = 'NULL';;
		}			

		if ($id == 0) {
			// inserir			
			$sql = ' insert into categoria ( nome, descricao, imagem_id )  						
			                        values ("' . $nome . '","' . $descricao . '", ' . $imagem_id . ') ';
			return $db->query_insert($sql);						 
		} else {
			// atualizar												  
			$sql = ' update categoria set nome = "' . $nome . '", descricao = "' . $descricao . '", imagem_id = ' . $imagem_id . ' where id = ' . $id;
			
			$resultado = $db->query_update($sql);			 

			// var_dump($imagem_id, $sql); 
			// var_dump($resultado);exit;

			return $resultado;
		}
	}

	function excluir($id)
	{
		$db = new Database();

		$sql = 'delete from categoria where id = ' . $id;
		return $db->query_update($sql);
	}

	function total()
	{
		$db = new Database();
		$sql = 'select count(id) as total from categoria  ';
		return $db->query($sql);

	}

}

?>