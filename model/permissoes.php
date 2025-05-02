<?php
require_once "db_mysqli.php";

class Permissoes
{

	function listar()
	{

		$db = new Database();

		$sql = ' select seq, nome, uexterno, uinterno, admin from permissoes order by seq;';
		return $db->query($sql);
	}



	function salvar($seq, $nome, $uexterno, $uinterno, $admin, $tipo)
	{

		$db = new Database();

		// inserir nova permissao
		if ( $tipo == 'Insert' ) {		            
			$sql = ' insert into permissoes ( seq, nome, uexterno, uinterno, admin) 
					 values ('. $seq . ',"' . $nome . '",' . (int) $uexterno . ',' . (int) $uinterno . ',' . (int) $admin .')';			
			return $db->query_insert($sql);            
		}        
		// atualizar permissao existente
		else if ($tipo == "Update") {			
			$sql = ' update permissoes 
						set uexterno = ' . (int) $uexterno . ' , uinterno = ' . (int) $uinterno . ' , admin = ' . (int) $admin    . ' 
						where nome = "' . $nome . '"';
			return $db->query_update($sql);
		}
		// actualizar apenas sequência
        else if ($tipo == "Renumera") {
            $sql = 'update permissoes set seq = ' . $seq . ' where nome = "' . $nome . '"';
            return $db->query_update($sql);
        }         

	}

    function excluir($nome)
	{
		$db = new Database();
		$sql = 'delete from permissoes where nome = "' . $nome .'"';
		return $db->query_update($sql);
	}

}

?>