<?php
namespace Salas\Application\Pages;


use Salas\Application\Services\AuthorizationService;
use Salas\Controllers\DashboardController;
use Salas\Infrastructure\Persistence\Database;
use Salas\Models\Usuario;
use Salas\Infrastructure\Support\Util;

class DashboardPageController
{
	private Database $db;
	private DashboardController $dashboard;

	public function __construct(Database $db, ?DashboardController $dashboard = null)
	{
		$this->db = $db;
		$this->dashboard = $dashboard ?? new DashboardController($db);
	}

	public function index(array $query): array
	{
		$date = isset($query['data']) && is_string($query['data'])
			? Util::parseDataPt($query['data'])
			: null;
		$date = $date ?? new \DateTime();

		$categoryId = filter_var($query['categoria'] ?? null, FILTER_VALIDATE_INT);
		$categoryId = $categoryId === false || $categoryId === null ? 0 : $categoryId;

		$usuarioId = filter_var($query['usuario'] ?? null, FILTER_VALIDATE_INT);
		$usuarioId = $usuarioId === false || $usuarioId === null ? 0 : $usuarioId;

		$dashboard = $this->dashboard;

		$authLevel = (int) ($_SESSION['user_nivel'] ?? 0);
		$showUserFilter = (new AuthorizationService($this->db))->validaPermissao('M_FiltroUtilizador', $authLevel);

	$cacheKey = 'dashboard_cache_' . (int) ($_SESSION['user_id'] ?? 0);
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
		$categories = $dashboard->listarCategoriasController();
		$periods = $dashboard->listarPeriodosDashboard();

		$_SESSION[$cacheKey] = [
			'ts' => time(),
			'categories' => $categories,
			'periods' => $periods,
			'users' => $users,
		];
	}

	return [
		'date' => $date,
		'dateLabel' => Util::traduz_data($date->format('D d/m/Y'), 'pt'),
		'previousDay' => Util::calcularDiaAdjacente($date, -1),
		'nextDay' => Util::calcularDiaAdjacente($date, 1),
		'previousReservation' => Util::calcularDiaController(
			$date,
			[$dashboard, 'prevController'],
			$categoryId,
			$usuarioId
		),
		'nextReservation' => Util::calcularDiaController(
			$date,
			[$dashboard, 'nextController'],
			$categoryId,
			$usuarioId
		),
		'categoryId' => $categoryId,
		'usuarioId' => $usuarioId,
		'showUserFilter' => $showUserFilter,
		'users' => $users,
		'categories' => $categories,
		'periods' => $periods,
		'rooms' => $dashboard->listarSalasDashboard($date, $categoryId, $usuarioId),
	];
	}
}
