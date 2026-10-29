<?php

class SalaRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function list(int $categoryId = 0, string $activeFilter = 'activas'): array
    {
        switch ($activeFilter) {
            case 'todas':
                $activeSql = 'true';
                break;
            case 'activas':
                $activeSql = 'sala.activa = true';
                break;
            default:
                $activeSql = 'sala.activa = false';
                break;
        }

        $sql = 'select sala.*, categoria.nome as categoria from sala
            left join categoria on sala.categoria_id = categoria.id
            where (categoria.id = ? or ? = 0) and ' . $activeSql . ' order by categoria.nome, sala.nome;';
        return $this->db->query($sql, [$categoryId, $categoryId]);
    }

    public function find(int $id): array
    {
        $sql = 'select * from sala where id = ?';
        return $this->db->query($sql, [$id]);
    }

    public function save(int $id, string $nome, string $descricao, int $categoryId, int $places, bool $active, $imageId): int
    {
        if ($imageId === '') {
            $imageId = null;
        }

        $activeValue = $active ? 1 : 0;

        if ($id === 0) {
            $sql = 'insert into sala (nome, descricao, categoria_id, lugares, activa, imagem_id) values (?, ?, ?, ?, ?, ?)';
            return $this->db->query_insert($sql, [$nome, $descricao, $categoryId, $places, $activeValue, $imageId]);
        }

        $sql = 'update sala set nome = ?, descricao = ?, categoria_id = ?, lugares = ?, activa = ?, imagem_id = ? where id = ?';
        $this->db->query_update($sql, [$nome, $descricao, $categoryId, $places, $activeValue, $imageId, $id]);
        return $id;
    }

    public function delete(int $id): int
    {
        $sql = 'delete from sala where id = ?';
        return $this->db->query_update($sql, [$id]);
    }

    public function total(): array
    {
        $sql = 'select count(id) as total from sala';
        return $this->db->query($sql);
    }

    public function name(int $id): string
    {
        $rows = $this->find($id);
        return isset($rows[0]['nome']) ? (string) $rows[0]['nome'] : '';
    }

    public function nullifyImageId(int $imageId): int
    {
        $sql = 'update sala set imagem_id = null where imagem_id = ?';
        return $this->db->query_update($sql, [$imageId]);
    }
}
