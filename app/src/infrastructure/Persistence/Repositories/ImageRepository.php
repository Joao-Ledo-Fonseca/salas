<?php

class ImageRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function find(int $id): array
    {
        $sql = 'select * from imagem where id = ?';
        return $this->db->query($sql, [$id]);
    }

    public function listar(): array
    {
        $sql = 'select id, nome_imagem, tipo_imagem, imagem from imagem order by id desc';
        return $this->db->query($sql);
    }

    public function save(int $id, string $nome, string $tamanho, string $tipo, string $conteudo): int
    {
        if ($id === 0) {
            $sql = 'insert into imagem (nome_imagem, tamanho_imagem, tipo_imagem, imagem) values (?, ?, ?, ?)';
            return $this->db->query_insert($sql, [$nome, $tamanho, $tipo, $conteudo]);
        }

        $sql = 'update imagem set nome_imagem = ?, tamanho_imagem = ?, tipo_imagem = ?, imagem = ? where id = ?';
        $this->db->query_update($sql, [$nome, $tamanho, $tipo, $conteudo, $id]);
        return $id;
    }

    public function delete(int $id): int
    {
        $sql = 'delete from imagem where id = ?';
        return $this->db->query_update($sql, [$id]);
    }
}
