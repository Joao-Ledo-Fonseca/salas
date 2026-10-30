<?php
namespace Salas\Tests\Integration;

use Salas\Models\Uso;

class UsoIntegrationTest extends DatabaseTestCase
{
    public function testListar(): void
    {
        $m = new Uso($this->db);
        $this->assertIsArray($m->listar());
    }

    public function testSalvarFindExcluir(): void
    {
        $m = new Uso($this->db);
        $id = $m->salvar(0, 'UsoTeste', 'Desc');
        $this->assertGreaterThan(0, $id);
        $found = $m->abrir((int) $id);
        $this->assertNotEmpty($found);
        $this->assertGreaterThanOrEqual(0, $m->excluir((int) $id));
    }
}
?>