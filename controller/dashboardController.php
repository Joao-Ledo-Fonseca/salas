<?php

require "util.php";
require "../model/periodo.php";
require "../model/sala.php";
require "../model/reserva.php";

class dashboardController
{

	public $reserva = 'null';
	function __construct()
	{

		$this->reserva = new Reserva();
	}

	// function que gera o dashboard da index.
	function gerarTopoController()
	{

		$periodo = new Periodo();

		// montar o topo
		$tabela_topo = '<tr> <th></th> ';

		$periodos = $periodo->listar();

		foreach ($periodos as $periodo) {
			$tabela_topo .= ' <th>' . $periodo['nome'] . '</th>';
		}
		$tabela_topo .= '</tr>';

		return $tabela_topo;

	}

	// gerar o corpo da index
	function gerarCorpoController($hoje)
	{

		$sl = new sala();
		$per = new Periodo();
		//$reserva = new Reserva();


		$tabela_corpo = '';

		$salas = $sl->listar();

		// para cada sala
		foreach ($salas as $sala) {
			if ($sala['activa']) {
				$tabela_corpo .= ' <tr><td> ' . $sala['nome'] . '</td> ';

				// para cada periodo
				$periodos = $per->listar();

				foreach ($periodos as $periodo) {

					$disciplina_reserva = '';
					$professores_reserva = '';
					$id_usuario = '';

					// checar se esse periodo, nessa sala, nesse dia,  está ocupada.

					$status = $this->reserva->verificarCompleto($hoje, $sala['id'], $periodo['id']);

					if (isset($status[0]['id'])) {
						if ($status[0]['status'] == 1)
							$css_ocupado = 'reservado';
						else if ($status[0]['status'] == 2)
							$css_ocupado = 'confirmado';
						else if ($status[0]['status'] == 3)
							$css_ocupado = 'cancelado';

						$disciplina_reserva = $status[0]['disciplina_desc'];
						$professores_reserva = $status[0]['professor_desc'];
						$id_reserva = $status[0]['id'];
						$id_usuario = $status[0]['usuario_id'];
						// procurar por disciplinas e professores dessa reserva

					} else {
						$id_reserva = 0;
						$css_ocupado = 'disponivel';
					}

					// Se for um usuario com nivel usuario, só pode alterar as reservas próprias
					// Os restantes podem alterar todas as reservas				
					if ($_SESSION['user_nivel'] == 0) {
						$accao = ((($_SESSION["user_id"] == $id_usuario) || ($id_usuario == "")) ? 'onClick="abreReserva(this)"' : '""');
					} else {
						$accao = 'onClick="abreReserva(this)"';
					}

					$tabela_corpo .= '<td ' . $accao . ' class="' . $css_ocupado . '" id="' . $id_reserva . '" sala="' . $sala['id']
						. '" periodo="' . $periodo['id'] . '" usuario_id="' . $id_usuario . '" > 
					' . $disciplina_reserva . '&nbsp;<hr>&nbsp;' . $professores_reserva . ' </td>';

				}
			}

		}

		$tabela_corpo .= ' </tr>';


		return $tabela_corpo;


	}

	// Lista de todas as reservas 
	function listaReservasController($hoje = null, $tipo = 'm')
	{

		// $reserva = new Reserva();
		$rows = $this->reserva->listaReservas($hoje, $tipo);

		$tabela = '';
		foreach ($rows as $row) {
			// $dia = date_create_from_format('Y-m-d', $row['dia'])->format('d/m/Y');
			$tabela .=
				'<tr>	
			<td>' . $row['categoria'] . '</td> 		
			<td>' . $row['sala'] . '</td>
			<td width="300">' . $row['disciplina_desc'] . ' </td>
			<td><a href=index.php?data=' . urlencode(date_create_from_format('Y-m-d', $row['dia'])->format('D d/m/Y')) . '>' . date_create_from_format('Y-m-d', $row['dia'])->format('d-m-Y') . '</a> </td>
			<td>' . $row['periodo'] . '</td>
			<td>' . $row['status_nome'] . '</td>
			</tr>';
		}

		return $tabela;
	}



	// relatorio de disciplina com mais reservas
	function disciplinaMaisReservasController()
	{
		$reserva = new Reserva();
		$row1 = $reserva->disciplinaMaisReservas();

		$tabela = '';
		foreach ($row1 as $row) {
			$tabela .= '<tr>
			<td></td>
			<td width="300">' . $row['disciplina_desc'] . ' </td>
			<td>' . $row['total'] . ' </td>
			
				</tr>';
		}

		return $tabela;
	}



	function totalHorariosController()
	{

		$total_horarios = 0;

		$sala = new sala();
		$periodo = new Periodo();


		// Total de salas * total de horarios * 30 dias
		$salas = $sala->total();
		$periodos = $periodo->total();

		$total_horarios = $salas[0]['total'] * $periodos[0]['total'] * 30;
		return $total_horarios;

	}


	function totalReservasController()
	{
		// $reserva = new Reserva();
		$hoje = new DateTime();

		$reservas = $this->reserva->totalReservasMes($hoje);

		$total_reservas = $reservas[0]['total'];

		return $total_reservas;

	}


	function prevController($dia)
	{

		// $reserva = new Reserva();
		return $this->reserva->prev($dia);

	}

	function nextController($dia)
	{

		// $reserva = new Reserva();
		return $this->reserva->next($dia);

	}

}

?>