<?php
require_once "db_mysqli.php";

class Reserva
{

	private $db = null;

	function __construct()
	{
		$this->db = new Database();
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
		,date_format(a.dia,"%d/%m/%Y") as dia
		,d.nome as usuario
					
	 	from reserva a
	 
	 	inner join periodo b on
	 	a.periodo_id = b.id
	 
	 	inner join sala c on
	 	a.sala_id = c.id
	 
		inner join usuario d on
	 	a.usuario_id = d.id

	 	where a.id = ' . $id;

		return $this->db->query($sql);

	}



	function verificar($data, $sala, $periodo)
	{
		
		$sql = ' select 
				a.id
			
	 			from reserva a
	 
	 			where a.sala_id =  ' . $sala . '
	 			and a.periodo_id = ' . $periodo . '
	 			and dia = "' . $data->format("Y-m-d") . '"; ';

		return $this->db->query($sql);

	}


	function verificarCompleto($data, $sala, $periodo)
	{		
		$sql = 'select * from reserva where sala_id =  ' . $sala . ' and periodo_id = ' . $periodo . ' and dia = "' . $data->format("Y-m-d") . '" ;';				
		$resultado = $this->db->query($sql);

		return $resultado;
	}

	function excluir($id)
	{
		$sql = ' delete from reserva where id =' . $id;
		$this->db->query_update($sql);

		return 0;

	}

	function salvar($id, $dia, $professor, $disciplina, $data, $observacao, $status, $sala, $periodo, $usuario)
	{		

		// atualizar
		if ($id > 0) {
			$sql = 'update reserva set
			dia = "' . $data->format("Y-m-d") . '"
			,professor_desc = "' . $professor . '"
			,disciplina_desc = "' . $disciplina . '"
			,observacao = "' . $observacao . '"
			,status = ' . $status . '			
			where id  =	' . $id;
			
			$this ->db->query_update($sql);

			return $id;

		}
		// salvar novo registro
		else {
			$sql = 'INSERT INTO reserva
			(
			 sala_id
			 ,periodo_id
			 ,dia
			 ,professor_desc
			 ,disciplina_desc
			 ,status
			 ,observacao
			 ,usuario_id
			)
			VALUES
			(
			  ' . $sala . ' -- sala_id - INT(11) NOT NULL
			 ,' . $periodo . ' -- periodo_id - INT(11) NOT NULL
			 ,"' . $data->format("Y-m-d") . '" -- dia - DATE NOT NULL
			 ,"' . $professor . '" -- professor_desc - VARCHAR(255)
			 ,"' . $disciplina . '" -- disciplina_desc - VARCHAR(255)
			 ,' . $status . ' -- status - INT(11) NOT NULL
			 ,"' . $observacao . '" -- observacao - TEXT
			 ,' . $usuario . ' -- usuario_id - INT(11) NOT NULL
			) ';


			return $this->db->query_insert($sql);

		}

	}


	function next($hoje)
	{
		//stuff to get data next reserva		
		$sql = 'select Min(dia)	
				   from reserva
				   where dia >"' . $hoje->format("Y-m-d") . '"';

		$res = $this->db->query($sql);
		$dia = $res[0]['Min(dia)'];

		$dia = is_null($dia) ? $hoje->format('Y-m-d') : $dia;

		$next = new datetime();
		$next = date_create_from_format('Y-m-d', $dia);

		return $next;
	}

	function prev($hoje)
	{		

		$sql = 'select Max(dia)	
				   from reserva
				   where dia <"' . $hoje->format("Y-m-d") . '"';

		$res = $this->db->query($sql);
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
		} else {
			$data_filtro = $dia->format('WY');
			$filtro = 'CONCAT(EXTRACT(WEEK FROM dia),EXTRACT(YEAR FROM dia))';			
		}

		// Consulta corrigida com a cláusula WHERE antes do ORDER BY e uso de parâmetros
		$sql = ' SELECT '. $filtro .' as data, categoria.nome AS categoria, sala.nome AS sala, disciplina_desc, dia, periodo.nome AS periodo, status
            FROM reserva
            LEFT JOIN sala ON sala_id = sala.id
            LEFT JOIN periodo ON periodo_id = periodo.id
            LEFT JOIN categoria ON sala.categoria_id = categoria.id
            WHERE    '.$filtro .' = "' . $data_filtro . '" 
			            ORDER BY dia, categoria, sala, periodo.seq';		

		// Executa a consulta com o parâmetro
		$reservas = $this->db->query($sql);

		$status_n = array('reservada','confirmada','cancelada');
		

		foreach($reservas as $key=>$valor) {
			$status_nome = $status_n[$valor['status']-1];
			$reservas[$key]['status_nome'] = $status_nome;
		}

		return $reservas;

	}


	function disciplinaMaisReservas()
	{		

		$sql = 'select disciplina_desc, count(id) as total	
				from reserva
				group by disciplina_desc
				order by total desc';


		return $this->db->query($sql);

	}


	function totalReservasMes($hoje)
	{		

		$sql = '  		
		select count(id) as total from reserva where month(dia) = ' . $hoje->format("m") . ';';
		return $this->db->query($sql);

	}
}

?>