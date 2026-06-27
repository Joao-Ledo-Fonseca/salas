<?php

require "util.php";
require "../model/categoria.php";
require "../model/periodo.php";
require "../model/sala.php";
require "../model/reserva.php";

class dashboardController
{

	public $reserva;

	function __construct()
	{
		$this->reserva = new Reserva();
	}

	private function escape($value)
	{
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	}

	// function que gera o dashboard da index.
	function gerarTopoController()
	{
		$periodo = new Periodo();

		// montar o topo
		$tabela_topo = '<tr><th>Sala</th>';

		$periodos = $periodo->listar();

		foreach ($periodos as $periodo) {
			$tabela_topo .= '<th>' . $this->escape($periodo['nome']) . '</th>';
		}
		$tabela_topo .= '</tr>';

		return $tabela_topo;
	}

	// gerar o corpo da index
	function gerarCorpoController($hoje, $categoria_id = 0)
	{
		$sl = new sala();
		$per = new Periodo();

		$tabela_corpo = '';
		$salas = $sl->listar($categoria_id);
		$periodos = $per->listar();

		// para cada sala
		foreach ($salas as $sala) {
			$tabela_corpo .= '<tr><td>' . $this->escape($sala['nome']) . '</td>';

			foreach ($periodos as $periodo) {
				$disciplina_reserva = '';
				$professores_reserva = '';
				$id_usuario = '';
				$id_reserva = 0;
				$css_ocupado = 'disponivel';

				// checar se esse periodo, nessa sala, nesse dia, está ocupada.
				$status = $this->reserva->verificarCompleto($hoje, $sala['id'], $periodo['id']);

				if (!empty($status) && isset($status[0]['id'])) {
					if ($status[0]['status'] == 1) {
						$css_ocupado = 'reservado';
					} elseif ($status[0]['status'] == 2) {
						$css_ocupado = 'confirmado';
					} elseif ($status[0]['status'] == 3) {
						$css_ocupado = 'cancelado';
					} else {
						$css_ocupado = 'desconhecido';
					}

					$disciplina_reserva = $status[0]['disciplina_desc'];
					$professores_reserva = $status[0]['professor_desc'];
					$id_reserva = $status[0]['id'];
					$id_usuario = $status[0]['usuario_id'];
				}

				if (isset($_SESSION['user_nivel']) && $_SESSION['user_nivel'] == 0) {
					$accao = ((($_SESSION['user_id'] == $id_usuario) || ($id_usuario == '')) ? 'onClick="abreReserva(this)"' : '');
				} else {
					$accao = 'onClick="abreReserva(this)"';
				}

				$tabela_corpo .= '<td ' . $accao . ' class="' . $this->escape($css_ocupado) . '" id="' . $this->escape($id_reserva) . '" data-sala="' . $this->escape($sala['id']) . '" data-periodo="' . $this->escape($periodo['id']) . '" data-usuario_id="' . $this->escape($id_usuario) . '">' . $this->escape($disciplina_reserva) . '&nbsp;<hr>&nbsp;' . $this->escape($professores_reserva) . '</td>';
			}

			$tabela_corpo .= '</tr>';
		}

		return $tabela_corpo;
	}

	// Lista de todas as reservas 
	function listaReservasController($hoje = null, $tipo = 'm')
	{
		if ($tipo === 'p') {
			// Espera-se que os parâmetros data_inicio e data_fim venham em dmY (ddmmyyyy)
			if (isset($_GET['data_inicio']) && isset($_GET['data_fim'])) {
				$inicio = date_create_from_format('dmY', $_GET['data_inicio']);
				$fim = date_create_from_format('dmY', $_GET['data_fim']);
				if ($inicio && $fim) {
					$rows = $this->reserva->listaReservasPeriodo($inicio, $fim);
				} else {
					$rows = array();
				}
			} else {
				$rows = array();
			}
		} else {
			$rows = $this->reserva->listaReservas($hoje, $tipo);
		}

		if (empty($rows)) {
			return '';
		}

		$tabela = '';
		foreach ($rows as $row) {
			$dia = date_create_from_format('Y-m-d', $row['dia']);
			$dia_formatado = $dia ? $dia->format('d/m/Y') : '';
			$href_data = $dia_formatado ? urlencode($dia_formatado) : '';

			$tabela .= '<tr>'
				. '<td>' . $this->escape($row['categoria']) . '</td>'
				. '<td>' . $this->escape($row['sala']) . '</td>'
				. '<td width="300">' . $this->escape($row['disciplina_desc']) . '</td>'
				. '<td><a href="index.php?data=' . $href_data . '">' . $this->escape($dia_formatado) . '</a></td>'
				. '<td>' . $this->escape($row['periodo']) . '</td>'
				. '<td>' . $this->escape($row['status_nome']) . '</td>'
				. '</tr>';
		}

		return $tabela;
	}

	// relatorio de disciplina com mais reservas
	function listarCategoriasController()
	{
		$categoria = new Categoria();
		return $categoria->listar();
	}

	function disciplinaMaisReservasController()
	{
		$reserva = new Reserva();
		$row1 = $reserva->disciplinaMaisReservas();

		$tabela = '';
		foreach ($row1 as $row) {
			$tabela .= '<tr>'
				. '<td></td>'
				. '<td width="300">' . $this->escape($row['disciplina_desc']) . '</td>'
				. '<td>' . $this->escape($row['total']) . '</td>'
				. '</tr>';
		}

		return $tabela;
	}

	function totalHorariosController()
	{
		$sala = new sala();
		$periodo = new Periodo();

		$salas = $sala->total();
		$periodos = $periodo->total();

		return ($salas[0]['total'] * $periodos[0]['total'] * 30);
	}

	function totalReservasController()
	{
		$hoje = new DateTime();
		$reservas = $this->reserva->totalReservasMes($hoje);

		return $reservas[0]['total'];
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