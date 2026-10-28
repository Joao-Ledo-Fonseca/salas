<?php

require_once __DIR__ . '/util.php';
require_once __DIR__ . "/../models/categoria.php";
require_once __DIR__ . "/../models/periodo.php";
require_once __DIR__ . "/../models/sala.php";
require_once __DIR__ . "/../models/reserva.php";

class dashboardController
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
						}
					elseif ($status[0]['status'] == 2) {
						$reserva['classe'] = 'confirmado';
						}
					elseif ($status[0]['status'] == 3) {
						$reserva['classe'] = 'cancelado';
						}
					else {
						$reserva['classe'] = 'desconhecido';
						}

					$reserva['disciplina'] = $status[0]['disciplina_desc'];
					$reserva['professor'] = $status[0]['professor_desc'];
					$reserva['id'] = $status[0]['id'];
					$reserva['usuario_id'] = $status[0]['usuario_id'];
					}

				if (isset($_SESSION['user_nivel']) && $_SESSION['user_nivel'] == 0) {
					$reserva['can_open'] = (($_SESSION['user_id'] == $reserva['usuario_id']) || ($reserva['usuario_id'] == ''));
					}
				else {
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

	// Lista de todas as reservas 
	function listaReservasController($hoje = null, $tipo = 'm', $fim = null)
		{
		if ($tipo === 'p' && $fim !== null) {
			$rows = $this->reserva->listaReservasPeriodo($hoje, $fim);
			}
		else {
			$rows = $this->reserva->listaReservas($hoje, $tipo);
			}

		return $rows;
		}

	// relatorio de disciplina com mais reservas
	function listarCategoriasController()
		{
		$categoria = new Categoria($this->db);
		return $categoria->listar();
		}

	// 🎯 Alinhado para receber $fim e tratar o período corretamente
	function disciplinaMaisReservasController($hoje = null, $tipo = 'm', $fim = null)
		{
		if ($hoje === null) {
			$hoje = new DateTime();
			}

		$reserva = new Reserva($this->db);
		
		// Encaminha os parâmetros necessários para o modelo
		$row1 = $reserva->disciplinaMaisReservas($hoje, $tipo, $fim);

		return $row1;
		}

	// 🎯 UNIFORMIZAÇÃO DOS HORÁRIOS: Ajusta o multiplicador baseado no intervalo real de dias
	function totalHorariosController($tipo = 'm', $hoje = null, $fim = null)
	{
		$sala = new sala($this->db);
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

	// 🎯 UNIFORMIZAÇÃO DO GRÁFICO: Agora recebe e consome os mesmos dados que a listagem
	function totalReservasController($hoje = null, $tipo = 'm', $fim = null)
	{
		if ($hoje === null) {
			$hoje = new DateTime();
		}

		$reserva = new Reserva($this->db);
		
		// Usamos uma lógica centralizada no modelo para contar as reservas com base nos mesmos filtros
		$rows = $reserva->listaReservasParaRelatorio($hoje, $tipo, $fim);

		return count($rows);
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
?>
