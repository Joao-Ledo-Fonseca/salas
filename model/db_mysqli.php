<?php

class Database
{
private $link;

function __construct()
{
global $cfg;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$this->link = mysqli_connect($cfg->db_host, $cfg->db_user, $cfg->db_senha, $cfg->db_banco, $cfg->db_porta);
if (!$this->link) {
die("erro: " . mysqli_connect_error());
}
mysqli_set_charset($this->link, 'utf8');
}

private function bindParams($stmt, $params)
{
if (empty($params)) {
return;
}

$types = '';
$refs = array();
foreach ($params as $key => $value) {
if (is_int($value)) {
$types .= 'i';
} elseif (is_float($value)) {
$types .= 'd';
} else {
$types .= 's';
}
$refs[$key] = &$params[$key];
}

array_unshift($refs, $types);
call_user_func_array(array($stmt, 'bind_param'), $refs);
}

private function prepare($sql, $params)
{
$stmt = mysqli_prepare($this->link, $sql);
if (!$stmt) {
die(mysqli_error($this->link));
}

if (!empty($params)) {
$this->bindParams($stmt, $params);
}

return $stmt;
}

function query($sql, $params = array())
{
if (empty($params)) {
$result = mysqli_query($this->link, $sql);
} else {
$stmt = $this->prepare($sql, $params);
$stmt->execute();
$result = $stmt->get_result();
}

$array_result = array();
if ($result instanceof mysqli_result) {
while ($row = mysqli_fetch_assoc($result)) {
$array_result[] = $row;
}
mysqli_free_result($result);
}
return $array_result;
}

function query_insert($sql, $params = array())
{
if (empty($params)) {
mysqli_query($this->link, $sql);
} else {
$stmt = $this->prepare($sql, $params);
$stmt->execute();
}
return mysqli_insert_id($this->link);
}

function query_update($sql, $params = array())
{
if (empty($params)) {
mysqli_query($this->link, $sql);
} else {
$stmt = $this->prepare($sql, $params);
$stmt->execute();
}
return mysqli_affected_rows($this->link);
}
}
?>
