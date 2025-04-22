<?php
require_once "db_mysqli.php";

class Permissoes
{

	function listar()
	{

		$db = new Database();

		$sql = ' select seq, nome, user, admin from permissoes order by seq;';
		return $db->query($sql);
	}



	function salvar($seq, $nome, bool $user, bool $admin, $tipo)
	{

		$db = new Database();

		// inserir nova permissao
		if ($tipo == "I") {		            
			$sql = 'insert into permissoes ( seq, nome, user, admin) values ('. $seq . ',"' . $nome . '",' . (int) $user . ',' . (int) $admin .')';
			return $db->query_insert($sql);            
		}        
		// atualizar permissao existente
		else if ($tipo == "U") {			
			$sql = ' update permissoes set user = ' . (int) $user . ' , admin = ' . (int) $admin . ' where nome = "' . $nome . '"';
			return $db->query_update($sql);
		}
		// actualizar apenas sequência
        else if ($tipo == "R") {
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