<?php
namespace Salas\Controllers;


use Salas\Models\Categoria;
use Salas\Core\Auth\AuthContext;
use Salas\Core\Auth\AuthorizationService;
use Salas\Core\Persistence\Database;
use Salas\Models\Periodo;
use Salas\Models\Reserva;
use Salas\Models\Sala;
use Salas\Models\Uso;
use Salas\Models\Usuario;
use Salas\Core\Support\DateUtil;

class DashboardController
{
    private Database $db;

    public $reserva;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->reserva = new Reserva($db);
    }

    public function listarPeriodosPainel()
    {
        $periodo = new Periodo($this->db);
        return $periodo->listar();
    }

    public function listarSalasPainel($hoje, $categoria_id = 0, $usuario_id = 0)
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
                    'uso' => '',
                    'entidade' => '',
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
                        $reserva['uso'] = '';
                        $reserva['entidade'] = '';
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

                        $reserva['uso'] = $status['uso_nome'] ?? '';
                        $reserva['entidade'] = $status['entidade_desc'] ?? '';
                        $reserva['observacao'] = (string) ($status['observacao'] ?? '');
                        $reserva['status'] = (int) $status['status'];
                        $reserva['usuario'] = (string) ($status['usuario'] ?? '');
                        $reserva['id'] = $status['id'];
                        $reserva['usuario_id'] = $status['usuario_id'];

                        $tooltipLines = [];
                        $tooltipLines[] = 'Entidade: ' . ($status['entidade_desc'] ?? '');
                        $tooltipLines[] = 'Uso: ' . ($status['uso_nome'] ?? '');
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

                $auth = new AuthContext();
                if ($auth->level() == 0) {
                    $reserva['can_open'] = (($auth->id() == $reserva['usuario_id']) || ($reserva['usuario_id'] == ''));
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

    public function listarCategoriasController()
    {
        $categoria = new Categoria($this->db);
        return $categoria->listar();
    }

    public function prevController($dia, $categoria_id, $usuario_id = 0)
    {
        return $this->reserva->prev($dia, $categoria_id, $usuario_id);
    }

    public function nextController($dia, $categoria_id, $usuario_id = 0)
    {
        return $this->reserva->next($dia, $categoria_id, $usuario_id);
    }
    public function index(array $query): array
    {
        $date = isset($query['data']) && is_string($query['data'])
            ? DateUtil::parseDataPt($query['data'])
            : null;
        $date = $date ?? new \DateTime();

        $categoryId = filter_var($query['categoria'] ?? null, FILTER_VALIDATE_INT);
        $categoryId = $categoryId === false || $categoryId === null ? 0 : $categoryId;

        $usuarioId = filter_var($query['usuario'] ?? null, FILTER_VALIDATE_INT);
        $usuarioId = $usuarioId === false || $usuarioId === null ? 0 : $usuarioId;

        $authLevel = (new AuthContext())->level();
        $showUserFilter = (new AuthorizationService($this->db))->validaPermissao('M_FiltroUtilizador', $authLevel);

        $cacheKey = 'dashboard_cache_' . (new AuthContext())->id();
        $cache = $_SESSION[$cacheKey] ?? null;
        $cacheValid = is_array($cache)
            && isset($cache['ts'], $cache['categories'], $cache['periods'], $cache['users'])
            && (time() - (int) $cache['ts'] < 60);

        if ($cacheValid) {
            $categories = $cache['categories'];
            $periods = $cache['periods'];
            $users = $cache['users'];
        } else {
            $users = [];
            if ($showUserFilter) {
                $usuarioModel = new Usuario($this->db);
                $users = $usuarioModel->listar();
            }
            $categories = $this->listarCategoriasController();
            $periods = $this->listarPeriodosPainel();

            $_SESSION[$cacheKey] = [
                'ts' => time(),
                'categories' => $categories,
                'periods' => $periods,
                'users' => $users,
            ];
        }

        return [
            'date' => $date,
            'dateLabel' => DateUtil::traduz_data($date->format('D d/m/Y'), 'pt'),
            'previousDay' => DateUtil::calcularDiaAdjacente($date, -1),
            'nextDay' => DateUtil::calcularDiaAdjacente($date, 1),
            'previousReservation' => DateUtil::calcularDiaController(
                $date,
                [$this, 'prevController'],
                $categoryId,
                $usuarioId
            ),
            'nextReservation' => DateUtil::calcularDiaController(
                $date,
                [$this, 'nextController'],
                $categoryId,
                $usuarioId
            ),
            'categoryId' => $categoryId,
            'usuarioId' => $usuarioId,
            'showUserFilter' => $showUserFilter,
            'users' => $users,
            'categories' => $categories,
            'periods' => $periods,
            'rooms' => $this->listarSalasPainel($date, $categoryId, $usuarioId),
        ];
    }



}


