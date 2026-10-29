<?php
namespace Salas\Models;

use Salas\Core\Persistence\Database;

class Sala
{
	private $db = null;

	function __construct(Database $db)
	{
		$this->db = $db;
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
		$rows = $this->db->query('select * from sala where id = ?', [$id]);
		if (!isset($rows[0])) {
			return [];
		}
		$imageId = $rows[0]['imagem_id'] ?? null;
		$image = ['id' => 0, 'nome_imagem' => '', 'tamanho_imagem' => '', 'tipo_imagem' => '', 'imagem' => ''];
		if ($imageId !== null) {
			$found = (new \Salas\Models\Imagem($this->db))->abrir((int) $imageId);
			if (isset($found[0])) {
				$image = $found[0];
			}
		}
		return [array_merge($image, $rows[0])];
	}

	function salvar($id, $nome, $descricao, $categoria_id, $lugares, $activa, $imagem_id, $uploadedImage = false)
	{
		if (is_array($uploadedImage)) {
			if ($imagem_id !== 0 && $imagem_id !== '' && $imagem_id !== null) {
				(new \Salas\Models\Imagem($this->db))->excluir((int) $imagem_id);
			}
			$imagem_id = (new \Salas\Models\Imagem($this->db))->salvar(
				0,
				$uploadedImage['nome_img'],
				$uploadedImage['tamanho_img'],
				$uploadedImage['tipo_img'],
				$uploadedImage['conteudo']
			);
		}

		if ($imagem_id === '') {
			$imagem_id = null;
		}

		$activa = $activa ? 1 : 0;

		if ($id == 0) {
			if (strlen($nome) <= 0) {
				return 0;
			}
			$sql = 'insert into sala (nome, descricao, categoria_id, lugares, activa, imagem_id) values (?, ?, ?, ?, ?, ?)';
			return $this->db->query_insert($sql, [$nome, $descricao, $categoria_id, $lugares, $activa, $imagem_id]);
		} else {
			if (strlen($nome) <= 0) {
				return $id;
			}
			$sql = 'update sala set nome = ?, descricao = ?, categoria_id = ?, lugares = ?, activa = ?, imagem_id = ? where id = ?';
			$this->db->query_update($sql, [$nome, $descricao, $categoria_id, $lugares, $activa, $imagem_id, $id]);
			return $id;
		}
	}

	function excluir($id, $imageId = '')
	{
		try {
			$sql = 'delete from sala where id = ?';
			$this->db->query_update($sql, [$id]);
		} catch (\PDOException $e) {
			return false;
		}
		if ($imageId !== 0 && $imageId !== '' && $imageId !== null) {
			(new \Salas\Models\Imagem($this->db))->excluir((int) $imageId);
		}
		return true;
	}

	function listarPorCategoria($categoria_id)
{
    $sql = 'select id, nome, descricao, lugares, activa from sala where categoria_id = ? order by nome;';
    return $this->db->query($sql, [$categoria_id]);
}

function name($id)
{
	$rows = $this->db->query('select * from sala where id = ?', [$id]);
	return isset($rows[0]['nome']) ? (string) $rows[0]['nome'] : '';
}

function total()
{
	$sql = 'select count(id) as total from sala';
	return $this->db->query($sql);
}

function nullifyImageId($imageId)
{
	$sql = 'update sala set imagem_id = null where imagem_id = ?';
	return $this->db->query_update($sql, [$imageId]);
}
}

?>
