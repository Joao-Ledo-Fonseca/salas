<?php

require_once __DIR__ . '/dashboardController.php';
require_once __DIR__ . '/util.php';

class DashboardPageController
{
	private dashboardController $dashboard;

	public function __construct(Database $db, ?dashboardController $dashboard = null)
	{
		$this->dashboard = $dashboard ?? new dashboardController($db);
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
