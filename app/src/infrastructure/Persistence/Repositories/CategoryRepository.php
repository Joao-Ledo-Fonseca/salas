<?php
namespace Salas\Infrastructure\Persistence\Repositories;

use Salas\Infrastructure\Persistence\Database;

class CategoryRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function list(): array
    {
        $sql = 'select id, nome, descricao from categoria order by nome;';
        return $this->db->query($sql);
    }

    public function find(int $id): array
    {
        $sql = 'select * from categoria where categoria.id = ?';
        return $this->db->query($sql, [$id]);
    }

    public function save(int $id, string $nome, string $descricao, $imagemId): int
    {
        if ($imagemId === '') {
            $imagemId = null;
        }

        if ($id === 0) {
            $sql = 'insert into categoria (nome, descricao, imagem_id) values (?, ?, ?)';
            return $this->db->query_insert($sql, [$nome, $descricao, $imagemId]);
        }

        $sql = 'update categoria set nome = ?, descricao = ?, imagem_id = ? where id = ?';
        $this->db->query_update($sql, [$nome, $descricao, $imagemId, $id]);
        return $id;
    }

    public function delete(int $id): int
    {
        $sql = 'delete from categoria where id = ?';
        return $this->db->query_update($sql, [$id]);
    }

    public function total(): array
    {
        $sql = 'select count(id) as total from categoria';
        return $this->db->query($sql);
    }
}
