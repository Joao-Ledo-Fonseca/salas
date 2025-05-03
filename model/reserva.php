<?php
require_once "db_mysqli.php";

class Reserva
{
	function abrir($id)
	{

		$db = new Database();

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

		return $db->query($sql);

	}



	function verificar($data, $sala, $periodo)
	{

		$db = new Database();

		$sql = ' select 
				a.id
			
	 			from reserva a
	 
	 			where a.sala_id =  ' . $sala . '
	 			and a.periodo_id = ' . $periodo . '
	 			and dia = "' . $data->format("Y-m-d") . '"; ';

		return $db->query($sql);

	}


	function verificarCompleto($data, $sala, $periodo)
	{
		$db = new Database();

		$sql = 'select * from reserva where sala_id =  ' . $sala . ' and periodo_id = ' . $periodo . ' and dia = "' . $data->format("Y-m-d") . '" ;';
		return $db->query($sql);
	}

	function excluir($id)
	{
		$db = new Database();

		$sql = ' delete from reserva where id =' . $id;

		$db->query_update($sql);
		return 0;

	}

	function salvar($id, $dia, $professor, $disciplina, $data, $observacao, $status, $sala, $periodo, $usuario)
	{

		$db = new Database();

		// atualizar
		if ($id > 0) {
			$sql = 'update reserva set
			dia = "' . $data->format("Y-m-d") . '"
			,professor_desc = "' . $professor . '"
			,disciplina_desc = "' . $disciplina . '"
			,observacao = "' . $observacao . '"
			,status = ' . $status . '
			
			where id  =	' . $id;

			$db->query_update($sql);
			return $id;

		}
		// salvar novo registro
		else {
			$sql = '
			
			INSERT INTO reserva
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
			 ,'  . $status . ' -- status - INT(11) NOT NULL
			 ,"' . $observacao . '" -- observacao - TEXT
			 ,' . $usuario . ' -- usuario_id - INT(11) NOT NULL
			) ';
			

			return $db->query_insert($sql);

		}

	}


	function next($hoje)
	{
		//stuff to get data next reserva
		$db = new Database();

		$sql = 'select Min(dia)	
				   from reserva
				   where dia >"' . $hoje->format("Y-m-d") . '"';

		$res = $db->query($sql);
		$dia = $res[0]['Min(dia)'];

		$dia = is_null($dia) ? $hoje->format('Y-m-d') : $dia;

		$next = new datetime();
		$next = date_create_from_format('Y-m-d', $dia);

		return $next;
	}

	function prev($hoje)
	{

		$db = new Database();

		$sql = 'select Max(dia)	
				   from reserva
				   where dia <"' . $hoje->format("Y-m-d") . '"';

		$res = $db->query($sql);
		$dia = $res[0]['Max(dia)'];

		$dia = is_null($dia) ? $hoje->format('Y-m-d') : $dia;

		$prev = new datetime();
		$prev = date_create_from_format('Y-m-d', $dia);

		return $prev;
	}

	function listaReservas()
	{

		$db = new Database();

		$sql = 'select disciplina_desc, sala.nome as sala, dia, periodo.nome as periodo, status	
				from reserva
				left join sala on sala_id = sala.id
				left join periodo on periodo_id = periodo.id
				order by dia, sala_id, periodo.seq';

		return $db->query($sql);

	}


	function disciplinaMaisReservas()
	{

		$db = new Database();

		$sql = 'select disciplina_desc, count(id) as total	
				from reserva
				group by disciplina_desc
				order by total desc';


		return $db->query($sql);

	}


	function totalReservasMes($hoje)
	{
		$db = new Database();

		$sql = '  		
		select count(id) as total from reserva where month(dia) = ' . $hoje->format("m") . ';';
		return $db->query($sql);

	}
}

?>