<?php
require_once "db_mysqli.php";

class imagem
{

	private $db = null;

	function __construct()
	{
		$this->db = new Database();
	}


    /*
	function listar()
	{		
		$db = new Database();
		
		$sql = ' select id, nome, descricao from categoria order by nome;' ;
		return $this->db->query($sql);
	}
	*/ 

	function abrir($id)
	{
		$sql = 'select * from imagem where id = ?';
		return $this->db->query($sql, array($id));
	}
	
	function salvar($id, $nome_img, $tamanho_img, $tipo_img, $conteudo )
	{			
				
		if($id == 0)
		{			
			// inserir
			$sql = 'insert into imagem (nome_imagem, tamanho_imagem, tipo_imagem, imagem) values (?, ?, ?, ?)';									
			return $this->db->query_insert($sql, array($nome_img, $tamanho_img, $tipo_img, $conteudo));
		}
		else
		{ 
			// atualizar			
			$sql = 'update imagem set nome_imagem = ?, tamanho_imagem = ?, tipo_imagem = ?, imagem = ? where id = ?';

			
			return $this->db->query_update($sql, array($nome_img, $tamanho_img, $tipo_img, $conteudo, $id));
		}
	}
	
	function excluir($id)
	{      
		$sql = 'delete from imagem where id = ?'; 
		return $this->db->query_update($sql, array($id));        
	}
	
}

?>