<?php
namespace Salas\Models;

use Salas\Core\Persistence\Database;

/**
 * Classe Reserva.
 */
class Reserva
	{

	private $db = null;

	/**
	 * Método __construct.
	 * @param Database $db
	 * @return void
	 */
	public function __construct(Database $db)
		{
		$this->db = $db;
		}


	/**
	 * Método abrir.
	 * @param mixed $id
	 * @return mixed
	 */
	public function abrir($id)
		{
		$sql = ' select 
		a.id
		,a.sala_id
		,c.nome as sala
		,a.periodo_id
		,b.nome as periodo
		,a.entidade_desc
		,a.status
		,a.observacao
		,a.uso_id
		,date_format(a.dia,"%d/%m/%Y") as dia
		,d.nome as usuario
					
	 	from reserva a
	 
	 	inner join periodo b on
	 	a.periodo_id = b.id
	 
	 	inner join sala c on
	 	a.sala_id = c.id
	 
		inner join usuario d on
	 	a.usuario_id = d.id

	 	where a.id = ?';

		return $this->db->query($sql, [$id]);

		}



	/**
	 * Método verificar.
	 * @param mixed $data
	 * @param mixed $sala
	 * @param mixed $periodo
	 * @return mixed
	 */
	public function verificar($data, $sala, $periodo)
		{

		$sql = 'select a.id
	 			from reserva a
	 			where a.sala_id = ?
	 			and a.periodo_id = ?
	 			and dia = ?';

		return $this->db->query($sql, [$sala, $periodo, $data->format("Y-m-d")]);

		}


	/**
	 * Método verificarCompleto.
	 * @param mixed $data
	 * @param mixed $sala
	 * @param mixed $periodo
	 * @return mixed
	 */
	public function verificarCompleto($data, $sala, $periodo)
		{
		$sql = 'select * from reserva where sala_id = ? and periodo_id = ? and dia = ?';
		$resultado = $this->db->query($sql, [$sala, $periodo, $data->format("Y-m-d")]);

		return $resultado;
		}

	/**
	 * Método listarPorDia.
	 * @param \DateTime $hoje
	 * @param int $categoria_id
	 * @param int $usuario_id
	 * @return array
	 */
	public function listarPorDia(\DateTime $hoje, int $categoria_id = 0, int $usuario_id = 0): array
		{
		$sql = 'select r.*, s.nome as sala_nome, s.categoria_id, p.nome as periodo_nome, u.nome as uso_nome
			   from reserva r
			   inner join sala s on r.sala_id = s.id
			   inner join periodo p on r.periodo_id = p.id
			   left join uso u on r.uso_id = u.id
			   where r.dia = ? and (? = 0 or s.categoria_id = ?) and (? = 0 or r.usuario_id = ?)';
		$rows = $this->db->query($sql, [
			$hoje->format('Y-m-d'),
			$categoria_id, $categoria_id,
			$usuario_id, $usuario_id,
		]);
		$map = [];
		foreach ($rows as $r) {
			$map[(int)$r['sala_id']][(int)$r['periodo_id']] = $r;
		}
		return $map;
		}


	/**
	 * Método excluir.
	 * @param mixed $id
	 * @return mixed
	 */
	public function excluir($id)
		{
		$sql = 'delete from reserva where id = ?';
		$this->db->query_update($sql, [$id]);

		return 0;
		}

	/**
	 * Método salvar.
	 * @param mixed $id
	 * @param mixed $dia
	 * @param mixed $entidade
	 * @param mixed $data
	 * @param mixed $observacao
	 * @param mixed $status
	 * @param mixed $sala
	 * @param mixed $periodo
	 * @param mixed $usuario
	 * @param mixed $uso_id
	 * @return mixed
	 */
	public function salvar($id, $dia, $entidade, $data, $observacao, $status, $sala, $periodo, $usuario, $uso_id = null)
		{

		// atualizar
		if ($id > 0) {
			$sql = 'update reserva set
			dia = ?, entidade_desc = ?, observacao = ?, status = ?, uso_id = ?
			where id = ?';

			$this->db->query_update($sql, [
				$data->format("Y-m-d"),
				$entidade,
				$observacao,
				$status,
				$uso_id,
				$id,
			]);

			return $id;
		}
		// salvar novo registro
		else {
			$sql = 'INSERT INTO reserva
				(sala_id, periodo_id, dia, entidade_desc, status, observacao, usuario_id, uso_id)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

			return $this->db->query_insert($sql, [
				$sala,
				$periodo,
				$data->format("Y-m-d"),
				$entidade,
				$status,
				$observacao,
				$usuario,
				$uso_id,
			]);
		}
		}


	/**
	 * Método next.
	/**
	 * @param mixed $hoje
	 * @param mixed $categoria
	 * @param mixed $usuario_id
	 * @return mixed
	 */
	public function next($hoje, $categoria, $usuario_id = 0)
		{

		$sql = 'select Min(dia)
				   from reserva
				   inner join sala on reserva.sala_id = sala.id
				   where dia > ? and (sala.categoria_id = ? or ? = 0) and (? = 0 or reserva.usuario_id = ?)';

		$res = $this->db->query($sql, [$hoje->format("Y-m-d"), $categoria, $categoria, $usuario_id, $usuario_id]);
		$dia = $res[0]['Min(dia)'];

		$dia = is_null($dia) ? $hoje->format('Y-m-d') : $dia;

		$next = new \DateTime();
		$next = date_create_from_format('Y-m-d', $dia);

		return $next;
		}


	/**
	 * Método prev.
	 * @param mixed $hoje
	 * @param mixed $categoria
	 * @param mixed $usuario_id
	 * @return mixed
	 */
	public function prev($hoje, $categoria, $usuario_id = 0)
		{

		$sql = 'select Max(dia)
				   from reserva
				   inner join sala on reserva.sala_id = sala.id
				   where dia < ? and (sala.categoria_id = ? or ? = 0) and (? = 0 or reserva.usuario_id = ?)';

		$res = $this->db->query($sql, [$hoje->format("Y-m-d"), $categoria, $categoria, $usuario_id, $usuario_id]);
		$dia = $res[0]['Max(dia)'];

		$dia = is_null($dia) ? $hoje->format('Y-m-d') : $dia;

		$prev = new \DateTime();
		$prev = date_create_from_format('Y-m-d', $dia);

		return $prev;
		}

	/**
	 * Devolve os limites inclusivo e exclusivo da semana ISO de uma data.
	 * @return array{0: string, 1: string}
	 */
	private static function intervaloSemanaIso(\DateTimeInterface $dia): array
	{
		$data = \DateTimeImmutable::createFromInterface($dia);
		$inicio = $data
			->setISODate((int) $data->format('o'), (int) $data->format('W'))
			->setTime(0, 0);

		return [
			$inicio->format('Y-m-d'),
			$inicio->modify('+7 days')->format('Y-m-d'),
		];
	}

	/**
	 * Método listarReservas.
	 * @param mixed $dia
	 * @param mixed $tipo
	 * @return mixed
	 */
	public function listarReservas($dia = null, $tipo = 'm')
		{

		// Define o filtro padrão como o mês atual, se não for fornecido
		if (is_null($dia))
			$dia = new \DateTime();


		if ($tipo == 'm') {
			// Verifica se o filtro é uma data válida		
			$data_filtro = $dia->format('Ym');
			$filtro = 'EXTRACT(YEAR_MONTH FROM dia)';
			$selecaoData = $filtro . ' as data';
			$condicao = $filtro . ' = ?';
			$parametros = [$data_filtro];
		} else {
			[$inicio, $fim] = self::intervaloSemanaIso($dia);
			$selecaoData = '? as data';
			$condicao = 'dia >= ? AND dia < ?';
			$parametros = [$dia->format('W') . $dia->format('o'), $inicio, $fim];
		}

		$sql = ' SELECT ' . $selecaoData . ' , categoria.nome AS categoria, sala.nome AS sala, entidade_desc, dia, periodo.nome AS periodo, status
            FROM reserva
            LEFT JOIN sala ON sala_id = sala.id
            LEFT JOIN periodo ON periodo_id = periodo.id
            LEFT JOIN categoria ON sala.categoria_id = categoria.id
            WHERE ' . $condicao . '
			            ORDER BY dia, categoria, sala, periodo.seq';

		$reservas = $this->db->query($sql, $parametros);

		$status_n = array('reservada', 'confirmada', 'cancelada');


		foreach ($reservas as $key => $valor) {
			$status_nome = $status_n[$valor['status'] - 1];
			$reservas[$key]['status_nome'] = $status_nome;
			}

		return $reservas;

		}



	/**
	 * Lista reservas num intervalo de datas inclusivo.
	 * Recebe dois \DateTime ($inicio, $fim) e devolve as linhas.
	 */
	public function listarReservasPeriodo($inicio, $fim)
		{
		if (is_null($inicio) || is_null($fim))
			return array();

		$inicio_str = $inicio->format('Y-m-d');
		$fim_str = $fim->format('Y-m-d');

		$sql = ' SELECT dia, categoria.nome AS categoria, sala.nome AS sala, entidade_desc, dia, periodo.nome AS periodo, status
		    FROM reserva
		    LEFT JOIN sala ON sala_id = sala.id
		    LEFT JOIN periodo ON periodo_id = periodo.id
		    LEFT JOIN categoria ON sala.categoria_id = categoria.id
		    WHERE dia BETWEEN ? AND ?
		    ORDER BY dia, categoria, sala, periodo.seq';

		$reservas = $this->db->query($sql, [$inicio_str, $fim_str]);

		$status_n = array('reservada', 'confirmada', 'cancelada');
		foreach ($reservas as $key => $valor) {
			$status_nome = $status_n[$valor['status'] - 1];
			$reservas[$key]['status_nome'] = $status_nome;
			}

		return $reservas;
		}

	/**
	 * Método listarReservasParaRelatorio.
	 * @param mixed $hoje
	 * @param mixed $tipo
	 * @param mixed $fim
	 * @return mixed
	 */
	public function listarReservasParaRelatorio($hoje, $tipo, $fim = null)
		{
		if ($tipo === 'p' && $fim !== null) {
			$inicio_str = $hoje->format('Y-m-d');
			$fim_str = $fim->format('Y-m-d');

			$sql = "SELECT id FROM reserva WHERE dia BETWEEN ? AND ? AND status IN (1, 2)";
			return $this->db->query($sql, [$inicio_str, $fim_str]);
			}

		if ($tipo === 'w') {
			[$inicio, $fim] = self::intervaloSemanaIso($hoje);

			$sql = "SELECT id FROM reserva WHERE dia >= ? AND dia < ? AND status IN (1, 2)";
			return $this->db->query($sql, [$inicio, $fim]);
			}

		$mes = (int) $hoje->format('m');
		$ano = (int) $hoje->format('Y');

		$sql = "SELECT id FROM reserva WHERE MONTH(dia) = ? AND YEAR(dia) = ? AND status IN (1, 2)";
		return $this->db->query($sql, [$mes, $ano]);
		}

		/**
		 * Método usoMaisReservas.
		 * @param mixed $hoje
		 * @param mixed $tipo
		 * @param mixed $fim
		 * @return mixed
		 */
		public function usoMaisReservas($hoje, $tipo, $fim = null)
		{

		if ($tipo === 'p' && $fim !== null) {
			$inicio = $hoje->format('Y-m-d');
			$fim_str = $fim->format('Y-m-d');

			$sql = "SELECT u.nome as uso, COUNT(*) as total
                FROM reserva r
                LEFT JOIN uso u ON r.uso_id = u.id
                WHERE r.dia BETWEEN ? AND ? AND r.status IN (1, 2)
                GROUP BY u.nome
                ORDER BY total DESC";

			return $this->db->query($sql, [$inicio, $fim_str]);
			}

		if ($tipo === 'w') {
			[$inicio, $fim] = self::intervaloSemanaIso($hoje);

			$sql = "SELECT u.nome as uso, COUNT(*) as total
                FROM reserva r
                LEFT JOIN uso u ON r.uso_id = u.id
                WHERE r.dia >= ? AND r.dia < ? AND r.status IN (1, 2)
                GROUP BY u.nome
                ORDER BY total DESC";

			return $this->db->query($sql, [$inicio, $fim]);
			}
		else {
			$mes = (int) $hoje->format('m');
			$ano = (int) $hoje->format('Y');

			$sql = "SELECT u.nome as uso, COUNT(*) as total
                FROM reserva r
                LEFT JOIN uso u ON r.uso_id = u.id
                WHERE MONTH(r.dia) = ? AND YEAR(r.dia) = ? AND r.status IN (1, 2)
                GROUP BY u.nome
                ORDER BY total DESC";

			return $this->db->query($sql, [$mes, $ano]);
			}
		}


	/**
	 * Método reservasPorData.
	 * @param mixed $hoje
	 * @param mixed $tipo
	 * @param mixed $fim
	 * @return mixed
	 */
	public function reservasPorData($hoje, $tipo, $fim = null)
	{
		if ($tipo === 'p' && $fim !== null) {
			$inicio = $hoje->format('Y-m-d');
			$fim_str = $fim->format('Y-m-d');

			$sql = "SELECT r.dia as data, COUNT(*) as total
                FROM reserva r
                WHERE r.dia BETWEEN ? AND ? AND r.status IN (1, 2)
                GROUP BY r.dia
                ORDER BY r.dia ASC";

			$rows = $this->db->query($sql, [$inicio, $fim_str]);
			$semanas = [];
			foreach ($rows as $row) {
				$date = new \DateTimeImmutable((string) $row['data']);
				$semana = $date->format('o-W');
				if (!isset($semanas[$semana])) {
					$semanas[$semana] = [
						'data' => $date->format('d/m/Y'),
						'total' => 0,
					];
				}
				$semanas[$semana]['total'] += (int) $row['total'];
			}

			return array_values($semanas);
		}

		if ($tipo === 'w') {
			[$inicio, $fim] = self::intervaloSemanaIso($hoje);

			$sql = "SELECT r.dia as data, COUNT(*) as total
                FROM reserva r
                WHERE r.dia >= ? AND r.dia < ? AND r.status IN (1, 2)
                GROUP BY r.dia
                ORDER BY r.dia ASC";

			$rows = $this->db->query($sql, [$inicio, $fim]);
			foreach ($rows as $key => $row) {
				$date = new \DateTimeImmutable((string) $row['data']);
				$rows[$key]['data'] = $date->format('d/m/Y');
			}

			return $rows;
		}

		$mes = (int) $hoje->format('m');
		$ano = (int) $hoje->format('Y');

		$sql = "SELECT DATE_FORMAT(r.dia, '%d/%m/%Y') as data, COUNT(*) as total
            FROM reserva r
            WHERE MONTH(r.dia) = ? AND YEAR(r.dia) = ? AND r.status IN (1, 2)
            GROUP BY r.dia
            ORDER BY r.dia ASC";

		return $this->db->query($sql, [$mes, $ano]);
	}

	/**
	 * Método reservasPorSala.
	 * @param mixed $hoje
	 * @param mixed $tipo
	 * @param mixed $fim
	 * @return mixed
	 */
	public function reservasPorSala($hoje, $tipo, $fim = null)
	{
		if ($tipo === 'p' && $fim !== null) {
			$inicio = $hoje->format('Y-m-d');
			$fim_str = $fim->format('Y-m-d');

			$sql = "SELECT COALESCE(s.nome, 'Sem sala') as sala, COUNT(*) as total
                FROM reserva r
                LEFT JOIN sala s ON r.sala_id = s.id
                WHERE r.dia BETWEEN ? AND ? AND r.status IN (1, 2)
                GROUP BY s.nome
                ORDER BY total DESC";

			return $this->db->query($sql, [$inicio, $fim_str]);
		}

		if ($tipo === 'w') {
			[$inicio, $fim] = self::intervaloSemanaIso($hoje);

			$sql = "SELECT COALESCE(s.nome, 'Sem sala') as sala, COUNT(*) as total
                FROM reserva r
                LEFT JOIN sala s ON r.sala_id = s.id
                WHERE r.dia >= ? AND r.dia < ? AND r.status IN (1, 2)
                GROUP BY s.nome
                ORDER BY total DESC";

			return $this->db->query($sql, [$inicio, $fim]);
		}

		$mes = (int) $hoje->format('m');
		$ano = (int) $hoje->format('Y');

		$sql = "SELECT COALESCE(s.nome, 'Sem sala') as sala, COUNT(*) as total
            FROM reserva r
            LEFT JOIN sala s ON r.sala_id = s.id
            WHERE MONTH(r.dia) = ? AND YEAR(r.dia) = ? AND r.status IN (1, 2)
            GROUP BY s.nome
            ORDER BY total DESC";

		return $this->db->query($sql, [$mes, $ano]);
	}


	/**
	 * @param mixed $hoje
	 * @return mixed
	 */
	public function totalReservasMes($hoje)
		{

		$sql = 'select count(id) as total from reserva where month(dia) = ?';
		return $this->db->query($sql, [(int) $hoje->format("m")]);

		}


	    /**
	     * Método listarPorUsuario.
	     * @param mixed $usuario_id
	     * @return mixed
	     */
	    public function listarPorUsuario($usuario_id)
    {
        $sql = "select a.id, c.nome as sala, b.nome as periodo, date_format(a.dia, '%d/%m/%Y') as dia, a.entidade_desc, u.nome as uso, a.status from reserva a inner join periodo b on a.periodo_id = b.id inner join sala c on a.sala_id = c.id left join uso u on a.uso_id = u.id where a.usuario_id = ? order by a.dia, c.nome, b.seq";
        return $this->db->query($sql, [$usuario_id]);
    }

    /**
     * Método excluirPorUsuario.
     * @param mixed $usuario_id
     * @return mixed
     */
    public function excluirPorUsuario($usuario_id)
    {
        $sql = 'delete from reserva where usuario_id = ?';
        return $this->db->query_update($sql, [$usuario_id]);
    }

    /**
     * Método listarPorSala.
     * @param mixed $sala_id
     * @return mixed
     */
    public function listarPorSala($sala_id)
    {
        $sql = "select a.id, b.nome as periodo, date_format(a.dia, '%d/%m/%Y') as dia, a.entidade_desc, u.nome as uso, a.status, d.nome as usuario from reserva a inner join periodo b on a.periodo_id = b.id inner join usuario d on a.usuario_id = d.id left join uso u on a.uso_id = u.id where a.sala_id = ? order by a.dia, b.seq";
        return $this->db->query($sql, [$sala_id]);
    }

    /**
     * Método excluirPorSala.
     * @param mixed $sala_id
     * @return mixed
     */
    public function excluirPorSala($sala_id)
    {
        $sql = 'delete from reserva where sala_id = ?';
        return $this->db->query_update($sql, [$sala_id]);
    }



}

?>
