<?php
namespace Salas\Tests\Integration;

use Salas\Models\Periodo;

class PeriodoIntegrationTest extends DatabaseTestCase
{
    public function testListar(): void
    {
        $m = new Periodo($this->db);
        $this->assertIsArray($m->listar());
    }

    public function testSalvarFindExcluir(): void
    {
        $m = new Periodo($this->db);
        $id = $m->salvar(0, 'PeriodoTeste', 99);
        $this->assertGreaterThan(0, $id);
        $found = $m->abrir((int) $id);
        $this->assertNotEmpty($found);
        $this->assertTrue($m->excluir((int) $id) >= 0);
    }
}
?>