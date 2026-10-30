<?php
namespace Salas\Tests\Integration;

use Salas\Models\Usuario;

class UsuarioIntegrationTest extends DatabaseTestCase
{
    public function testListar(): void
    {
        $m = new Usuario($this->db);
        $this->assertIsArray($m->listar());
    }
}
?>