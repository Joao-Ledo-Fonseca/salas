<?php
namespace Salas\Infrastructure\Support;

/* classe de funcionalidades gerais e uteis */
class Util
{

// comparação entre datas
public static function traduz_data(string $date, string $lang = 'en'): string
{
$meses_pt = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez', 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
$meses_en = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Sun', 'Mon', 'Tue', 'Thu', 'Wed', 'Fri', 'Sat'];
return ($lang === 'en') ? str_replace($meses_pt, $meses_en, $date) : str_replace($meses_en, $meses_pt, $date);
}

public static function parseDataPt(string $data): ?\DateTime
{
$data = trim($data);
if ($data === '') {
return null;
}

$date = date_create_from_format('D d/m/Y', self::traduz_data($data, 'en'));
if ($date !== false) {
return $date;
}

return \DateTime::createFromFormat('d/m/Y', $data) ?: null;
}

public static function calcularDiaAdjacente(\DateTime $baseDate, int $dias): string
{
$dia = \DateTime::createFromFormat('d/m/Y', $baseDate->format('d/m/Y')) ?: clone $baseDate;
$dia->modify(($dias >= 0 ? '+' : '') . $dias . ' day');
return self::traduz_data($dia->format('D d/m/Y'), 'pt');
}

public static function calcularDiaController(\DateTime $baseDate, callable $callback, int $categoria, int $usuario_id = 0): string
{
$dia = \DateTime::createFromFormat('d/m/Y', $baseDate->format('d/m/Y')) ?: clone $baseDate;
$dia = call_user_func($callback, $dia, $categoria, $usuario_id);
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

return array('nome_img' => $nome_img, 'tamanho_img' => $tamanho_img, 'tipo_img' => $tipo_img, 'conteudo' => $conteudo);
}
}

?>

