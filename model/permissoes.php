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
        if ($tipo !== 't') {
            $sql = 'select seq, nome, tipo, uexterno, uinterno, admin from permissoes where tipo = ? order by seq;';
            return $this->db->query($sql, [$tipo]);
        }

        $sql = 'select seq, nome, tipo, uexterno, uinterno, admin from permissoes order by seq;';
        return $this->db->query($sql);
    }


    function salvar($seq, $nome, $tipo, $uexterno, $uinterno, $admin)
    {
        // inserir nova permissao
        $sql = 'insert into permissoes (seq, nome, tipo, uexterno, uinterno, admin) values (?, ?, ?, ?, ?, ?)';
        return $this->db->query_insert($sql, [$seq, $nome, $tipo, (int) $uexterno, (int) $uinterno, (int) $admin]);
    }

    function update($seq, $nome, $tipo, $uexterno, $uinterno, $admin, $comando)
    {
        // atualizar permissao existente
        if ($comando == "Update") {
            $sql = 'update permissoes set uexterno = ?, uinterno = ?, admin = ? where nome = ?';
            return $this->db->query_update($sql, [(int) $uexterno, (int) $uinterno, (int) $admin, $nome]);
        }
        // actualizar apenas sequência
        else if ($comando == "Renumera") {
            $sql = 'update permissoes set seq = ? where nome = ?';
            return $this->db->query_update($sql, [$seq, $nome]);
        }

    }

    function renumerar($seq, $nome)
    {		
        // actualizar apenas sequência		
        $sql = 'update permissoes set seq = ? where nome = ?';
        return $this->db->query_update($sql, [$seq, $nome]);
    }

    function excluir($nome)
    {
        $sql = 'delete from permissoes where nome = ?';
        return $this->db->query_update($sql, [$nome]);
    }
}
?>