<?php
namespace Salas\Controllers;


use Salas\Core\Persistence\Database;
use Salas\Models\Periodo;
use Salas\Models\Reserva;
use Salas\Models\Sala;

class RelatorioController
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    // Lista de todas as reservas 
    public function listarReservasController($hoje = null, $tipo = 'm', $fim = null)
    {
        $reserva = new Reserva($this->db);
        if ($tipo === 'p' && $fim !== null) {
            return $reserva->listarReservasPeriodo($hoje, $fim);
        }

        return $reserva->listarReservas($hoje, $tipo);
    }

    // relatorio de disciplina com mais reservas
    public function usoMaisReservasController($hoje = null, $tipo = 'm', $fim = null)
    {
        if ($hoje === null) {
            $hoje = new \DateTime();
        }

        $reserva = new Reserva($this->db);

        // Encaminha os parâmetros necessários para o modelo
        return $reserva->usoMaisReservas($hoje, $tipo, $fim);
    }

    // UNIFORMIZAÇÃO DOS HORÁRIOS: Ajusta o multiplicador baseado no intervalo real de dias
    public function totalHorariosController($tipo = 'm', $hoje = null, $fim = null)
    {
        $sala = new Sala($this->db);
        $periodo = new Periodo($this->db);

        $salas = $sala->total();
        $periodos = $periodo->total();

        $total_salas = isset($salas[0]['total']) ? $salas[0]['total'] : (isset($salas['total']) ? $salas['total'] : 1);
        $total_periodos = isset($periodos[0]['total']) ? $periodos[0]['total'] : (isset($periodos['total']) ? $periodos['total'] : 1);

        // Calcula a quantidade real de dias do filtro
        if ($tipo === 'w') {
            $multiplicador_dias = 7;
        } elseif ($tipo === 'p' && $hoje !== null && $fim !== null) {
            // Calcula a diferença real de dias entre as duas datas
            $intervalo = $hoje->diff($fim);
            $multiplicador_dias = $intervalo->days + 1; // +1 para incluir o próprio dia de fim
        } else {
            $multiplicador_dias = 30; // Padrão mensal
        }

        return ($total_salas * $total_periodos * $multiplicador_dias);
    }

    // UNIFORMIZAÇÃO DO GRÁFICO: Agora recebe e consome os mesmos dados que a listagem
    public function totalReservasController($hoje = null, $tipo = 'm', $fim = null)
    {
        if ($hoje === null) {
            $hoje = new \DateTime();
        }

        $reserva = new Reserva($this->db);

        // Usamos uma lógica centralizada no modelo para contar as reservas com base nos mesmos filtros
        $rows = $reserva->listarReservasParaRelatorio($hoje, $tipo, $fim);

        return count($rows);
    }
    public function reportDates(array $query): array
    {
        $type = in_array($query['tipo'] ?? 'm', ['m', 'w', 'p'], true) ? ($query['tipo'] ?? 'm') : 'm';
        $today = $this->parseCompactDate($query['data'] ?? null) ?? new \DateTime();
        $start = $type === 'p' ? ($this->parseCompactDate($query['data_inicio'] ?? null) ?? new \DateTime()) : $today;
        $end = $type === 'p' ? ($this->parseCompactDate($query['data_fim'] ?? null) ?? new \DateTime()) : null;
        if ($type === 'p') {
            $today = $start;
        }
        $previous = clone $today;
        $previous->modify($type === 'm' ? '-1 month' : '-1 week');
        $next = clone $today;
        $next->modify($type === 'm' ? '+1 month' : '+1 week');
        $label = $type === 'p'
            ? $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y')
            : $today->format('d/m/Y');

        return [
            'tipo' => $type,
            'hoje' => $today,
            'inicio' => $start,
            'fim' => $end,
            'hoje_pt' => $label,
            'dia_anterior' => $previous,
            'dia_posterior' => $next,
            'dateFormat' => $type === 'w' ? 'W - Y (M)' : ($type === 'p' ? 'd/m/Y' : 'M Y'),
        ];
    }

    public function reportSummary(array $query): array
    {
        $data = $this->reportDates($query);
        $end = $data['tipo'] === 'p' ? $data['fim'] : null;
        $bookings = $this->usoMaisReservasController($data['hoje'], $data['tipo'], $end);
        $totalBookings = $this->totalReservasController($data['hoje'], $data['tipo'], $end);
        $totalSlots = $this->totalHorariosController($data['tipo'], $data['hoje'], $end);
        if ($totalSlots >= $totalBookings) {
            $totalSlots -= $totalBookings;
        }
        if ($totalBookings === 0 && $totalSlots === 0) {
            $totalSlots = 100;
        }
        return $data + [
            'resumo_reservas' => $bookings,
            'total_reservas' => $totalBookings,
            'total_horarios' => $totalSlots,
        ];
    }

    public function reportReservations(array $query): array
    {
        $data = $this->reportDates($query);
        $data['reservas'] = $this->listarReservasController(
            $data['hoje'],
            $data['tipo'],
            $data['tipo'] === 'p' ? $data['fim'] : null
        );
        return $data;
    }

    private function parseCompactDate($value): ?\DateTime
    {
        if (!is_string($value) || !preg_match('/^\d{8}$/', $value)) {
            return null;
        }
        $date = \DateTime::createFromFormat('!dmY', $value);
        $errors = \DateTime::getLastErrors();
        return $date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
            ? $date
            : null;
    }


}

