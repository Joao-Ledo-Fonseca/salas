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

	public static function traduz_data(string $date, string $lang = 'en'): string
	{
		$meses_pt = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez', 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
		$meses_en = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Sun', 'Mon', 'Tue', 'Thu', 'Wed', 'Fri', 'Sat'];
		return ($lang === 'en') ? str_replace($meses_pt, $meses_en, $date) : str_replace($meses_en, $meses_pt, $date);
	}

	public static function parseDataPt(string $data): ?DateTime
	{
		$data = trim($data);
		if ($data === '') {
			return null;
		}

		$date = date_create_from_format('D d/m/Y', self::traduz_data($data, 'en'));
		if ($date !== false) {
			return $date;
		}

		return DateTime::createFromFormat('d/m/Y', $data) ?: null;
	}

	public static function calcularDiaAdjacente(DateTime $baseDate, int $dias): string
	{
		$dia = DateTime::createFromFormat('d/m/Y', $baseDate->format('d/m/Y')) ?: clone $baseDate;
		$dia->modify(($dias >= 0 ? '+' : '') . $dias . ' day');
		return self::traduz_data($dia->format('D d/m/Y'), 'pt');
	}

	public static function calcularDiaController(DateTime $baseDate, callable $callback, int $categoria): string
	{
		$dia = DateTime::createFromFormat('d/m/Y', $baseDate->format('d/m/Y')) ?: clone $baseDate;
		$dia = call_user_func($callback, $dia, $categoria);
		return self::traduz_data($dia->format('D d/m/Y'), 'pt');
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

		return array('nome_img' => $nome_img, 'tamanho_img' => $tamanho_img, 'tipo_img' => $tipo_img, 'conteudo' => $conteudo);
	}


	public static function url_GET($url, $params)
	{

		/* Ex de uso:

						   $post_data = array(
								 'param1' => 'valor1',
								 'param2' => 'valor2',
								 );
						 
							url = "https://www.exemplo.com/pagina"

							echo url_POST($url, $params );
						   
						 Output: https://www.exemplo.com/pagina (parametros no corpo da requisição)
						 */

		$url_completo = $url . http_build_query($params);

		return $url_completo;

	}

public static function redirect_POST($url, $post_data)
{
    echo '<form id="postForm" action="' . htmlspecialchars($url) . '" method="post">';
    foreach ($post_data as $key => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
    }
    echo '</form>';
    echo '<script>document.getElementById("postForm").submit();</script>';
    exit;
}



	public static function url_POST($url, $post_data)
	{
		/* Ex de uso:

					 $post_data = array(
					   'param1' => 'valor1',
					   'param2' => 'valor2',
					   );
				   
					   url = "https://www.exemplo.com/pagina"

					   $resposta_servidor = url_POST($url, $params );
					 
					   echo $resposta_servidor (parametros no corpo da requisição)
				   */

				   

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
		// curl_setopt($curl, CURLOPT_POSTFIELDS, 'submit=Submit&categoria_filtro=0');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);

		/*
		if ($response) {
			$data = json_decode($response, true);
			// Exibição de dados com operadores PHP

			echo "Post ID: " . $data['id'] . "<br>";
			echo "Title: " . $data['title'] . "<br>";
			echo "Body: " . $data['body'] . "<br>";
		}
		*/
		curl_close($curl);
		return $response;

	}



/*
	public static function url_POST($url, $post_data)
	{
		// $url = 'http://server.com/path';
		// $post_data = ['key1' => 'value1', 'key2' => 'value2'];

		// use key 'http' even if you send the request to https://...
		$options = [
			'http' => [
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'POST',
				'content' => http_build_query($post_data),
			],
		];

		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === false) {
			// Handle error 
			ECHO "error ERROR error";
		}
		;
		return $result;
	}

*/
}


?>