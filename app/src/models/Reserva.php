<?php
require_once __DIR__ . "/../infrastructure/Persistence/Database.php";

class Reserva
	{

	private $db = null;

	function __construct(Database $db)
		{
		$this->db = $db;
		}


	function abrir($id)
		{
		$sql = ' select 
		a.id
		,a.sala_id
		,c.nome as sala
		,a.periodo_id
		,b.nome as periodo
		,a.professor_desc
		,a.disciplina_desc
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



	function verificar($data, $sala, $periodo)
		{

		$sql = 'select a.id
	 			from reserva a
	 			where a.sala_id = ?
	 			and a.periodo_id = ?
	 			and dia = ?';

		return $this->db->query($sql, [$sala, $periodo, $data->format("Y-m-d")]);

		}


	function verificarCompleto($data, $sala, $periodo)
		{
		$sql = 'select * from reserva where sala_id = ? and periodo_id = ? and dia = ?';
		$resultado = $this->db->query($sql, [$sala, $periodo, $data->format("Y-m-d")]);

		return $resultado;
		}

	function listarPorDia(DateTime $hoje, int $categoria_id = 0, int $usuario_id = 0): array
		{
		$sql = 'select r.*, s.nome as sala_nome, s.categoria_id, p.nome as periodo_nome
			   from reserva r
			   inner join sala s on r.sala_id = s.id
			   inner join periodo p on r.periodo_id = p.id
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


	function excluir($id)
		{
		$sql = 'delete from reserva where id = ?';
		$this->db->query_update($sql, [$id]);

		return 0;
		}

	function salvar($id, $dia, $professor, $disciplina, $data, $observacao, $status, $sala, $periodo, $usuario, $uso_id = null)
		{

		// atualizar
		if ($id > 0) {
			$sql = 'update reserva set
			dia = ?, professor_desc = ?, disciplina_desc = ?, observacao = ?, status = ?, uso_id = ?
			where id = ?';

			$this->db->query_update($sql, [
				$data->format("Y-m-d"),
				$professor,
				$disciplina,
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
				(sala_id, periodo_id, dia, professor_desc, disciplina_desc, status, observacao, usuario_id, uso_id)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

			return $this->db->query_insert($sql, [
				$sala,
				$periodo,
				$data->format("Y-m-d"),
				$professor,
				$disciplina,
				$status,
				$observacao,
				$usuario,
				$uso_id,
			]);
		}
		}


	function next($hoje, $categoria, $usuario_id = 0)
		{

		$sql = 'select Min(dia)
				   from reserva
				   inner join sala on reserva.sala_id = sala.id
				   where dia > ? and (sala.categoria_id = ? or ? = 0) and (? = 0 or reserva.usuario_id = ?)';

		$res = $this->db->query($sql, [$hoje->format("Y-m-d"), $categoria, $categoria, $usuario_id, $usuario_id]);
		$dia = $res[0]['Min(dia)'];

		$dia = is_null($dia) ? $hoje->format('Y-m-d') : $dia;

		$next = new datetime();
		$next = date_create_from_format('Y-m-d', $dia);

		return $next;
		}


	function prev($hoje, $categoria, $usuario_id = 0)
		{

		$sql = 'select Max(dia)
				   from reserva
				   inner join sala on reserva.sala_id = sala.id
				   where dia < ? and (sala.categoria_id = ? or ? = 0) and (? = 0 or reserva.usuario_id = ?)';

		$res = $this->db->query($sql, [$hoje->format("Y-m-d"), $categoria, $categoria, $usuario_id, $usuario_id]);
		$dia = $res[0]['Max(dia)'];

		$dia = is_null($dia) ? $hoje->format('Y-m-d') : $dia;

		$prev = new datetime();
		$prev = date_create_from_format('Y-m-d', $dia);

		return $prev;
		}

	function listaReservas($dia = null, $tipo = 'm')
		{

		// Define o filtro padrão como o mês atual, se não for fornecido
		if (is_null($dia))
			$dia = new dateTime();


		if ($tipo == 'm') {   // m - mês ; w - week
			// Verifica se o filtro é uma data válida		
			$data_filtro = $dia->format('Ym');
			$filtro = 'EXTRACT(YEAR_MONTH FROM dia)';
			}
		else {
			$data_filtro = $dia->format('WY');
			$filtro = 'CONCAT(EXTRACT(WEEK FROM dia),EXTRACT(YEAR FROM dia))';
			}

		// Consulta corrigida com a cláusula WHERE antes do ORDER BY e uso de parâmetros
		$sql = ' SELECT ' . $filtro . ' as data, categoria.nome AS categoria, sala.nome AS sala, disciplina_desc, dia, periodo.nome AS periodo, status
            FROM reserva
            LEFT JOIN sala ON sala_id = sala.id
            LEFT JOIN periodo ON periodo_id = periodo.id
            LEFT JOIN categoria ON sala.categoria_id = categoria.id
            WHERE    ' . $filtro . ' = ?
			            ORDER BY dia, categoria, sala, periodo.seq';

		$reservas = $this->db->query($sql, [$data_filtro]);

		$status_n = array('reservada', 'confirmada', 'cancelada');


		foreach ($reservas as $key => $valor) {
			$status_nome = $status_n[$valor['status'] - 1];
			$reservas[$key]['status_nome'] = $status_nome;
			}

		return $reservas;

		}



	/**
	 * Lista reservas num intervalo de datas inclusivo.
	 * Recebe dois DateTime ($inicio, $fim) e devolve as linhas.
	 */
	function listaReservasPeriodo($inicio, $fim)
		{
		if (is_null($inicio) || is_null($fim))
			return array();

		$inicio_str = $inicio->format('Y-m-d');
		$fim_str = $fim->format('Y-m-d');

		$sql = ' SELECT dia, categoria.nome AS categoria, sala.nome AS sala, disciplina_desc, dia, periodo.nome AS periodo, status
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

	public function listaReservasParaRelatorio($hoje, $tipo, $fim = null)
		{
		if ($tipo === 'p' && $fim !== null) {
			$inicio_str = $hoje->format('Y-m-d');
			$fim_str = $fim->format('Y-m-d');

			$sql = "SELECT id FROM reserva WHERE dia BETWEEN ? AND ? AND status IN (1, 2)";
			return $this->db->query($sql, [$inicio_str, $fim_str]);
			}

		if ($tipo === 'w') {
			$data_base = $hoje->format('Y-m-d');

			$sql = "SELECT id FROM reserva WHERE YEARWEEK(dia, 1) = YEARWEEK(?, 1) AND status IN (1, 2)";
			return $this->db->query($sql, [$data_base]);
			}

		$mes = (int) $hoje->format('m');
		$ano = (int) $hoje->format('Y');

		$sql = "SELECT id FROM reserva WHERE MONTH(dia) = ? AND YEAR(dia) = ? AND status IN (1, 2)";
		return $this->db->query($sql, [$mes, $ano]);
		}

	public function disciplinaMaisReservas($hoje, $tipo, $fim = null)
		{
		if ($tipo === 'p' && $fim !== null) {
			$inicio = $hoje->format('Y-m-d');
			$fim_str = $fim->format('Y-m-d');

			$sql = "SELECT disciplina_desc, COUNT(*) as total 
                FROM reserva 
                WHERE dia BETWEEN ? AND ? AND status IN (1, 2)
                GROUP BY disciplina_desc 
                ORDER BY total DESC";

			return $this->db->query($sql, [$inicio, $fim_str]);
			}

		if ($tipo === 'w') {
			$data_base = $hoje->format('Y-m-d');

			$sql = "SELECT disciplina_desc, COUNT(*) as total 
                FROM reserva 
                WHERE YEARWEEK(dia, 1) = YEARWEEK(?, 1) AND status IN (1, 2)
                GROUP BY disciplina_desc 
                ORDER BY total DESC";

			return $this->db->query($sql, [$data_base]);
			}
		else {
			$mes = (int) $hoje->format('m');
			$ano = (int) $hoje->format('Y');

			$sql = "SELECT disciplina_desc, COUNT(*) as total 
                FROM reserva 
                WHERE MONTH(dia) = ? AND YEAR(dia) = ? AND status IN (1, 2)
                GROUP BY disciplina_desc 
                ORDER BY total DESC";

			return $this->db->query($sql, [$mes, $ano]);
			}
		}


	function totalReservasMes($hoje)
		{

		$sql = 'select count(id) as total from reserva where month(dia) = ?';
		return $this->db->query($sql, [(int) $hoje->format("m")]);

		}


	    function listarPorUsuario($usuario_id)
    {
        $sql = "select a.id, c.nome as sala, b.nome as periodo, date_format(a.dia, '%d/%m/%Y') as dia, a.professor_desc, a.disciplina_desc, a.status from reserva a inner join periodo b on a.periodo_id = b.id inner join sala c on a.sala_id = c.id where a.usuario_id = ? order by a.dia, c.nome, b.seq";
        return $this->db->query($sql, [$usuario_id]);
    }

    function excluirPorUsuario($usuario_id)
    {
        $sql = 'delete from reserva where usuario_id = ?';
        return $this->db->query_update($sql, [$usuario_id]);
    }

    function listarPorSala($sala_id)
    {
        $sql = "select a.id, b.nome as periodo, date_format(a.dia, '%d/%m/%Y') as dia, a.professor_desc, a.disciplina_desc, a.status, d.nome as usuario from reserva a inner join periodo b on a.periodo_id = b.id inner join usuario d on a.usuario_id = d.id where a.sala_id = ? order by a.dia, b.seq";
        return $this->db->query($sql, [$sala_id]);
    }

    function excluirPorSala($sala_id)
    {
        $sql = 'delete from reserva where sala_id = ?';
        return $this->db->query_update($sql, [$sala_id]);
    }
}

?>
