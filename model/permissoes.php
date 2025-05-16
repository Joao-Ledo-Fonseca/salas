<?php
require_once "db_mysqli.php";

class Permissoes
{

	function listar($tipo = 't')
	{
		$db = new Database();

		$selector = ($tipo <> 't' ? 'where tipo="' . $tipo . '"' : '');
		$sql = ' select seq, nome, tipo, uexterno, uinterno, admin 
				from permissoes ' .
			$selector .
			' order by seq;';

		return $db->query($sql);
	}


	function salvar($seq, $nome, $tipo, $uexterno, $uinterno, $admin)
	{

		$db = new Database();

		// inserir nova permissao
		$sql = ' insert into permissoes ( seq, nome, tipo, uexterno, uinterno, admin) 
					 values (' . $seq . ',"' . $nome . '","' . $tipo . '",' . (int) $uexterno . ',' . (int) $uinterno . ',' . (int) $admin . ')';
		return $db->query_insert($sql);

	}

	function update($seq, $nome, $tipo, $uexterno, $uinterno, $admin, $comando)
	{

		$db = new Database();

		// atualizar permissao existente
		if ($comando == "Update") {
			$sql = ' update permissoes 
						set uexterno = ' . (int) $uexterno . ' , uinterno = ' . (int) $uinterno . ' , admin = ' . (int) $admin . ' 
						where nome = "' . $nome . '"';
			return $db->query_update($sql);
		}
		// actualizar apenas sequência
		else if ($comando == "Renumera") {
			$sql = 'update permissoes set seq = ' . $seq . ' where nome = "' . $nome . '"';
			return $db->query_update($sql);
		}

	}

	function renumerar($seq, $nome)
	{
		$db = new Database();

		// actualizar apenas sequência		
		$sql = 'update permissoes set seq = ' . $seq . ' where nome = "' . $nome . '"';
		return $db->query_update($sql);

	}






	function excluir($nome)
	{
		$db = new Database();
		$sql = 'delete from permissoes where nome = "' . $nome . '"';
		return $db->query_update($sql);
	}

}

?>