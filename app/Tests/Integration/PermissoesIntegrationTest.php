<?php
namespace Salas\Tests\Integration;

use Salas\Models\Permissoes;

class PermissoesIntegrationTest extends DatabaseTestCase
{
    public function testListar(): void
    {
        $m = new Permissoes($this->db);
        $this->assertIsArray($m->listar('n'));
    }

    public function testEnsureDefaults(): void
    {
        $m = new Permissoes($this->db);
        $m->ensureDefaults();
        $this->assertIsArray($m->listar());
    }
}
?>