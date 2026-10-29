<?php
namespace Salas\Application\Services;


use Salas\Infrastructure\Persistence\Database;
use Salas\Infrastructure\Persistence\Repositories\PeriodoRepository;

class PeriodoService
{
    private PeriodoRepository $periods;

    public function __construct(Database $db)
    {
        $this->periods = new PeriodoRepository($db);
    }

    public function list(): array
    {
        return $this->periods->list();
    }

    public function open(int $id): array
    {
        return $this->periods->find($id);
    }

    public function save(int $id, string $nome, int $seq): void
    {
        if (strlen($nome) > 0) {
            $this->periods->save($id, $nome, $seq);
        }
    }

    public function delete(int $id): bool
    {
        try {
            $this->periods->delete($id);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function name(int $id): string
    {
        return $this->periods->name($id);
    }
}
