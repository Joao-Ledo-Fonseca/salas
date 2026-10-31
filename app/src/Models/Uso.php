<?php
namespace Salas\Models;

use Salas\Core\Persistence\Database;

/**
 * Classe Uso.
 */
class Uso
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
     * Método listar.
     * @return mixed
     */
    public function listar()
    {
        $sql = ' select id, nome, descricao from uso order by nome;';
        return $this->db->query($sql);
    }

    /**
     * Método abrir.
     * @param mixed $id
     * @return mixed
     */
    public function abrir($id)
    {
        $sql = 'select * from uso where uso.id = ?';
        return $this->db->query($sql, array($id));
    }

    /**
     * Método salvar.
     * @param mixed $id
     * @param mixed $nome
     * @param mixed $descricao
     * @return mixed
     */
    public function salvar($id, $nome, $descricao)
    {
        if ($id == 0) {
            $sql = 'insert into uso (nome, descricao) values (?, ?)';
            return $this->db->query_insert($sql, array($nome, $descricao));
        } else {
            $sql = 'update uso set nome = ?, descricao = ? where id = ?';
            return $this->db->query_update($sql, array($nome, $descricao, $id));
        }
    }

    /**
     * Método excluir.
     * @param mixed $id
     * @return mixed
     */
    public function excluir($id)
    {
        $sql = 'delete from uso where id = ?';
        return $this->db->query_update($sql, array($id));
    }

    /**
     * Método total.
     * @return mixed
     */
    public function total()
    {
        $sql = 'select count(id) as total from uso';
        return $this->db->query($sql);
    }




}
?>
