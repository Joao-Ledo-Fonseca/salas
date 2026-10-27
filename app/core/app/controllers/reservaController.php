<?php

require_once __DIR__ . "/../models/reserva.php";
require_once __DIR__ . '/util.php';

class ReservaController
	{
	private Database $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}


	function abrirController($id)
		{
		$reserva = new Reserva($this->db);
		return $reserva->abrir($id);
		}

	public function guardar(array $post): string
	{
		$id = (int) Util::clearparam($post['id'] ?? '0');
		$dia = Util::clearparam($post['dia'] ?? '');
		$data = DateTime::createFromFormat('!d/m/Y', $dia);
		$dateErrors = DateTime::getLastErrors();
		if ($data === false || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))) {
			return 'Data inválida.';
		}

		$salaId = (int) Util::clearparam($post['sala_id'] ?? '0');
		$periodoId = (int) Util::clearparam($post['periodo_id'] ?? '0');
		$reserva = new Reserva($this->db);
		if ($id === 0 && $reserva->verificar($data, $salaId, $periodoId)) {
			return 'A data selecionada para essa sala nesse período já se encontra ocupada.';
		}

		$userId = (int) ($_SESSION['user_id'] ?? 0);
		$id = (int) $reserva->salvar(
			$id,
			$dia,
			Util::clearparam($post['professor'] ?? ''),
			Util::clearparam($post['disciplina'] ?? ''),
			$data,
			Util::clearparam($post['observacao'] ?? ''),
			(int) Util::clearparam($post['status'] ?? '1'),
			$salaId,
			$periodoId,
			$userId
		);

		$repeatUntil = trim((string) ($post['data_final'] ?? ''));
		if ($repeatUntil !== '') {
			$endDate = DateTime::createFromFormat('!d/m/Y', Util::clearparam($repeatUntil));
			$dateErrors = DateTime::getLastErrors();
			if ($endDate !== false && ($dateErrors === false || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0))) {
				$repeatDate = clone $data;
				$repeatDate->modify('+7 days');
				while ($repeatDate <= $endDate) {
					if (!$reserva->verificar($repeatDate, $salaId, $periodoId)) {
						$reserva->salvar(
							0,
							$repeatDate->format('d/m/Y'),
							Util::clearparam($post['professor'] ?? ''),
							Util::clearparam($post['disciplina'] ?? ''),
							$repeatDate,
							Util::clearparam($post['observacao'] ?? ''),
							(int) Util::clearparam($post['status'] ?? '1'),
							$salaId,
							$periodoId,
							$userId
						);
					}
					$repeatDate->modify('+7 days');
				}
			}
		}

		return (string) $id;
	}

	public function excluir(array $post): string
	{
		$id = (int) Util::clearparam($post['id'] ?? '0');
		(new Reserva($this->db))->excluir($id);
		return (string) $id;
	}


	public $a_nomes_status = array("Reservado", "Confirmado", "Cancelado");


	function nome_status($status)
		{
		return $this->a_nomes_status[$status - 1];
		}


	function options_status($select)
		{
		$options_status = [];
		for ($i = 1; $i <= count($this->a_nomes_status); $i++) {
			$options_status[] = [
				'value' => $i,
				'nome' => $this->nome_status($i),
				'selected' => ($i == $select),
			];
			}
		return $options_status;
		}


	}
?>
