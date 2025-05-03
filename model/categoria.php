<?php
require_once "db_mysqli.php";

class categoria
{
 
	function listar()
	{		
		$db = new Database();
		
		$sql = ' select id, nome, descricao from categoria order by nome;' ;
		return $db->query($sql);
	}
	
	function abrir($id)
	{		
		$db = new Database();
		
		$sql = ' select * from categoria where id = '. $id; 
		return $db->query($sql);

	}
	
	function salvar($id, $nome, $descricao, $nome_img, $tamanho_img, $tipo_img, $conteudo )
	{	
		$db = new Database();
				
		if($id == 0)
		{			
			// inserir
			$sql = ' insert into categoria ( nome, descricao, nome_imagem, tamanho_imagem, tipo_imagem, imagem ) values ("'.$nome.'","'.$descricao.'", "'.$nome_img.'", "'.$tamanho_img.'", "'.$tipo_img.'", "'.$conteudo.'") ';
			$return = $db->query_insert($sql);			
		}
		else
		{ 
			// atualizar
			$sql = ' update categoria set nome = "'.$nome.'", 
										  descricao = "'.$descricao.'", 
										  nome_imagem = "'.$nome_img.'", 
										  tamanho_imagem = "'.$tamanho_img.'", 
										  tipo_imagem = "'.$tipo_img.'", 
										  imagem = "'.$conteudo.'" 
					                      where id = ' .$id;
			return $db->query_update($sql);
		}
	}
	
	function excluir($id)
	{
		$db = new Database();

		$sql = 'delete from categoria where id = '.$id; 
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