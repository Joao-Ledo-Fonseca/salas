<?php
namespace Salas\Tests\Integration;

use Salas\Models\Sala;

class SalaIntegrationTest extends DatabaseTestCase
{
    public function testListar(): void
    {
        $m = new Sala($this->db);
        $this->assertIsArray($m->listar());
    }
}
?>