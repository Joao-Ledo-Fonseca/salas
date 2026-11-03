<?php
declare(strict_types=1);

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Persistence\Database;
use Salas\Models\Periodo;

class PeriodoTest extends TestCase
{
    public function testExcluirReturnsFalseWhenPeriodoHasReservations(): void
    {
        $exception = new \PDOException('Cannot delete parent row');
        $exception->errorInfo = ['23000', 1451, 'Foreign key constraint fails'];

        $database = $this->createMock(Database::class);
        $database->expects($this->once())
            ->method('query_update')
            ->with('delete from periodo where id = ?', [14])
            ->willThrowException($exception);

        $this->assertFalse((new Periodo($database))->excluir(14));
    }

    public function testExcluirRethrowsUnexpectedDatabaseErrors(): void
    {
        $exception = new \PDOException('Database unavailable');
        $exception->errorInfo = ['HY000', 2002, 'Connection refused'];

        $database = $this->createMock(Database::class);
        $database->expects($this->once())
            ->method('query_update')
            ->willThrowException($exception);

        try {
            (new Periodo($database))->excluir(14);
            $this->fail('Unexpected database errors must be rethrown.');
        } catch (\PDOException $caught) {
            $this->assertSame($exception, $caught);
        }
    }
}
