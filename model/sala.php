<?php
require_once "db_mysqli.php";

class sala
{
	private $db = null;

	function __construct()
	{
		$this->db = new Database();
	}

	function listar($filtro_categorias = 0, $filtro_activas = 'todas')
	{
		switch ($filtro_activas) {
			case 'todas':
				$filtro_activas = 'true';
				break;
			case 'activas':
				$filtro_activas = 'sala.activa = true';
				break;
			default:
				$filtro_activas = 'sala.activa = false';
				break;
		}

		$sql = 'select sala.*, categoria.nome as categoria from sala 
			 left join categoria on sala.categoria_id = categoria.id
			 where (categoria.id = ? or ? = 0) and ' . $filtro_activas . ' order by categoria.nome, sala.nome;';
		return $this->db->query($sql, [$filtro_categorias, $filtro_categorias]);
	}

	function abrir($id)
	{
		$sql = 'select * from sala where id = ?';
		return $this->db->query($sql, [$id]);
	}

	function salvar($id, $nome, $descricao, $categoria_id, $lugares, $activa, $imagem_id)
	{
		if ($imagem_id === '') {
			$imagem_id = null;
		}

		$activa = $activa ? 1 : 0;

		if ($id == 0) {
			$sql = 'insert into sala (nome, descricao, categoria_id, lugares, activa, imagem_id) values (?, ?, ?, ?, ?, ?)';
			return $this->db->query_insert($sql, [$nome, $descricao, $categoria_id, $lugares, $activa, $imagem_id]);
		} else {
			$sql = 'update sala set nome = ?, descricao = ?, categoria_id = ?, lugares = ?, activa = ?, imagem_id = ? where id = ?';
			return $this->db->query_update($sql, [$nome, $descricao, $categoria_id, $lugares, $activa, $imagem_id, $id]);
		}
	}

	function excluir($id)
	{
		$sql = 'delete from sala where id = ?';
		return $this->db->query_update($sql, [$id]);
	}

	function total()
	{
		$sql = 'select count(id) as total from sala';
		return $this->db->query($sql);
	}
}

?>