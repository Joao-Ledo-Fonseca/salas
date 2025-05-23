<?php


class Database
{
	private $link;

	function __construct()
	{
		global $cfg;

		$this->link = mysqli_connect($cfg->db_host, $cfg->db_user, $cfg->db_senha, $cfg->db_banco, $cfg->db_porta) or die("erro: " . mysqli_connect_error());
	}

	function query($sql)  // SELECT
	{
		mysqli_query($this->link, "SET NAMES utf8");
		$result = mysqli_query($this->link, $sql) or die(mysqli_error($this->link));

		$array_result = array();
		//colocar as informações em array
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$array_result[] = $row;
		}
		return $array_result;
	}

	function query_insert($sql)// INSERT
	{
		mysqli_query($this->link, "SET NAMES utf8");
		
		try {
			mysqli_query($this->link, $sql);
		} catch (mysqli_sql_exception $e) {
			return $e->getMessage();
		};				
		return mysqli_insert_id($this->link);		 		
	}

	function query_update($sql)//UPDATE & DELETE
	{
		mysqli_query($this->link, "SET NAMES utf8");	

		try {					
			mysqli_query($this->link, $sql); 
		} catch (mysqli_sql_exception $e) { 
			return $e->getMessage();			
		}		
		return 0;
	}
	
}
?>