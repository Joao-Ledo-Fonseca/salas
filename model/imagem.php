<?php
require_once "db_mysqli.php";

class imagem
{
    /*
	function listar()
	{		
		$db = new Database();
		
		$sql = ' select id, nome, descricao from categoria order by nome;' ;
		return $db->query($sql);
	}
	*/ 

	function abrir($id)
	{		
		$db = new Database();
		
		$sql = ' select * from imagem 				 				 
				 where id = '. $id . ';'; 

        return $db->query($sql);

	}
	
	function salvar($id, $nome_img, $tamanho_img, $tipo_img, $conteudo )
	{	
		$db = new Database();
				
		if($id == 0)
		{			
			// inserir
			$sql = ' insert into imagem ( nome_imagem, tamanho_imagem, tipo_imagem, imagem ) 
			                     values ("'.$nome_img.'", "'.$tamanho_img.'", "'.$tipo_img.'", "'.$conteudo.'") ';									
			return $db->query_insert($sql);			
		}
		else
		{ 
			// atualizar			
			$sql = ' update imagem set nome_imagem = "'.$nome_img.'", 
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

		$sql = 'delete from imagem where id = '.$id; 
		return $db->query_update($sql);

        
        
	}
	
}

?>