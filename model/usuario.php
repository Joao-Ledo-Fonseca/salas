<?php
require_once "db_mysqli.php";

class Usuario
{

	function autenticar($email, $senha)
	{

		$db = new Database();

		$sql = ' select id, nome, nivel from usuario where (email = "' . $email . '" or nome = "' . $email . '") and senha="' . $senha . '";';
		return $db->query($sql);

	}

	function listar()
	{

		$db = new Database();

		$sql = ' select * from usuario order by nivel desc;';
		return $db->query($sql);
	}


	function abrir($id)
	{

		$db = new Database();

		$sql = ' select * from usuario where id = ' . $id;
		;
		return $db->query($sql);
	}

	function salvar($id, $nome, $email, $senha, $nivel)
	{

		$db = new Database();

		// inserir
		if ($id == 0) {
			$senha = md5($senha);

			$sql = 'insert into usuario ( nome, email, senha, nivel) values ("' . $nome . '","' . $email . '","' . $senha . '","' . $nivel . '")';
			return $db->query_insert($sql);
		}
		// atualizar
		else {
			if (!is_null($senha))
				$and = ' ,senha = md5(\'' . $senha . '\')  ';
			else
				$and = '';

			$sql = ' update usuario set nome = "' . $nome . '", email = "' . $email . '" ' . $and . ', nivel = ' . $nivel . '  where id = ' . $id;
			return $db->query_update($sql);

		}


	}

	function excluir($id)
	{
		$db = new Database();
		$sql = 'delete from usuario where id = ' . $id;
		return $db->query_update($sql);
	}

}

?>