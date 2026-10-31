<?php
namespace Salas\Models;

use Salas\Core\Persistence\Database;

/**
 * Classe Imagem.
 */
class Imagem
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
	 * Método abrir.
	 * @param mixed $id
	 * @return mixed
	 */
	public function abrir($id)
	{
		$sql = 'select * from imagem where id = ?';
		return $this->db->query($sql, array($id));
	}
	
	/**
	 * Método listar.
	 * @return mixed
	 */
	public function listar()
	{
		$sql = 'select id, nome_imagem, tipo_imagem, imagem from imagem order by id desc';
		return $this->db->query($sql);
	}

	/**
	 * Método listarPaginado.
	 * @param mixed $offset
	 * @param mixed $limit
	 * @return mixed
	 */
	public function listarPaginado($offset, $limit)
	{
		$sql = 'select id, nome_imagem, tipo_imagem, imagem from imagem order by id desc limit ? offset ?';
		return $this->db->query($sql, [$limit, $offset]);
	}

	/**
	 * Método contar.
	 * @return mixed
	 */
	public function contar()
	{
		$sql = 'select count(*) as total from imagem';
		$rows = $this->db->query($sql);
		return isset($rows[0]['total']) ? (int) $rows[0]['total'] : 0;
	}

	/**
	 * Método salvar.
	 * @param mixed $id
	 * @param mixed $nome_img
	 * @param mixed $tamanho_img
	 * @param mixed $tipo_img
	 * @param mixed $conteudo
	 * @return mixed
	 */
	public function salvar($id, $nome_img, $tamanho_img, $tipo_img, $conteudo )
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
	
	/**
	 * Método excluir.
	 * @param mixed $id
	 * @return mixed
	 */
	public function excluir($id)
	{      
		$sql = 'delete from imagem where id = ?'; 
		return $this->db->query_update($sql, array($id));        
	}




}

?>
