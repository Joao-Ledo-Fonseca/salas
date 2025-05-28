<?php
require_once "db_mysqli.php";

class sala
{
	private $db = null;

	function __construct()
	{
		$this->db = new Database();
	}

	function listar($filtro_categorias = 0 , $filtro_activas = 'todas')
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


		$sql = ' select sala.*, categoria.nome as categoria from sala 
				 left join categoria on sala.categoria_id = categoria.id
				 where (categoria.id = "' . $filtro_categorias . '" or "' . $filtro_categorias . '" = 0) and ' . $filtro_activas . ' order by categoria.nome, sala.nome;';
		return $this->db->query($sql);
	}

	function abrir($id)
	{		
		$sql = ' select * from sala 
					where id = ' . $id . ';';		
		return $this->db->query($sql);
	}
			 
	
	function salvar($id, $nome, $descricao, $categoria_id, $lugares, $activa, $imagem_id)	
	{

		if ($imagem_id == '') {
			$imagem_id = 'NULL';;
		}

		$activa=$activa?1:0; // converte para tinyint do MySql

		if ($id == 0) {
			// inserir
			$sql = ' insert into sala ( nome, descricao, categoria_id, lugares, activa, imagem_id ) 
							values ( "' . $nome . '", "' . $descricao . '", "' . $categoria_id . '", ' . $lugares . ',' . $activa . ',' . $imagem_id . ') ';			
							

			return $this->db->query_insert($sql);			
		} else {
			// atualizar
			$sql = ' update sala set nome = "' . $nome . '", descricao = "' . $descricao . '", categoria_id= "'. $categoria_id . '" , lugares=' . $lugares .' ,activa=' . $activa . ', imagem_id = ' . $imagem_id . ' where id = ' . $id;						
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