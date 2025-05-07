<?php

/* classe de funcionalidades gerais e uteis */
class Util
{

	// limpar parametros que possam causar SQL injection
	public static function clearparam($param)
	{

		$badchars = array(")", "(", "'", "\"", ";", "--", "\\", ">", "..");
		return str_replace($badchars, '', $param);

	}

	// comparação entre datas
	public static function ndate_diff($date1, $date2)
	{
		$current = $date1;
		$datetime2 = date_create($date2);
		$count = 0;
		while (date_create($current) < $datetime2) {
			$current = gmdate("Y-m-d", strtotime("+1 day", strtotime($current)));
			$count++;
		}
		return $count;
	}

	public static function imagemUpload($img_file)
	{  
		// Verifica se o ficheiro foi enviado
		if ($img_file == "") {
			return false;
		}

		// Verifica se o ficheiro existe
		if (!file_exists($img_file)) {
			return false;
		}

		// Verifica se o ficheiro é imagem
		if (getimagesize($img_file) === false) {
			return false;
		}
				
			$imagem = $_FILES['imagem'];
			$nome_img = $imagem['name'];
			$tamanho_img = $imagem['size'];
			$tipo_img = $imagem['type'];

			//Lê o ficheiro uploaded
			$conteudo = file_get_contents($imagem['tmp_name']);			
			$conteudo = addslashes($conteudo);
				
		return array('nome_img'=>$nome_img, 'tamanho_img'=>$tamanho_img, 'tipo_img'=>$tipo_img, 'conteudo'=>$conteudo);
	}


}


?>