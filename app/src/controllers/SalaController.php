<?php

require_once __DIR__ . '/../infrastructure/Support/Util.php';
require_once __DIR__ . '/../application/Services/SalaService.php';
require_once __DIR__ . '/../infrastructure/Validation/Validator.php';

class SalaController
{
    private Database $db;
    private SalaService $service;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->service = new SalaService($db);
    }

    public function salvar($categoria_filtro, $activas_filtro): bool
    {
        if (!isset($_POST['salvar'])) {
            return false;
        }

        $id = Validator::int($_POST['id'] ?? 0);
        $nome = Validator::string($_POST['nome'] ?? '');
        $descricao = Validator::string($_POST['descricao'] ?? '');
        $places = Validator::int($_POST['lugares'] ?? 0);
        $categoryId = Validator::int($_POST['categoria_id'] ?? 0);
        $active = isset($_POST['activa']);
        $imageId = Validator::string($_POST['imagem_id'] ?? '');

        $uploadedImage = Util::imagemUpload($_FILES['imagem']['tmp_name'] ?? '');
        $this->service->save($id, $nome, $descricao, $categoryId, $places, $active, $imageId, is_array($uploadedImage) ? $uploadedImage : false);

        header('Location: ' . app_url('?acao=sala_list&categoria_id=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)));
        exit();
    }

    public function excluir($categoria_filtro, $activas_filtro): bool
    {
        if (!isset($_POST['excluir'])) {
            return false;
        }

        $id = Validator::int($_POST['id'] ?? 0);

        $success = $this->service->delete($id, Validator::string($_POST['imagem_id'] ?? ''));
        if (!$success) {
            $errormsg = 'Erro ao excluir a sala. Existem reservas.';
            header('Location: ' . app_url('?acao=sala_form&id=' . $id . '&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&errormsg=' . urlencode($errormsg)));
            exit();
        }

        header('Location: ' . app_url('?acao=sala_list&categoria_id=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)));
        exit();
    }

    public function cancelar($categoria_filtro, $activas_filtro): bool
    {
        if (isset($_POST['cancelar'])) {
            header('Location: ' . app_url('?acao=sala_list&categoria_id=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)));
            exit();
        }

        return false;
    }

    public function nomeSala($sala_id): string
    {
        return $this->service->name((int) $sala_id);
    }

    public function abrir(): array
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = Validator::int($_GET['id']);
            return $this->service->open($id);
        }

        return [];
    }

    public function listarController($filtro_categoria_id = 0, $filtro_activas = 'activas'): array
    {
        return $this->service->list((int) $filtro_categoria_id, (string) $filtro_activas);
    }
}
