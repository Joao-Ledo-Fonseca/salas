<?php
namespace Salas\Controllers;


use Salas\Infrastructure\Persistence\Database;
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
    function listaReservasController($hoje = null, $tipo = 'm', $fim = null)
    {
        $reserva = new Reserva($this->db);
        if ($tipo === 'p' && $fim !== null) {
            return $reserva->listaReservasPeriodo($hoje, $fim);
        }

        return $reserva->listaReservas($hoje, $tipo);
    }

    // relatorio de disciplina com mais reservas
    function disciplinaMaisReservasController($hoje = null, $tipo = 'm', $fim = null)
    {
        if ($hoje === null) {
            $hoje = new \DateTime();
        }

        $reserva = new Reserva($this->db);

        // Encaminha os parâmetros necessários para o modelo
        return $reserva->disciplinaMaisReservas($hoje, $tipo, $fim);
    }

    // UNIFORMIZAÇÃO DOS HORÁRIOS: Ajusta o multiplicador baseado no intervalo real de dias
    function totalHorariosController($tipo = 'm', $hoje = null, $fim = null)
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
    function totalReservasController($hoje = null, $tipo = 'm', $fim = null)
    {
        if ($hoje === null) {
            $hoje = new \DateTime();
        }

        $reserva = new Reserva($this->db);

        // Usamos uma lógica centralizada no modelo para contar as reservas com base nos mesmos filtros
        $rows = $reserva->listaReservasParaRelatorio($hoje, $tipo, $fim);

        return count($rows);
    }
}

