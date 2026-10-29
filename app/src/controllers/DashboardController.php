<?php
namespace Salas\Controllers;


use Salas\Models\Categoria;
use Salas\Core\Persistence\Database;
use Salas\Models\Periodo;
use Salas\Models\Reserva;
use Salas\Models\Sala;
use Salas\Models\Uso;

class DashboardController
{
    private Database $db;

    public $reserva;

    function __construct(Database $db)
    {
        $this->db = $db;
        $this->reserva = new Reserva($db);
    }

    function listarPeriodosDashboard()
    {
        $periodo = new Periodo($this->db);
        return $periodo->listar();
    }

    function listarSalasDashboard($hoje, $categoria_id = 0, $usuario_id = 0)
    {
        $sl = new Sala($this->db);
        $per = new Periodo($this->db);

        $salas = $sl->listar($categoria_id);
        $periodos = $per->listar();
        $linhas = [];

        // Uma única query para todas as reservas do dia (evita N+1)
        $reservasMap = $this->reserva->listarPorDia($hoje, $categoria_id, $usuario_id);

        foreach ($salas as $sala) {
            $cells = [];

            foreach ($periodos as $periodo) {
                $reserva = [
                    'disciplina' => '',
                    'professor' => '',
                    'observacao' => '',
                    'status' => 0,
                    'usuario' => '',
                    'tooltip' => '',
                    'id' => 0,
                    'usuario_id' => '',
                    'classe' => 'disponivel',
                ];

                $status = $reservasMap[(int)$sala['id']][(int)$periodo['id']] ?? null;

                if (!empty($status) && isset($status['id'])) {
                    if ($usuario_id > 0 && (int) $status['usuario_id'] !== (int) $usuario_id) {
                        $reserva['classe'] = 'disponivel';
                        $reserva['disciplina'] = '';
                        $reserva['professor'] = '';
                        $reserva['observacao'] = '';
                        $reserva['status'] = 0;
                        $reserva['usuario'] = '';
                        $reserva['tooltip'] = '';
                        $reserva['id'] = 0;
                        $reserva['usuario_id'] = '';
                    } else {
                        if ($status['status'] == 1) {
                            $reserva['classe'] = 'reservado';
                        } elseif ($status['status'] == 2) {
                            $reserva['classe'] = 'confirmado';
                        } elseif ($status['status'] == 3) {
                            $reserva['classe'] = 'cancelado';
                        } else {
                            $reserva['classe'] = 'desconhecido';
                        }

                        $statusNames = [1 => 'Reservado', 2 => 'Confirmado', 3 => 'Cancelado'];
                        $statusName = $statusNames[(int) $status['status']] ?? 'Desconhecido';

                        $reserva['disciplina'] = $status['disciplina_desc'];
                        $reserva['professor'] = $status['professor_desc'];
                        $reserva['observacao'] = (string) ($status['observacao'] ?? '');
                        $reserva['status'] = (int) $status['status'];
                        $reserva['usuario'] = (string) ($status['usuario'] ?? '');
                        $reserva['id'] = $status['id'];
                        $reserva['usuario_id'] = $status['usuario_id'];

                        $tooltipLines = [];
                        $tooltipLines[] = 'Entidade: ' . $status['professor_desc'];
                        $tooltipLines[] = 'Uso: ' . $status['disciplina_desc'];
                        if (trim((string) ($status['observacao'] ?? '')) !== '') {
                            $tooltipLines[] = 'Obs: ' . $status['observacao'];
                        }
                        $tooltipLines[] = 'Status: ' . $statusName;
                        if (trim((string) ($status['usuario'] ?? '')) !== '') {
                            $tooltipLines[] = 'User: ' . $status['usuario'];
                        }
                        $reserva['tooltip'] = implode("\n", $tooltipLines);
                    }
                }

                if (isset($_SESSION['user_nivel']) && $_SESSION['user_nivel'] == 0) {
                    $reserva['can_open'] = (($_SESSION['user_id'] == $reserva['usuario_id']) || ($reserva['usuario_id'] == ''));
                } else {
                    $reserva['can_open'] = true;
                }

                $reserva['sala_id'] = $sala['id'];
                $reserva['periodo_id'] = $periodo['id'];
                $cells[] = $reserva;
            }

            $linhas[] = [
                'nome' => $sala['nome'],
                'cells' => $cells,
            ];
        }

        return $linhas;
    }

    function listarCategoriasController()
    {
        $categoria = new Categoria($this->db);
        return $categoria->listar();
    }

    function prevController($dia, $categoria_id, $usuario_id = 0)
    {
        return $this->reserva->prev($dia, $categoria_id, $usuario_id);
    }

    function nextController($dia, $categoria_id, $usuario_id = 0)
    {
        return $this->reserva->next($dia, $categoria_id, $usuario_id);
    }
}

