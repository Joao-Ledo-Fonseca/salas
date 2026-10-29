<?php
namespace Salas\Controllers;


use Salas\Application\Services\CategoryService;
use Salas\Infrastructure\Persistence\Database;
use Salas\Infrastructure\Support\Util;
use Salas\Infrastructure\Validation\Validator;

class CategoriaController
{
    private Database $db;
    private CategoryService $service;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->service = new CategoryService($db);
    }

    public function salvar(): bool
    {
        if (!isset($_POST['salvar'])) {
            return false;
        }

        $id = (int) Validator::int($_POST['id'] ?? 0);
        $nome = Validator::string($_POST['nome'] ?? '');
        $descricao = Validator::string($_POST['descricao'] ?? '');
        $imagemId = Validator::string($_POST['imagem_id'] ?? '');

        $uploadedImage = Util::imagemUpload($_FILES['imagem']['tmp_name'] ?? '');
        $this->service->save($id, $nome, $descricao, $imagemId, is_array($uploadedImage) ? $uploadedImage : false);

        unset($_SESSION['dashboard_cache_' . (int) ($_SESSION['user_id'] ?? 0)]);
        header('Location: ' . app_url('?acao=categoria_list'));
        exit();
    }

    public function excluir(): bool
    {
        if (!isset($_POST['excluir'])) {
            return false;
        }

        $id = (int) Validator::int($_POST['id'] ?? 0);
        $imagemId = Validator::string($_POST['imagem_id'] ?? '');

        $success = $this->service->delete($id, $imagemId);
        if (!$success) {
            $errormsg = 'Erro ao excluir a categoria! Existem salas na categoria.';
            header('Location: ' . app_url('?acao=categoria_form&id=' . $id . '&errormsg=' . urlencode($errormsg)));
            exit();
        }

        header('Location: ' . app_url('?acao=categoria_list'));
        exit();
    }

    public function cancelar(): bool
    {
        if (isset($_POST['cancelar'])) {
            header('Location: ' . app_url('?acao=categoria_list'));
            exit();
        }

        return false;
    }

    public function abrir(): array|false
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int) Validator::int($_GET['id'] ?? 0);
            return $this->service->open($id);
        }

        return false;
    }

    public function listarController(): array
    {
        return $this->service->list();
    }

    public function listarOpcoesController(): array
    {
        return $this->service->list();
    }
}
