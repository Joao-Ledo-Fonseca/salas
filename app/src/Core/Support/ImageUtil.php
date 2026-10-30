<?php
namespace Salas\Core\Support;

class ImageUtil
{
    public static function imagemUpload($img_file)
    {
        if ($img_file == "") {
            return false;
        }

        if (!file_exists($img_file)) {
            return false;
        }

        if (getimagesize($img_file) === false) {
            return false;
        }

        $imagem = $_FILES['imagem'];
        $nome_img = $imagem['name'];
        $tamanho_img = $imagem['size'];
        $tipo_img = $imagem['type'];

        $conteudo = file_get_contents($imagem['tmp_name']);

        return ['nome_img' => $nome_img, 'tamanho_img' => $tamanho_img, 'tipo_img' => $tipo_img, 'conteudo' => $conteudo];
    }
}
?>