<?php
namespace Salas\Controllers;



use Salas\Core\Auth\AuthContext;
use Salas\Core\Persistence\Database;
use Salas\Models\Reserva;
use Salas\Models\Uso;
use Salas\Core\Validation\Validator;

class ReservaController
	{
	private Database $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}


	public function abrirController($id)
		{
		$reserva = new Reserva($this->db);
		return $reserva->abrir($id);
		}

	public function salvar(array $post): string
	{
		$id = (int) Validator::string($post['id'] ?? '0');
		$dia = Validator::string($post['dia'] ?? '');

		$userId = (new AuthContext())->id();
		$usoId = (int) Validator::int($post['uso_id'] ?? 0);
		if (trim(Validator::string($post['entidade'] ?? '')) === '') {
			return 'Preencha a entidade.';
		}
		if ($usoId <= 0) {
			return 'Selecione um uso.';
		}

		$data = \DateTime::createFromFormat('!d/m/Y', $dia);
		$dateErrors = \DateTime::getLastErrors();
		if ($data === false || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))) {
			return 'Data inválida.';
		}

		$salaId = (int) Validator::string($post['sala_id'] ?? '0');
		$periodoId = (int) Validator::string($post['periodo_id'] ?? '0');
		$periodosRaw = isset($post['periodos']) && is_string($post['periodos']) ? $post['periodos'] : '';
		$periodosArr = array_values(array_filter(array_map('intval', explode(',', $periodosRaw))));
		if (empty($periodosArr)) {
			$periodosArr = [$periodoId];
		}
		$reserva = new Reserva($this->db);
		foreach ($periodosArr as $pId) {
			if ($id === 0 && $reserva->verificar($data, $salaId, $pId)) {
				return 'A data selecionada para essa sala nesse período já se encontra ocupada.';
			}
		}

		if ($usoId > 0) {
			$usoRecord = (new Uso($this->db))->abrir($usoId);
		}
		$lastId = $id;
		foreach ($periodosArr as $pId) {
			$lastId = (int) $reserva->salvar(
				$id === 0 ? 0 : $id,
				$dia,
				Validator::string($post['entidade'] ?? ''),
				$data,
				Validator::string($post['observacao'] ?? ''),
				(int) Validator::string($post['status'] ?? '1'),
				$salaId,
				$pId,
				$userId,
				$usoId > 0 ? $usoId : null
			);
		}
		$id = $lastId;

		$repeatUntil = trim((string) ($post['data_final'] ?? ''));
		if ($repeatUntil !== '') {
			$endDate = \DateTime::createFromFormat('!d/m/Y', Validator::string($repeatUntil));
			$dateErrors = \DateTime::getLastErrors();
			if ($endDate !== false && ($dateErrors === false || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0))) {
				foreach ($periodosArr as $pId) {
				$repeatDate = clone $data;
				$repeatDate->modify('+7 days');
				while ($repeatDate <= $endDate) {
					if (!$reserva->verificar($repeatDate, $salaId, $pId)) {
						$reserva->salvar(
							0,
							$repeatDate->format('d/m/Y'),
							Validator::string($post['entidade'] ?? ''),
							$repeatDate,
							Validator::string($post['observacao'] ?? ''),
							(int) Validator::string($post['status'] ?? '1'),
							$salaId,
							$pId,
							$userId,
							$usoId > 0 ? $usoId : null
						);
					}
					$repeatDate->modify('+7 days');
				}
				}
			}
		}

		return (string) $id;
	}

	public function excluir(array $post): string
	{
		$id = (int) Validator::string($post['id'] ?? '0');
		(new Reserva($this->db))->excluir($id);
		return (string) $id;
	}


	public $a_nomes_status = array("Reservado", "Confirmado", "Cancelado");


	public function nomeStatus($status)
		{
		return $this->a_nomes_status[$status - 1];
		}


	public function options_status($select)
		{
		$options_status = [];
		for ($i = 1; $i <= count($this->a_nomes_status); $i++) {
			$options_status[] = [
				'value' => $i,
				'nome' => $this->nomeStatus($i),
				'selected' => ($i == $select),
			];
			}
		return $options_status;
		}


    public function reservaForm(array $query): array
    {
        $controller = $this;
        $id = filter_var($query['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id === null) {
            $id = 0;
        }
        $roomId = filter_var($query['sala_id'] ?? null, FILTER_VALIDATE_INT);
        if ($roomId === false || $roomId === null) {
            $roomId = 0;
        }
        $periodId = filter_var($query['periodo_id'] ?? null, FILTER_VALIDATE_INT);
        if ($periodId === false || $periodId === null) {
            $periodId = 0;
        }
        $periodosRaw = isset($query['periodos']) && is_string($query['periodos']) ? $query['periodos'] : '';
        $periodosArr = array_filter(array_map('intval', explode(',', $periodosRaw)));
        $periodos = implode(',', $periodosArr);
        if ($id > 0) {
            $opened = $controller->abrirController($id);
            $record = (isset($opened[0]) && is_array($opened[0])) ? $opened[0] : [];
        } else {
            $record = [];
        }
        $date = null;
        if (isset($query['data']) && is_string($query['data'])) {
            $date = \DateTime::createFromFormat('!d/m/Y', $query['data']);
        }
        if (!($date instanceof \DateTime)) {
            $date = new \DateTime();
        }
        $period = new PeriodoController($this->db);
        $room = new SalaController($this->db);
        $record = array_merge([
            'id' => $id,
            'sala_id' => $roomId,
            'periodo_id' => $periodId,
            'dia' => $date->format('d/m/Y'),
            'entidade_desc' => '',
            'observacao' => '',
            'status' => 1,
            'usuario' => (new AuthContext())->name(),
            'sala' => $roomId ? $room->nomeSala($roomId) : '',
            'periodo' => $periodId ? $period->nomePeriodo($periodId) : '',
            'periodos' => $periodos,
        ], $record);
        $record['options_status'] = $controller->options_status((int) $record['status']);
        $record['usos'] = (new UsoController($this->db))->listarOpcoesController();
        $record['uso_id'] = isset($record['uso_id']) ? (int) $record['uso_id'] : 0;
		return $record;
	}

}
?>
