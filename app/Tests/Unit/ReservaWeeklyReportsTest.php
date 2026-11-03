<?php
declare(strict_types=1);

namespace Salas\Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Salas\Core\Persistence\Database;
use Salas\Models\Reserva;

class ReservaWeeklyReportsTest extends TestCase
{
    private DateTimeImmutable $date;

    protected function setUp(): void
    {
        $this->date = new DateTimeImmutable('2021-01-01');
    }

    public function testWeeklyReservationListUsesIsoWeekDateRange(): void
    {
        $database = $this->createMock(Database::class);
        $database->expects($this->once())
            ->method('query')
            ->with(
                $this->callback(static function (string $sql): bool {
                    return str_contains($sql, 'usuario.nome AS reservado_por')
                        && str_contains($sql, 'LEFT JOIN usuario ON reserva.usuario_id = usuario.id')
                        && str_contains($sql, 'dia >= ? AND dia < ?')
                        && !str_contains($sql, 'YEARWEEK');
                }),
                ['532020', '2020-12-28', '2021-01-04']
            )
            ->willReturn([[
                'status' => 1,
                'reservado_por' => 'João',
            ]]);

        $reservations = (new Reserva($database))->listarReservas($this->date, 'w');

        $this->assertSame('reservada', $reservations[0]['status_nome']);
        $this->assertSame('João', $reservations[0]['reservado_por']);
    }

    public function testReservationListsJoinTheUserForTheReservedByColumn(): void
    {
        $database = $this->createMock(Database::class);
        $database->expects($this->once())
            ->method('query')
            ->with(
                $this->callback(static function (string $sql): bool {
                    return str_contains($sql, 'usuario.nome AS reservado_por')
                        && str_contains($sql, 'LEFT JOIN usuario ON reserva.usuario_id = usuario.id');
                }),
                ['2020-12-30', '2021-01-05']
            )
            ->willReturn([['status' => 1, 'reservado_por' => 'João']]);

        $reservations = (new Reserva($database))->listarReservasPeriodo(
            new DateTimeImmutable('2020-12-30'),
            new DateTimeImmutable('2021-01-05')
        );

        $this->assertSame('João', $reservations[0]['reservado_por']);
    }

    public function testWeeklyReservationCountUsesIsoWeekDateRange(): void
    {
        $database = $this->expectWeeklyQuery(['2020-12-28', '2021-01-04'], [['id' => 1]]);

        $this->assertCount(1, (new Reserva($database))->listarReservasParaRelatorio($this->date, 'w'));
    }

    public function testWeeklyUsageReportUsesIsoWeekDateRange(): void
    {
        $database = $this->expectWeeklyQuery(['2020-12-28', '2021-01-04'], [['uso' => 'Reunião', 'total' => 1]]);

        $this->assertSame(
            [['uso' => 'Reunião', 'total' => 1]],
            (new Reserva($database))->usoMaisReservas($this->date, 'w')
        );
    }

    public function testWeeklyDateChartFormatsDatesInPhp(): void
    {
        $database = $this->expectWeeklyQuery(['2020-12-28', '2021-01-04'], [[
            'data' => '2021-01-01',
            'total' => 1,
        ]]);

        $this->assertSame(
            [['data' => '01/01/2021', 'total' => 1]],
            (new Reserva($database))->reservasPorData($this->date, 'w')
        );
    }

    public function testCustomRangeDateChartGroupsByIsoWeekInPhp(): void
    {
        $database = $this->createMock(Database::class);
        $database->expects($this->once())
            ->method('query')
            ->with(
                $this->callback(static function (string $sql): bool {
                    return str_contains($sql, 'GROUP BY r.dia')
                        && !str_contains($sql, 'YEARWEEK')
                        && !str_contains($sql, 'DATE_FORMAT');
                }),
                ['2020-12-30', '2021-01-05']
            )
            ->willReturn([
                ['data' => '2020-12-31', 'total' => '2'],
                ['data' => '2021-01-01', 'total' => '3'],
                ['data' => '2021-01-04', 'total' => '1'],
            ]);

        $this->assertSame(
            [
                ['data' => '31/12/2020', 'total' => 5],
                ['data' => '04/01/2021', 'total' => 1],
            ],
            (new Reserva($database))->reservasPorData(
                new DateTimeImmutable('2020-12-30'),
                'p',
                new DateTimeImmutable('2021-01-05')
            )
        );
    }

    public function testWeeklyRoomReportUsesIsoWeekDateRange(): void
    {
        $database = $this->expectWeeklyQuery(['2020-12-28', '2021-01-04'], [['sala' => 'Sala A', 'total' => 1]]);

        $this->assertSame(
            [['sala' => 'Sala A', 'total' => 1]],
            (new Reserva($database))->reservasPorSala($this->date, 'w')
        );
    }

    private function expectWeeklyQuery(array $parameters, array $result): Database
    {
        $database = $this->createMock(Database::class);
        $database->expects($this->once())
            ->method('query')
            ->with(
                $this->callback(static function (string $sql): bool {
                    return str_contains($sql, 'dia >= ?')
                        && str_contains($sql, 'dia < ?')
                        && !str_contains($sql, 'YEARWEEK')
                        && !str_contains($sql, 'EXTRACT(WEEK');
                }),
                $parameters
            )
            ->willReturn($result);

        return $database;
    }
}
