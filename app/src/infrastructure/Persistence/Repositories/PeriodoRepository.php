<?php

class PeriodoRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function list(): array
    {
        $sql = 'select * from periodo order by seq;';
        return $this->db->query($sql);
    }

    public function find(int $id): array
    {
        $sql = 'select * from periodo where id = ?';
        return $this->db->query($sql, [$id]);
    }

    public function save(int $id, string $nome, int $seq): int
    {
        if ($id === 0) {
            $sql = 'insert into periodo (nome, seq) values (?, ?)';
            return $this->db->query_insert($sql, [$nome, $seq]);
        }

        $sql = 'update periodo set nome = ?, seq = ? where id = ?';
        $this->db->query_update($sql, [$nome, $seq, $id]);
        return $id;
    }

    public function delete(int $id): int
    {
        $sql = 'delete from periodo where id = ?';
        return $this->db->query_update($sql, [$id]);
    }

    public function total(): array
    {
        $sql = 'select count(id) as total from periodo';
        return $this->db->query($sql);
    }

    public function name(int $id): string
    {
        $rows = $this->find($id);
        return isset($rows[0]['nome']) ? (string) $rows[0]['nome'] : '';
    }
}
