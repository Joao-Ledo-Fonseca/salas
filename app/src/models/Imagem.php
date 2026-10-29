<?php
namespace Salas\Models;

use Salas\Infrastructure\Persistence\Database;

class Imagem
{

	private $db = null;

	function __construct(Database $db)
	{
		$this->db = $db;
	}
	function abrir($id)
	{
		$sql = 'select * from imagem where id = ?';
		return $this->db->query($sql, array($id));
	}
	
	function listar()
	{
		$sql = 'select id, nome_imagem, tipo_imagem, imagem from imagem order by id desc';
		return $this->db->query($sql);
	}

	function listarPaginado($offset, $limit)
	{
		$sql = 'select id, nome_imagem, tipo_imagem, imagem from imagem order by id desc limit ? offset ?';
		return $this->db->query($sql, [$limit, $offset]);
	}

	function contar()
	{
		$sql = 'select count(*) as total from imagem';
		$rows = $this->db->query($sql);
		return isset($rows[0]['total']) ? (int) $rows[0]['total'] : 0;
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
