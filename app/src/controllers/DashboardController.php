<?php

require_once __DIR__ . '/../infrastructure/Support/Util.php';
require_once __DIR__ . "/../models/Categoria.php";
require_once __DIR__ . "/../models/Periodo.php";
require_once __DIR__ . "/../models/Sala.php";
require_once __DIR__ . "/../models/Reserva.php";

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

    function listarSalasDashboard($hoje, $categoria_id = 0)
    {
        $sl = new sala($this->db);
        $per = new Periodo($this->db);

        $salas = $sl->listar($categoria_id);
        $periodos = $per->listar();
        $linhas = [];

        foreach ($salas as $sala) {
            $cells = [];

            foreach ($periodos as $periodo) {
                $reserva = [
                    'disciplina' => '',
                    'professor' => '',
                    'id' => 0,
                    'usuario_id' => '',
                    'classe' => 'disponivel',
                ];
                $status = $this->reserva->verificarCompleto($hoje, $sala['id'], $periodo['id']);

                if (!empty($status) && isset($status[0]['id'])) {
                    if ($status[0]['status'] == 1) {
                        $reserva['classe'] = 'reservado';
                    } elseif ($status[0]['status'] == 2) {
                        $reserva['classe'] = 'confirmado';
                    } elseif ($status[0]['status'] == 3) {
                        $reserva['classe'] = 'cancelado';
                    } else {
                        $reserva['classe'] = 'desconhecido';
                    }

                    $reserva['disciplina'] = $status[0]['disciplina_desc'];
                    $reserva['professor'] = $status[0]['professor_desc'];
                    $reserva['id'] = $status[0]['id'];
                    $reserva['usuario_id'] = $status[0]['usuario_id'];
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

    function prevController($dia, $categoria_id)
    {
        return $this->reserva->prev($dia, $categoria_id);
    }

    function nextController($dia, $categoria_id)
    {
        return $this->reserva->next($dia, $categoria_id);
    }
}

