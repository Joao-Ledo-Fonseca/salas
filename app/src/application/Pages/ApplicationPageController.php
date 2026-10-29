<?php
namespace Salas\Application\Pages;


use Salas\Infrastructure\Auth\AuthContext;
use Salas\Application\AuthorizationMiddleware;
use Salas\Application\Services\AuthorizationService;
use Salas\Controllers\CategoriaController;
use Salas\Infrastructure\Support\Csrf;
use Salas\Infrastructure\Persistence\Database;
use Salas\Models\Imagem;
use Salas\Controllers\PeriodoController;
use Salas\Controllers\PermissoesController;
use Salas\Controllers\RelatorioController;
use Salas\Models\Reserva;
use Salas\Controllers\ReservaController;
use Salas\Models\Sala;
use Salas\Controllers\SalaController;
use Salas\Controllers\UsoController;
use Salas\Controllers\UsuarioController;
use Salas\Infrastructure\Validation\Validator;

require_once __DIR__ . '/DashboardPageController.php';

class ApplicationPageController
{
	private Database $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}


	public function dispatch(string $action, array $query, array $post): array
	{
		if ($post !== [] && !Csrf::validate($post['csrf_token'] ?? null)) {
			http_response_code(403);
			exit('CSRF token inválido.');
		}
		(new AuthorizationMiddleware($this->db))->authorize($action, $query, $post);
		if ($action === 'reserva_form' && isset($post['submeter_excluir'])) {
			return ['response' => (new ReservaController($this->db))->excluir($post)];
		}
		if ($action === 'reserva_form' && isset($post['submeter_salvar'])) {
			return ['response' => (new ReservaController($this->db))->guardar($post)];
		}

		$data = ['menuData' => $this->menuData()];

		switch ($action) {
			case 'categoria_list':
				$data['lista'] = (new CategoriaController($this->db))->listarController();
				$data['_layout'] = 'layout.php';
				break;
			case 'categoria_form':
				$data += $this->categoriaForm($query, $post);
				$data['_layout'] = 'layout.php';
				break;
			case 'uso_list':
				$data['lista'] = (new UsoController($this->db))->listarController();
				$data['_layout'] = 'layout.php';
				break;
			case 'uso_form':
				$data += $this->usoForm($query, $post);
				$data['_layout'] = 'layout.php';
				break;
			case 'periodo_list':
				$data['lista'] = (new PeriodoController($this->db))->listarController();
				$data['_layout'] = 'layout.php';
				break;
			case 'periodo_form':
				$data += $this->periodoForm($query, $post);
				$data['_layout'] = 'layout.php';
				break;
			case 'sala_list':
				$data += $this->salaList($query, $post);
				$data['_layout'] = 'layout.php';
				break;
			case 'sala_form':
				$data += $this->salaForm($query, $post);
				$data['_layout'] = 'layout.php';
				break;
			case 'usuario_list':
				$data['lista'] = (new UsuarioController($this->db))->listarController();
				$data['_layout'] = 'layout.php';
				$permissionController = new AuthorizationService($this->db);
				$users = $data['lista'];
				foreach (array_keys($users) as $i) {
					$user = $users[$i];
					$user['nivel_nome'] = $permissionController->nomeNivel((int) $user['nivel'], 2);
					$users[$i] = $user;
				}
				$data['lista'] = $users;
				break;
			case 'usuario_form':
				$data += $this->usuarioForm($query, $post);
				$data['_layout'] = 'layout.php';
				break;
			case 'permissoes_form':
				$data += $this->permissoesForm($post);
				$data['_layout'] = 'layout.php';
				break;
			case 'estatisticas':
				$data += $this->reportSummary($query);
				break;
			case 'relatorios':
				$data += $this->reportReservations($query);
				break;
			case 'reserva_form':
				$data += $this->reservaForm($query);
				break;
			default:
				throw new RuntimeException('Ação não suportada.');
		}

		$routes = include __DIR__ . '/../../config/routes.php';
		return ['view' => $routes[$action], 'data' => $data];
	}

	public function dashboard(array $query): array
	{
		(new AuthorizationMiddleware($this->db))->authorize('dashboard', $query, []);
		return [
			'dashboardView' => (new DashboardPageController($this->db))->index($query),
			'menuData' => $this->menuData(),
		];
	}


	private function menuData(): array
	{
		$permissions = new AuthorizationService($this->db);
		$auth = new AuthContext();
		$level = $auth->level();
		$items = [
			['permission' => 'M_Categorias', 'label' => 'Categorias', 'url' => '?acao=categoria_list'],
			['permission' => 'M_Usos', 'label' => 'Usos', 'url' => '?acao=uso_list'],
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
			'userName' => $auth->name(),
			'userLevel' => $permissions->nomeNivel($level, 1),
			'profileUrl' => app_url('?acao=usuario_form&id=' . $auth->id()),
			'showStatistics' => $permissions->validaPermissao('M_Estatisticas', $level),
			'showReports' => $permissions->validaPermissao('M_Relatorios', $level),
			'adminItems' => $visibleItems,
		];
	}

	private function categoriaForm(array $query, array $post = []): array
	{
		$controller = new CategoriaController($this->db);
		if ($post !== []) {
			$controller->cancelar();
			$controller->excluir();
			$controller->salvar();
		}
		$record = $controller->abrir();
		$targetId = (int) ($query['id'] ?? 0);
		$salasCategoria = [];
		if ($targetId > 0) {
			$salasCategoria = (new SalaController($this->db))->listarSalasCategoria($targetId);
		}
		return array_merge([
			'id' => 0,
			'nome' => '',
			'descricao' => '',
			'imagem_id' => '',
			'nome_img' => '',
			'tamanho_img' => '',
			'tipo_img' => '',
			'imagem' => '',
			'errormsg' => (string) ($query['errormsg'] ?? ''),
			'salasCategoria' => $salasCategoria,
		], is_array($record) ? $record : []);
	}

	private function usoForm(array $query, array $post = []): array
	{
		$controller = new UsoController($this->db);
		if ($post !== []) {
			$controller->cancelar();
			$controller->excluir();
			$controller->salvar();
		}
		$record = $controller->abrir();
		return array_merge([
			'id' => 0,
			'nome' => '',
			'descricao' => '',
			'errormsg' => (string) ($query['errormsg'] ?? ''),
		], is_array($record) ? $record[0] ?? [] : []);
	}

	private function periodoForm(array $query, array $post = []): array
	{
		$controller = new PeriodoController($this->db);
		if ($post !== []) {
			$controller->cancelar();
			$controller->excluir();
			$controller->salvar();
		}
		$record = $controller->abrir();
		return array_merge(['id' => 0, 'nome' => '', 'seq' => 0], isset($record[0]) ? $record[0] : []);
	}

	private function salaList(array $query, array $post): array
	{
		$categoryId = (int) Validator::string($post['categoria_id'] ?? $query['categoria_id'] ?? 0);
		$activeFilter = (string) Validator::string($post['activas'] ?? $query['activas'] ?? 'activas');
		return [
			'categoria_id' => $categoryId,
			'activas' => $activeFilter,
			'categorias' => (new CategoriaController($this->db))->listarOpcoesController(),
			'lista' => (new SalaController($this->db))->listarController($categoryId, $activeFilter),
		];
	}

	private function salaForm(array $query, array $post): array
	{
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
		$categoryFilter = Validator::string($query['categoria_filtro'] ?? $post['categoria_filtro'] ?? 0);
		$activeFilter = (string) Validator::string($query['activas'] ?? $post['activas'] ?? 'activas');
		$returnTo = Validator::string($query['return_to'] ?? $post['return_to'] ?? '');
		$returnId = (int) Validator::string($query['return_id'] ?? $post['return_id'] ?? 0);
		$controller = new SalaController($this->db);
		$auth = new AuthContext();
		$reservasSala = [];
		$podeEliminarReservas = (new AuthorizationService($this->db))->validaPermissao('M_EliminarReservasUtilizador', $auth->level());
		$error = '';
		if (isset($post['eliminar_reservas']) && $podeEliminarReservas) {
			$targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
			$controller->eliminarReservasSala($targetId);
			$error = 'Reservas da sala eliminadas com sucesso.';
		}
		if (isset($post['eliminar_reserva'])) {
			$reservaId = (int) ($post['reserva_id'] ?? 0);
			if ($reservaId > 0 && $podeEliminarReservas) {
				(new \Reserva($this->db))->excluir($reservaId);
				$error = 'Reserva eliminada com sucesso.';
			}
		}
		if (isset($post['eliminar_imagem'])) {
			$imageId = (int) ($post['imagem_id'] ?? 0);
			if ($imageId > 0) {
				$controller->eliminarImagem($imageId);
				$error = 'Imagem eliminada com sucesso.';
			}
		}
		if ($post !== []) {
			$controller->cancelar($categoryFilter, $activeFilter, $returnTo, $returnId);
			$deleteResult = $controller->excluir($categoryFilter, $activeFilter, $returnTo, $returnId);
			if ($deleteResult === false) {
				$error = 'Sala não pode ser excluída. Existem reservas associadas.';
			}
			$controller->salvar($categoryFilter, $activeFilter, $returnTo, $returnId);
		}
		$record = $controller->abrir();
		$targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
		if ($targetId > 0) {
			$reservasSala = $controller->listarReservasSala($targetId);
		}
		return array_merge([
			'id' => 0,
			'nome' => '',
			'descricao' => '',
			'categoria_id' => $categoryFilter,
			'lugares' => 0,
			'activa' => true,
			'imagem_id' => '',
			'nome_img' => '',
			'tamanho_img' => '',
			'tipo_img' => '',
			'imagem' => '',
			'categoria_filtro' => $categoryFilter,
			'activas_filtro' => $activeFilter,
			'errormsg' => $error !== '' ? $error : (string) ($query['errormsg'] ?? ''),
			'reservasSala' => $reservasSala,
			'podeEliminarReservas' => $podeEliminarReservas,
			'return_to' => $returnTo,
			'return_id' => $returnId,
		], is_array($record) ? $record : [], [
			'categorias' => (new CategoriaController($this->db))->listarOpcoesController(),
		]);
	}

	private function usuarioForm(array $query, array $post): array
	{
		$controller = new UsuarioController($this->db);
		if ($post !== []) {
			$controller->cancelar();
		}
		$auth = new AuthContext();
		$reservasUsuario = [];
		$podeEliminarReservas = (new AuthorizationService($this->db))->validaPermissao('M_EliminarReservasUtilizador', $auth->level());
		$error = '';
		$isSelf = (int) ($post['id'] ?? $query['id'] ?? 0) === $auth->id();
		if (isset($post['eliminar_reservas']) && !$isSelf && $podeEliminarReservas) {
			$targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
			$controller->eliminarReservasUsuario($targetId);
			$error = 'Reservas do utilizador eliminadas com sucesso.';
		}
		if (isset($post['eliminar_reserva'])) {
			$reservaId = (int) ($post['reserva_id'] ?? 0);
			if ($reservaId > 0 && ($podeEliminarReservas || $isSelf)) {
				(new \Reserva($this->db))->excluir($reservaId);
				$error = 'Reserva eliminada com sucesso.';
			}
		}

		$deleteResult = ($post !== [] && !$isSelf) ? $controller->excluir() : false;
		$error = isset($post['excluir']) && is_string($deleteResult)
			? 'Utilizador não pode ser excluído. Existem reservas em seu nome.'
			: $error;

		if (isset($post['excluir']) && is_string($deleteResult)) {
			$targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
			$reservasUsuario = $controller->listarReservasUsuario($targetId);
		}
		if ($isSelf && $post !== []) {
			$post['nivel'] = (string) $auth->level();
		}
		$saveResult = $post !== [] ? $controller->salvar($post) : null;
		if ($saveResult === 0) {
			$error = 'Não é possível criar/editar: Nome ou E-mail já existentes no sistema!';
		} elseif ($saveResult !== null && isset($post['salvar'])) {
			header('Location: ' . app_url('?acao=usuario_list'));
			exit;
		}

		$record = [];
		if (!isset($_GET['id']) && $targetId > 0) {
			$_GET['id'] = $targetId;
		}
		if (!isset($post['salvar'])) {
			$opened = $controller->abrir();
			$record = $opened[0] ?? [];
			if (empty($record) && isset($post['id'])) {
				$record = [
					'id' => Validator::int($post['id'] ?? 0),
					'nome' => Validator::string($post['nome'] ?? ''),
					'email' => Validator::string($post['email'] ?? ''),
					'telefone' => Validator::string($post['telefone'] ?? ''),
					'NIF' => Validator::string($post['NIF'] ?? ''),
					'nivel' => Validator::int($post['nivel'] ?? 0),
				];
			}
		} else {
			$record = [
				'id' => (int) ($post['id'] ?? 0),
				'nome' => Validator::string($post['nome'] ?? ''),
				'email' => Validator::string($post['email'] ?? ''),
				'telefone' => Validator::string($post['telefone'] ?? ''),
				'NIF' => Validator::string($post['NIF'] ?? ''),
				'nivel' => (int) ($post['nivel'] ?? 0),
			];
		}

		$targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
		if ($targetId > 0) {
			$reservasUsuario = $controller->listarReservasUsuario($targetId);
		}

		$permissionController = new AuthorizationService($this->db);
		$maxLevel = min(count($permissionController->niveis) - 1, (int) ($_SESSION['user_nivel'] ?? 0));
		$levelOptions = [];
		for ($level = 0; $level <= $maxLevel; $level++) {
			$levelOptions[] = [
				'value' => $level,
				'label' => $permissionController->nomeNivel($level, 2) . ' (' . $permissionController->nomeNivel($level, 1) . ')',
			];
		}

		return array_merge([
			'id' => 0,
			'nome' => '',
			'email' => '',
			'senha' => '',
			'telefone' => '',
			'NIF' => '',
			'nivel' => 0,
			'errormsg' => $error,
			'reservasUsuario' => $reservasUsuario,
			'podeEliminarReservas' => $podeEliminarReservas,
			'levelOptions' => $levelOptions,
			'sessionUserName' => (string) ($_SESSION['user_nome'] ?? ''),
			'sessionUserLevel' => (int) ($_SESSION['user_nivel'] ?? 0),
		], $record);
	}

	private function permissoesForm(array $post = []): array
	{
		$controller = new PermissoesController($this->db);
		if ($post !== []) {
			$controller->cancelar();
			$controller->salvar();
		}
		return [
			'tabela_n' => $controller->listar('n'),
			'tabela_s' => $controller->listar('s'),
			'nivelLabels' => [
				$controller->nomeNivel(0, 2),
				$controller->nomeNivel(1, 2),
				$controller->nomeNivel(2, 2),
				$controller->nomeNivel(3, 2),
			],
		];
	}

	private function reportDates(array $query): array
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

	private function reportSummary(array $query): array
	{
		$data = $this->reportDates($query);
		$relatorio = new RelatorioController($this->db);
		$end = $data['tipo'] === 'p' ? $data['fim'] : null;
		$bookings = $relatorio->disciplinaMaisReservasController($data['hoje'], $data['tipo'], $end);
		$totalBookings = $relatorio->totalReservasController($data['hoje'], $data['tipo'], $end);
		$totalSlots = $relatorio->totalHorariosController($data['tipo'], $data['hoje'], $end);
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
		$relatorio = new RelatorioController($this->db);
		$data['reservas'] = $relatorio->listaReservasController(
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
		$periodosRaw = isset($query['periodos']) && is_string($query['periodos']) ? $query['periodos'] : '';
		$periodosArr = array_filter(array_map('intval', explode(',', $periodosRaw)));
		$periodos = implode(',', $periodosArr);
		$record = $id > 0 ? ($controller->abrirController($id)[0] ?? []) : [];
		$date = isset($query['data']) && is_string($query['data'])
			? \DateTime::createFromFormat('!d/m/Y', $query['data'])
			: false;
		$date = $date ?: new \DateTime();
		$period = new PeriodoController($this->db);
		$room = new SalaController($this->db);
		$record = array_merge([
			'id' => $id,
			'sala_id' => $roomId,
			'periodo_id' => $periodId,
			'dia' => $date->format('d/m/Y'),
			'professor_desc' => '',
			'disciplina_desc' => '',
			'observacao' => '',
			'status' => 1,
			'usuario' => (string) ($_SESSION['user_nome'] ?? ''),
			'sala' => $roomId ? $room->nomeSala($roomId) : '',
			'periodo' => $periodId ? $period->nomePeriodo($periodId) : '',
			'periodos' => $periodos,
		], $record);
		$record['options_status'] = $controller->options_status($record['status']);
		$record['usos'] = (new UsoController($this->db))->listarOpcoesController();
		$record['uso_id'] = isset($record['uso_id']) ? (int) $record['uso_id'] : 0;
		return $record;
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
