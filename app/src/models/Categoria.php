<?php
namespace Salas\Models;

use Salas\Core\Persistence\Database;

class Categoria 
{

private $db = null;

function __construct(Database $db)
{
$this->db = $db;
}

function listar()
{

$sql = ' select id, nome, descricao from categoria order by nome;';
return $this->db->query($sql);
}

function abrir($id)
{
$rows = $this->db->query('select * from categoria where categoria.id = ?', [$id]);
if (!isset($rows[0])) {
return [];
}
$imageId = $rows[0]['imagem_id'] ?? null;
$image = ['id' => 0, 'nome_imagem' => '', 'tamanho_imagem' => '', 'tipo_imagem' => '', 'imagem' => ''];
if ($imageId !== null) {
$found = (new \Salas\Models\Imagem($this->db))->abrir((int) $imageId);
if (isset($found[0])) {
$image = $found[0];
}
}
return [array_merge($image, $rows[0])];
}

function salvar($id, $nome, $descricao, $imagem_id, $uploadedImage = false)
{

if (is_array($uploadedImage)) {
if ($imagem_id !== 0 && $imagem_id !== '' && $imagem_id !== null) {
(new \Salas\Models\Imagem($this->db))->excluir((int) $imagem_id);
}
$imagem_id = (new \Salas\Models\Imagem($this->db))->salvar(
0,
$uploadedImage['nome_img'],
$uploadedImage['tamanho_img'],
$uploadedImage['tipo_img'],
$uploadedImage['conteudo']
);
}

if ($imagem_id === '') {
$imagem_id = null;
}

if ($id == 0) {
if (strlen($nome) <= 0) {
return 0;
}
// inserir
$sql = 'insert into categoria (nome, descricao, imagem_id) values (?, ?, ?)';
return $this->db->query_insert($sql, [$nome, $descricao, $imagem_id]);

} else {
if (strlen($nome) <= 0) {
return $id;
}
// atualizar
$sql = 'update categoria set nome = ?, descricao = ?, imagem_id = ? where id = ?';
$this->db->query_update($sql, [$nome, $descricao, $imagem_id, $id]);
return $id;
}
}

function excluir($id, $imagemId = '')
{
try {
$sql = 'delete from categoria where id = ?';
$this->db->query_update($sql, [$id]);
} catch (\PDOException $e) {
return false;
}
if ($imagemId !== 0 && $imagemId !== '' && $imagemId !== null) {
(new \Salas\Models\Imagem($this->db))->excluir((int) $imagemId);
}
return true;
}

function total()
{

$sql = 'select count(id) as total from categoria  ';
return $this->db->query($sql);

}

}

?>
