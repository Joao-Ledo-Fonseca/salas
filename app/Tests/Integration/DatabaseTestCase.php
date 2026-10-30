<?php
namespace Salas\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Salas\Core\Persistence\Database;

abstract class DatabaseTestCase extends TestCase
{
    protected Database $db;

    protected function setUp(): void
    {
        parent::setUp();
        $appConfig = $GLOBALS['appConfig'] ?? null;
        $this->db = new Database($appConfig);
        $this->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->db->rollBack();
        parent::tearDown();
    }
}
?>