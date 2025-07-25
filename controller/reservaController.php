<?php

require "../model/reserva.php";
require "util.php";

class ReservaController
{
	

	function abrirController($id)
	{

		$reserva = new Reserva();
		return $reserva->abrir($id);


	}

	function excluirController()
	{
		if (isset($_POST['id'])) {
			// excluir	
			$id = Util::clearparam($_POST['id']);
			$reserva = new Reserva();
			echo $reserva->excluir($id);

		}
	}

	function salvarController()
	{

		// se clicou no botao salvar
		if (isset($_POST['id'])) {

			// receber dados			
			$id = Util::clearparam($_POST['id']);
			$dia = Util::clearparam($_POST['dia']);
			$professor = Util::clearparam($_POST['professor']);
			$disciplina = Util::clearparam($_POST['disciplina']);
			$dia = Util::clearparam($_POST['dia']);
			$observacao = Util::clearparam($_POST['observacao']);
			$status = Util::clearparam($_POST['status']);

			/*
					 $sala= Util::clearparam($_POST['sala_id']);
					 $periodo= Util::clearparam($_POST['periodo_id']);
					 */

			$sala_id = Util::clearparam($_POST['sala_id']);
			$periodo_id = Util::clearparam($_POST['periodo_id']);

			$data = new DateTime();
			$data = date_create_from_format('d/m/Y', $dia);

			$usuario_id = $_SESSION['user_id'];

			$reserva = new Reserva();

			// verificar se data já não está ocupada para um novo registro
			if ($id == 0) {
				$id_existente = $reserva->verificar($data, $sala_id, $periodo_id);
				if (isset($id_existente['id'])) {
					echo "A data selecionada para essa sala nesse período já se encontra ocupada.";
					return false;
				}
			}

			$id = $reserva->salvar(
				$id,
				$dia,
				$professor,
				$disciplina,
				$data,
				$observacao,
				$status,
				$sala_id,
				$periodo_id,
				$usuario_id
			);

			echo $id; // javascript espera esse id para saber se registro inserido está ok


			// verificar repetições:
			$data_final = Util::clearparam($_POST['data_final']);

			if ($data_final != "") {
				$data_final = date_create_from_format('d/m/Y', $data_final);

				// lloping +7 dias . Se data limite ultrapassada, sair.
				$data->modify('+7 day');
				$diff = Util::ndate_diff($data->format("Y-m-d"), $data_final->format("Y-m-d"));

				while ($diff > 0 || $data->format("Y-m-d") == $data_final->format("Y-m-d")) {

					$id_existente = $reserva->verificar($data, $sala_id, $periodo_id);

					if (!isset($id_existente[0]['id'])) {
						// duplicar reserva para a data selecionada.
						$id = 0;
						$reserva->salvar(
							$id,
							$dia,
							$professor,
							$disciplina,
							$data,
							$observacao,
							$status,
							$sala_id,
							$periodo_id,
							$usuario_id
						);
					}

					$data->modify('+7 day');
					$diff = Util::ndate_diff($data->format("Y-m-d"), $data_final->format("Y-m-d"));
				}

			}


		}

	}


	public $a_nomes_status = array("Reservado", "Confirmado", "Cancelado");

	
	function nome_status($status)
	{		
		return $this->a_nomes_status[$status - 1];
	}


	function options_status($select)
	{		
		
		$options_status = '';
		for ($i = 1; $i <= count($this->a_nomes_status); $i++) {
			$selected = (($i == $select) ? ' selected ' : ' ');
			$options_status .= "<option value=\"$i\" $selected > " . $this->nome_status($i) . "</option>";
		}
		return $options_status;
	}

}
?>