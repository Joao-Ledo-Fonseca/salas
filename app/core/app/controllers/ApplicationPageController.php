<?php

require_once __DIR__ . '/util.php';
require_once __DIR__ . '/DashboardPageController.php';
require_once __DIR__ . '/dashboardController.php';
require_once __DIR__ . '/categoriaController.php';
require_once __DIR__ . '/periodoController.php';
require_once __DIR__ . '/salaController.php';
require_once __DIR__ . '/usuarioController.php';
require_once __DIR__ . '/permissoesController.php';
require_once __DIR__ . '/reservaController.php';
require_once __DIR__ . '/../integration/SalasSessionAdapter.php';

class ApplicationPageController
{
	private Database $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	private const PAGE_PERMISSIONS = [
		'categoria_list' => 'M_Categorias',
		'categoria_form' => 'M_Categorias',
		'sala_list' => 'M_Salas',
		'sala_form' => 'M_Salas',
		'periodo_list' => 'M_Periodos',
		'periodo_form' => 'M_Periodos',
		'usuario_list' => 'M_Utilizadores',
		'permissoes_form' => 'M_Permissoes',
		'relatorios' => 'M_Relatorios',
		'relatorios2' => 'M_Relatorios',
		'relatorios_ORG' => 'M_Estatisticas',
		'reserva_form' => 'M_Reservas',
	];

	public function dispatch(string $action, array $query, array $post): array
	{
		$this->authorize($action, $query, $post);
		if ($action === 'reserva_form' && isset($post['submeter_excluir'])) {
			return ['response' => (new ReservaController($this->db))->excluir($post)];
		}
		if ($action === 'reserva_form' && isset($post['submeter_salvar'])) {
			return ['response' => (new ReservaController($this->db))->guardar($post)];
		}

		$data = ['menuData' => $this->menuData()];

		switch ($action) {
			case 'categoria_list':
				$data['lista'] = (new categoriaController($this->db))->listarController();
				break;
			case 'categoria_form':
				$data += $this->categoriaForm($query);
				break;
			case 'periodo_list':
				$data['lista'] = (new periodoController($this->db))->listarcontroller();
				break;
			case 'periodo_form':
				$data += $this->periodoForm($query);
				break;
			case 'sala_list':
				$data += $this->salaList($query, $post);
				break;
			case 'sala_form':
				$data += $this->salaForm($query, $post);
				break;
			case 'usuario_list':
				$data['lista'] = (new UsuarioController($this->db))->listarcontroller();
				$permissionController = new PermissoesController($this->db);
				foreach ($data['lista'] as &$user) {
					$user['nivel_nome'] = $permissionController->nomeNivel((int) $user['nivel'], 2);
				}
				unset($user);
				break;
			case 'usuario_form':
				$data += $this->usuarioForm($query, $post);
				break;
			case 'permissoes_form':
				$data += $this->permissoesForm();
				break;
			case 'relatorios':
				$data += $this->reportSummary($query);
				break;
			case 'relatorios_ORG':
				$data += $this->reportSummary($query);
				break;
			case 'relatorios2':
				$data += $this->reportReservations($query);
				break;
			case 'reserva_form':
				$data += $this->reservaForm($query);
				break;
			default:
				throw new RuntimeException('Ação não suportada.');
		}

		$routes = include __DIR__ . '/../config/routes.php';
		return ['view' => $routes[$action], 'data' => $data];
	}

	public function dashboard(array $query): array
	{
		$this->authorize('dashboard', $query, []);
		return [
			'dashboardView' => (new DashboardPageController($this->db))->index($query),
			'menuData' => $this->menuData(),
		];
	}

	private function authorize(string $action, array $query, array $post): void
	{
		$session = new Salas\Integration\SalasSessionAdapter();
		if (!$session->isAuthenticated()) {
			$isAjax = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
			if ($isAjax) {
				http_response_code(401);
				exit;
			}
			header('Location: ' . app_url('?acao=login'));
			exit;
		}

		$permission = self::PAGE_PERMISSIONS[$action] ?? null;
		$level = (int) ($_SESSION['user_nivel'] ?? -1);
		if ($action === 'usuario_form') {
			$targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
			if ($targetId === (int) ($_SESSION['user_id'] ?? 0)) {
				return;
			}
			$permission = 'M_Utilizadores';
		}

		if ($action === 'dashboard') {
			return;
		}

		if ($permission !== null && !(new PermissoesController($this->db))->validaPermissao($permission, $level)) {
			$isAjax = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
			if ($isAjax) {
				http_response_code(403);
				exit;
			}
			header('Location: ' . app_url());
			exit;
		}
	}

	private function menuData(): array
	{
		$permissions = new PermissoesController($this->db);
		$level = (int) $_SESSION['user_nivel'];
		$items = [
			['permission' => 'M_Categorias', 'label' => 'Categorias', 'url' => '?acao=categoria_list'],
			['permission' => 'M_Salas', 'label' => 'Salas', 'url' => '?acao=sala_list'],
			['permission' => 'M_Periodos', 'label' => 'Períodos', 'url' => '?acao=periodo_list'],
			['permission' => 'M_Utilizadores', 'label' => 'Utilizadores', 'url' => '?acao=usuario_list'],
			['permission' => 'M_Permissoes', 'label' => 'Permissões', 'url' => '?acao=permissoes_form'],
		];
		$visibleItems = [];
		foreach ($items as $item) {
			if ($permissions->validaPermissao($item['permission'], $level)) {
				$visibleItems[] = $item;
			}
		}

		return [
			'userName' => (string) ($_SESSION['user_nome'] ?? ''),
			'userLevel' => $permissions->nomeNivel($level, 1),
			'profileUrl' => app_url('?acao=usuario_form&id=' . (int) $_SESSION['user_id']),
			'showStatistics' => $permissions->validaPermissao('M_Estatisticas', $level),
			'showReports' => $permissions->validaPermissao('M_Relatorios', $level),
			'adminItems' => $visibleItems,
		];
	}

	private function categoriaForm(array $query): array
	{
		$controller = new categoriaController($this->db);
		$controller->cancelar();
		$controller->excluir();
		$controller->salvar();
		$record = $controller->abrir();
		return array_merge([
			'id' => 0, 'nome' => '', 'descricao' => '', 'imagem_id' => '',
			'nome_img' => '', 'tamanho_img' => '', 'tipo_img' => '', 'imagem' => '',
			'errormsg' => (string) ($query['errormsg'] ?? ''),
		], is_array($record) ? $record : []);
	}

	private function periodoForm(array $query): array
	{
		$controller = new periodoController($this->db);
		$controller->cancelar();
		$controller->excluir();
		$controller->salvar();
		$record = $controller->abrir();
		return array_merge(['id' => 0, 'nome' => '', 'seq' => 0], isset($record[0]) ? $record[0] : []);
	}

	private function salaList(array $query, array $post): array
	{
		$categoryId = (int) Util::clearparam($post['categoria_id'] ?? $query['categoria_id'] ?? 0);
		$activeFilter = (string) Util::clearparam($post['activas'] ?? $query['activas'] ?? 'activas');
		return [
			'categoria_id' => $categoryId,
			'activas' => $activeFilter,
			'categorias' => (new categoriaController($this->db))->listarOpcoesController(),
			'lista' => (new salaController($this->db))->listarcontroller($categoryId, $activeFilter),
		];
	}

	private function salaForm(array $query, array $post): array
	{
		$categoryFilter = Util::clearparam($query['categoria_filtro'] ?? $post['categoria_filtro'] ?? 0);
		$activeFilter = (string) Util::clearparam($query['activas'] ?? $post['activas'] ?? 'activas');
		$controller = new salaController($this->db);
		$controller->cancelar($categoryFilter, $activeFilter);
		$controller->excluir($categoryFilter, $activeFilter);
		$controller->salvar($categoryFilter, $activeFilter);
		$record = $controller->abrir();
		return array_merge([
			'id' => 0, 'nome' => '', 'descricao' => '', 'categoria_id' => $categoryFilter,
			'lugares' => 0, 'activa' => true, 'imagem_id' => '', 'nome_img' => '',
			'tamanho_img' => '', 'tipo_img' => '', 'imagem' => '',
			'categoria_filtro' => $categoryFilter, 'activas_filtro' => $activeFilter,
			'errormsg' => (string) ($query['errormsg'] ?? ''),
		], is_array($record) ? $record : [], [
			'categorias' => (new categoriaController($this->db))->listarOpcoesController(),
		]);
	}

	private function usuarioForm(array $query, array $post): array
	{
		$controller = new UsuarioController($this->db);
		$controller->cancelar();
		$isSelf = (int) ($post['id'] ?? $query['id'] ?? 0) === (int) ($_SESSION['user_id'] ?? 0);
		$deleteResult = $isSelf ? false : $controller->excluir();
		$error = isset($post['excluir']) && is_string($deleteResult)
			? 'Utilizador não pode ser excluído. Existem reservas em seu nome.'
			: '';
		if ($isSelf) {
			$post['nivel'] = (string) ($_SESSION['user_nivel'] ?? 0);
		}
		$saveResult = $controller->salvar($post);
		if ($saveResult === 0) {
			$error = 'Não é possível criar/editar: Nome ou E-mail já existentes no sistema!';
		}

		$record = [];
		if (!isset($post['salvar'])) {
			$opened = $controller->abrir();
			$record = $opened[0] ?? [];
		} else {
			$record = [
				'id' => (int) ($post['id'] ?? 0),
				'nome' => Util::clearparam($post['nome'] ?? ''),
				'email' => Util::clearparam($post['email'] ?? ''),
				'telefone' => Util::clearparam($post['telefone'] ?? ''),
				'NIF' => Util::clearparam($post['NIF'] ?? ''),
				'nivel' => (int) ($post['nivel'] ?? 0),
			];
		}

		$permissionController = new PermissoesController($this->db);
		$maxLevel = min(count($permissionController->niveis) - 1, (int) ($_SESSION['user_nivel'] ?? 0));
		$levelOptions = [];
		for ($level = 0; $level <= $maxLevel; $level++) {
			$levelOptions[] = [
				'value' => $level,
				'label' => $permissionController->nomeNivel($level, 2) . ' (' . $permissionController->nomeNivel($level, 1) . ')',
			];
		}

		return array_merge([
			'id' => 0, 'nome' => '', 'email' => '', 'senha' => '', 'telefone' => '',
			'NIF' => '', 'nivel' => 0, 'errormsg' => $error,
			'levelOptions' => $levelOptions,
			'sessionUserName' => (string) ($_SESSION['user_nome'] ?? ''),
			'sessionUserLevel' => (int) ($_SESSION['user_nivel'] ?? 0),
		], $record);
	}

	private function permissoesForm(): array
	{
		$controller = new permissoesController($this->db);
		$controller->cancelar();
		$controller->salvar();
		return [
			'tabela_n' => $controller->listar('n'),
			'tabela_s' => $controller->listar('s'),
			'nivelLabels' => [
				$controller->nomeNivel(0, 2), $controller->nomeNivel(1, 2),
				$controller->nomeNivel(2, 2), $controller->nomeNivel(3, 2),
			],
		];
	}

	private function reportDates(array $query): array
	{
		$type = in_array($query['tipo'] ?? 'm', ['m', 'w', 'p'], true) ? ($query['tipo'] ?? 'm') : 'm';
		$today = $this->parseCompactDate($query['data'] ?? null) ?? new DateTime();
		$start = $type === 'p' ? ($this->parseCompactDate($query['data_inicio'] ?? null) ?? new DateTime()) : $today;
		$end = $type === 'p' ? ($this->parseCompactDate($query['data_fim'] ?? null) ?? new DateTime()) : null;
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
			'tipo' => $type, 'hoje' => $today, 'inicio' => $start, 'fim' => $end,
			'hoje_pt' => $label, 'dia_anterior' => $previous, 'dia_posterior' => $next,
			'dateFormat' => $type === 'w' ? 'W - Y (M)' : ($type === 'p' ? 'd/m/Y' : 'M Y'),
		];
	}

	private function reportSummary(array $query): array
	{
		$data = $this->reportDates($query);
		$dashboard = new dashboardController($this->db);
		$end = $data['tipo'] === 'p' ? $data['fim'] : null;
		$bookings = $dashboard->disciplinaMaisReservasController($data['hoje'], $data['tipo'], $end);
		$totalBookings = $dashboard->totalReservasController($data['hoje'], $data['tipo'], $end);
		$totalSlots = $dashboard->totalHorariosController($data['tipo'], $data['hoje'], $end);
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

	private function reportReservations(array $query): array
	{
		$data = $this->reportDates($query);
		$dashboard = new dashboardController($this->db);
		$data['reservas'] = $dashboard->listaReservasController(
			$data['hoje'],
			$data['tipo'],
			$data['tipo'] === 'p' ? $data['fim'] : null
		);
		return $data;
	}

	private function reservaForm(array $query): array
	{
		$controller = new ReservaController($this->db);
		$id = filter_var($query['id'] ?? null, FILTER_VALIDATE_INT);
		$id = $id === false || $id === null ? 0 : $id;
		$roomId = filter_var($query['sala_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
		$periodId = filter_var($query['periodo_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
		$record = $id > 0 ? ($controller->abrirController($id)[0] ?? []) : [];
		$date = isset($query['data']) && is_string($query['data'])
			? DateTime::createFromFormat('!d/m/Y', $query['data'])
			: false;
		$date = $date ?: new DateTime();
		$period = new periodoController($this->db);
		$room = new salaController($this->db);
		$record = array_merge([
			'id' => $id, 'sala_id' => $roomId, 'periodo_id' => $periodId,
			'dia' => $date->format('d/m/Y'), 'professor_desc' => '', 'disciplina_desc' => '',
			'observacao' => '', 'status' => 1, 'usuario' => (string) ($_SESSION['user_nome'] ?? ''),
			'sala' => $roomId ? $room->nomeSala($roomId) : '',
			'periodo' => $periodId ? $period->nomePeriodo($periodId) : '',
		], $record);
		$record['options_status'] = $controller->options_status($record['status']);
		return $record;
	}

	private function parseCompactDate($value): ?DateTime
	{
		if (!is_string($value) || !preg_match('/^\d{8}$/', $value)) {
			return null;
		}
		$date = DateTime::createFromFormat('!dmY', $value);
		$errors = DateTime::getLastErrors();
		return $date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
			? $date
			: null;
	}
}
