<?php
namespace Salas\Tests\Integration;

use Salas\Models\Reserva;

class ReservaIntegrationTest extends DatabaseTestCase
{
    public function testListar(): void
    {
        $m = new Reserva($this->db);
        $this->assertIsArray($m->listarReservas());
    }
}
?>