<?php
namespace Salas\Application;

use Salas\Application\Pages\ApplicationPageController;
use Salas\Infrastructure\Persistence\Database;


class Router
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function dispatch(string $action, array $query, array $post): array
    {
        return (new ApplicationPageController($this->db))->dispatch($action, $query, $post);
    }

    public function dashboard(array $query): array
    {
        return (new ApplicationPageController($this->db))->dashboard($query);
    }
}
