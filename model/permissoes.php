<?php
require_once "db_mysqli.php";

class Permissoes
{
	
	private $db = null;

	function __construct()
	{
		$this->db = new Database();
	}

	function listar($tipo = 't')
	{
		//  Permissoes são tipo do tipo 
		// 		s -> simples  (só o campo uexterno é usado)
		// 		n -> por niveis (são usados os três campos uexterno, uinterno, admin)
		$selector = ($tipo <> 't' ? 'where tipo="' . $tipo . '"' : '');
		$sql = ' select seq, nome, tipo, uexterno, uinterno, admin 
				from permissoes ' .
			$selector .
			' order by seq;';

		return $this->db->query($sql);
	}


	function salvar($seq, $nome, $tipo, $uexterno, $uinterno, $admin)
	{

		// inserir nova permissao
		$sql = ' insert into permissoes ( seq, nome, tipo, uexterno, uinterno, admin) 
					 values (' . $seq . ',"' . $nome . '","' . $tipo . '",' . (int) $uexterno . ',' . (int) $uinterno . ',' . (int) $admin . ')';
		return $this->db->query_insert($sql);

	}

	function update($seq, $nome, $tipo, $uexterno, $uinterno, $admin, $comando)
	{

		// atualizar permissao existente
		if ($comando == "Update") {
			$sql = ' update permissoes 
						set uexterno = ' . (int) $uexterno . ' , uinterno = ' . (int) $uinterno . ' , admin = ' . (int) $admin . ' 
						where nome = "' . $nome . '"';
			return $this->db->query_update($sql);
		}
		// actualizar apenas sequência
		else if ($comando == "Renumera") {
			$sql = 'update permissoes set seq = ' . $seq . ' where nome = "' . $nome . '"';
			return $this->db->query_update($sql);
		}

	}

	function renumerar($seq, $nome)
	{		

		// actualizar apenas sequência		
		$sql = 'update permissoes set seq = ' . $seq . ' where nome = "' . $nome . '"';
		return $this->db->query_update($sql);

	}



	function excluir($nome)
	{

		$sql = 'delete from permissoes where nome = "' . $nome . '"';
		return $this->db->query_update($sql);
	}

}

?>