<?php
namespace Salas\Tests\Integration;

use Salas\Models\Categoria;

class CategoriaIntegrationTest extends DatabaseTestCase
{
    public function testListarReturnsArray(): void
    {
        $model = new Categoria($this->db);
        $rows = $model->listar();
        $this->assertIsArray($rows);
    }

    public function testSalvarAndFind(): void
    {
        $model = new Categoria($this->db);
        $id = $model->salvar(0, 'TesteCat', 'Desc', null, false);
        $this->assertGreaterThan(0, $id);

        $found = $model->abrir((int) $id);
        $this->assertNotEmpty($found);
        $this->assertSame('TesteCat', $found[0]['nome']);
    }

    public function testExcluir(): void
    {
        $model = new Categoria($this->db);
        $id = $model->salvar(0, 'CatDel', 'Desc', null, false);
        $ok = $model->excluir((int) $id, '');
        $this->assertTrue($ok);
    }
}
?>