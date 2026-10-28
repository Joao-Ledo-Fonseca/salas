<?php

require_once __DIR__ . '/../../controllers/DashboardController.php';
require_once __DIR__ . '/../../infrastructure/Support/Util.php';

class DashboardPageController
{
	private DashboardController $dashboard;

	public function __construct(Database $db, ?DashboardController $dashboard = null)
	{
		$this->dashboard = $dashboard ?? new DashboardController($db);
	}

	public function index(array $query): array
	{
		$date = isset($query['data']) && is_string($query['data'])
			? Util::parseDataPt($query['data'])
			: null;
		$date = $date ?? new DateTime();

		$categoryId = filter_var($query['categoria'] ?? null, FILTER_VALIDATE_INT);
		$categoryId = $categoryId === false || $categoryId === null ? 0 : $categoryId;

		$dashboard = $this->dashboard;

		return [
			'date' => $date,
			'dateLabel' => Util::traduz_data($date->format('D d/m/Y'), 'pt'),
			'previousDay' => Util::calcularDiaAdjacente($date, -1),
			'nextDay' => Util::calcularDiaAdjacente($date, 1),
			'previousReservation' => Util::calcularDiaController(
				$date,
				[$dashboard, 'prevController'],
				$categoryId
			),
			'nextReservation' => Util::calcularDiaController(
				$date,
				[$dashboard, 'nextController'],
				$categoryId
			),
			'categoryId' => $categoryId,
			'categories' => $dashboard->listarCategoriasController(),
			'periods' => $dashboard->listarPeriodosDashboard(),
			'rooms' => $dashboard->listarSalasDashboard($date, $categoryId),
		];
	}
}
