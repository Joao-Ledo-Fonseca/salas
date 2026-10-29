<?php
namespace Salas\Application\Services;

use Salas\Infrastructure\Persistence\Database;

class DefaultSuperAdminSeeder
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function seed(): void
    {
        $result = $this->db->query('select count(*) as total from usuario');
        $total = isset($result[0]['total']) ? (int) $result[0]['total'] : 0;

        if ($total === 0) {
            $senhaHash = password_hash('sadmin', PASSWORD_DEFAULT);
            $this->db->query_insert(
                'insert into usuario (nome, email, senha, telefone, NIF, nivel) values (?, ?, ?, ?, ?, ?)',
                ['sadmin', 'sadmin', $senhaHash, '', '', 3]
            );
        }
    }
}
