<?php
namespace Salas\Models;

use Salas\Core\Persistence\Database;

class Uso
{
    private $db = null;

    function __construct(Database $db)
    {
        $this->db = $db;
    }

    function listar()
    {
        $sql = ' select id, nome, descricao from uso order by nome;';
        return $this->db->query($sql);
    }

    function abrir($id)
    {
        $sql = 'select * from uso where uso.id = ?';
        return $this->db->query($sql, array($id));
    }

    function salvar($id, $nome, $descricao)
    {
        if ($id == 0) {
            $sql = 'insert into uso (nome, descricao) values (?, ?)';
            return $this->db->query_insert($sql, array($nome, $descricao));
        } else {
            $sql = 'update uso set nome = ?, descricao = ? where id = ?';
            return $this->db->query_update($sql, array($nome, $descricao, $id));
        }
    }

    function excluir($id)
    {
        $sql = 'delete from uso where id = ?';
        return $this->db->query_update($sql, array($id));
    }

    function total()
    {
        $sql = 'select count(id) as total from uso';
        return $this->db->query($sql);
    }
}
?>
