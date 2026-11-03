<?php
declare(strict_types=1);

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Persistence\Database;
use Salas\Models\Reserva;

class ReservasAssociadasTest extends TestCase
{
    public function testRendersEmptyState(): void
    {
        $html = $this->render([
            'reservasAssociadas' => [],
            'mostrarUtilizador' => false,
            'podeEliminarReserva' => false,
            'urlEliminarReserva' => '/?acao=usuario_form&id=7',
            'camposEliminarReserva' => ['id' => 7],
        ]);

        $this->assertStringContainsString('Nenhuma reserva encontrada.', $html);
        $this->assertStringNotContainsString('<table', $html);
    }

    public function testRendersUserReservationsAndDeleteAction(): void
    {
        $html = $this->render([
            'reservasAssociadas' => [[
                'id' => 42,
                'sala' => '<Sala A>',
                'periodo' => 'Manhã',
                'dia' => '03/11/2026',
                'uso' => 'Reunião',
                'status' => 2,
            ]],
            'mostrarUtilizador' => false,
            'podeEliminarReserva' => true,
            'urlEliminarReserva' => '/?acao=usuario_form&id=7',
            'camposEliminarReserva' => ['id' => 7],
        ]);

        $this->assertStringContainsString('<th class="p-0-3">Sala</th>', $html);
        $this->assertStringNotContainsString('<th class="p-0-3">Utilizador</th>', $html);
        $this->assertStringContainsString('&lt;Sala A&gt;', $html);
        $this->assertStringContainsString('Confirmado', $html);
        $this->assertStringContainsString('action="/?acao=usuario_form&amp;id=7"', $html);
        $this->assertStringContainsString('name="reserva_id" value="42"', $html);
        $this->assertStringContainsString('name="csrf_token"', $html);
    }

    public function testRendersRoomReservationsWithUserColumn(): void
    {
        $html = $this->render([
            'reservasAssociadas' => [[
                'id' => 42,
                'usuario' => 'Utilizador A',
                'periodo' => 'Manhã',
                'dia' => '03/11/2026',
                'entidade_desc' => 'Reunião',
                'uso' => 'Trabalho',
                'status' => 1,
            ]],
            'mostrarUtilizador' => true,
            'podeEliminarReserva' => true,
            'urlEliminarReserva' => '/?acao=sala_form&id=3&categoria_filtro=0&activas=activas',
            'camposEliminarReserva' => ['id' => 3],
        ]);

        $this->assertStringContainsString('<th class="p-0-3">Utilizador</th>', $html);
        $this->assertStringContainsString('Utilizador A', $html);
        $this->assertStringContainsString('Entidade', $html);
        $this->assertStringContainsString('action="/?acao=sala_form&amp;id=3&amp;categoria_filtro=0&amp;activas=activas"', $html);
        $this->assertStringContainsString('name="reserva_id" value="42"', $html);
    }

    public function testOwnerDeleteIsScopedToReservationAndUser(): void
    {
        $database = $this->createMock(Database::class);
        $database->expects($this->once())
            ->method('query_update')
            ->with(
                'delete from reserva where id = ? and usuario_id = ?',
                [42, 7]
            )
            ->willReturn(1);

        $this->assertSame(1, (new Reserva($database))->excluirPorIdEUsuario(42, 7));
    }

    private function render(array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src'
            . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'components'
            . DIRECTORY_SEPARATOR . 'ReservasAssociadas.php';

        return (string) ob_get_clean();
    }
}
