<?php
require_once "db_mysqli.php";

class Usuario 
{
	private $db = null;

	function __construct()
	{
		$this->db = new Database();
	}


	function autenticar($email, $senha)
	{
		$sql = ' select id, nome, nivel from usuario where (email = "' . $email . '" or nome = "' . $email . '") and senha="' . $senha . '";';
		return$this->db->query($sql);
	}

	function listar()
	{	

		$sql = ' select * from usuario order by nivel desc;';
		return$this->db->query($sql);
	}


	function abrir($id)
	{	
		$sql = ' select * from usuario where id = ' . $id;				
		return $this->db->query($sql);						
	}

	function salvar($id, $nome, $email, $senha, $telefone, $NIF, $nivel)
	{		

		// inserir		
		if ($id == 0) {
			
			$senha = md5($senha);

			$sql = ' insert into usuario ( nome, email, senha, telefone, NIF, nivel) 
					values ("' . $nome . '","' . $email . '","' . $senha . '","' . $telefone . '","' . $NIF . '","' . $nivel . '")';					
			return $this->db->query_insert($sql);			
		}
		// atualizar
		else {
			if (!is_null($senha))
				$and = ' ,senha = md5(\'' . $senha . '\')  ';
			else
				$and = '';

			$sql = ' update usuario set nome = "' . $nome . '", 
						email = "' . $email . '" ' . 
						$and . ', 
						telefone = "'. $telefone . '", 
						NIF = "' . $NIF .'", 
						nivel = ' . $nivel . '  
						where id = ' . $id;
			return $this->db->query_update($sql);
		}


	}

	function excluir($id)
	{
		$db = new Database();
		
		$sql = 'delete from usuario where id = ' . $id;
		return $this->db->query_update($sql);
	}

}

?>