<?php
namespace Salas\Controllers;


use Salas\Core\Persistence\Database;
use Salas\Models\Uso;
use Salas\Core\Validation\Validator;

class UsoController
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function salvar(): bool
    {
        if (!isset($_POST['salvar'])) {
            return false;
        }

        $id = (int) Validator::int($_POST['id'] ?? 0);
        $nome = Validator::string($_POST['nome'] ?? '');
        $descricao = Validator::string($_POST['descricao'] ?? '');

        $uso = new Uso($this->db);
        if (strlen($nome) > 0) {
            $uso->salvar($id, $nome, $descricao);
        }

        header('Location: ' . app_url('?acao=uso_list'));
        exit();
    }

    public function excluir(): bool
    {
        if (!isset($_POST['excluir'])) {
            return false;
        }

        $id = (int) Validator::int($_POST['id'] ?? 0);
        $uso = new Uso($this->db);
        $uso->excluir($id);

        header('Location: ' . app_url('?acao=uso_list'));
        exit();
    }

    public function cancelar(): bool
    {
        if (isset($_POST['cancelar'])) {
            header('Location: ' . app_url('?acao=uso_list'));
            exit();
        }
        return false;
    }

    public function abrir(): array|false
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $uso = new Uso($this->db);
            return $uso->abrir((int) $_GET['id']);
        }
        return false;
    }

    public function listarController(): array
    {
        $uso = new Uso($this->db);
        return $uso->listar();
    }

    public function listarOpcoesController(): array
    {
        $uso = new Uso($this->db);
        return $uso->listar();
    }
    public function usoForm(array $query, array $post = []): array
    {
        $controller = $this;
        if ($post !== []) {
            $controller->cancelar();
            $controller->excluir();
            $controller->salvar();
        }
        $record = $controller->abrir();
        return array_merge([
            'id' => 0,
            'nome' => '',
            'descricao' => '',
            'errormsg' => (string) ($query['errormsg'] ?? ''),
        ], is_array($record) ? $record[0] ?? [] : []);
    }




}
?>
