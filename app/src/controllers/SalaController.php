<?php
namespace Salas\Controllers;


use Salas\Infrastructure\Persistence\Database;
use Salas\Infrastructure\Persistence\Repositories\ImageRepository;
use Salas\Models\Reserva;
use Salas\Models\Sala;
use Salas\Application\Services\SalaService;
use Salas\Infrastructure\Support\Util;
use Salas\Infrastructure\Validation\Validator;

class SalaController
{
    private Database $db;
    private SalaService $service;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->service = new SalaService($db);
    }

    public function salvar($categoria_filtro, $activas_filtro, $returnTo = '', $returnId = 0): bool
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

        if ($returnTo !== '') {
            header('Location: ' . app_url('?acao=' . $returnTo . '&id=' . rawurlencode((string) $returnId)));
        } else {
            header('Location: ' . app_url('?acao=sala_list&categoria_id=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)));
        }
        exit();
    }

    public function excluir($categoria_filtro, $activas_filtro, $returnTo = '', $returnId = 0): bool
    {
        if (!isset($_POST['excluir'])) {
            return false;
        }

        $id = Validator::int($_POST['id'] ?? 0);

        $success = $this->service->delete($id, Validator::string($_POST['imagem_id'] ?? ''));
        if (!$success) {
            $errormsg = 'Erro ao excluir a sala. Existem reservas.';
            header('Location: ' . app_url('?acao=sala_form&id=' . $id . '&categoria_filtro=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro) . '&errormsg=' . urlencode($errormsg) . ($returnTo !== '' ? '&return_to=' . rawurlencode($returnTo) . '&return_id=' . rawurlencode((string) $returnId) : '')));
            exit();
        }

        if ($returnTo !== '') {
            header('Location: ' . app_url('?acao=' . $returnTo . '&id=' . rawurlencode((string) $returnId)));
        } else {
            header('Location: ' . app_url('?acao=sala_list&categoria_id=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)));
        }
        exit();
    }

    public function cancelar($categoria_filtro, $activas_filtro, $returnTo = '', $returnId = 0): bool
    {
        if (isset($_POST['cancelar'])) {
            if ($returnTo !== '') {
                header('Location: ' . app_url('?acao=' . $returnTo . '&id=' . rawurlencode((string) $returnId)));
            } else {
                header('Location: ' . app_url('?acao=sala_list&categoria_id=' . rawurlencode((string) $categoria_filtro) . '&activas=' . rawurlencode((string) $activas_filtro)));
            }
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

    public function listarReservasSala(int $id): array
    {
        $reserva = new Reserva($this->db);
        return $reserva->listarPorSala($id);
    }

    public function eliminarReservasSala(int $id): int
    {
        $reserva = new Reserva($this->db);
        return (int) $reserva->excluirPorSala($id);
    }

    public function listarSalasCategoria(int $id): array
    {
        $sala = new Sala($this->db);
        return $sala->listarPorCategoria($id);
    }

    public function eliminarImagem(int $imageId): bool
    {
        if ($imageId <= 0) {
            return false;
        }
        $sala = new Sala($this->db);
        $sala->nullifyImageId($imageId);
        $image = new ImageRepository($this->db);
        $image->delete($imageId);
        return true;
    }

    public function listarImagensController(): array
    {
        $image = new ImageRepository($this->db);
        return $image->listar();
    }

    public function listarImagensJson(array $query): string
    {
        $page = isset($query['page']) ? max(1, (int) $query['page']) : 1;
        $perPage = isset($query['per_page']) ? max(1, min(50, (int) $query['per_page'])) : 12;
        $offset = ($page - 1) * $perPage;
        $image = new ImageRepository($this->db);
        $rows = $image->listarPaginado($offset, $perPage);
        $total = $image->contar();
        $images = [];
        foreach ($rows as $row) {
            $images[] = [
                'id' => (int) $row['id'],
                'nome' => (string) $row['nome_imagem'],
                'base64' => base64_encode($row['imagem']),
            ];
        }
        $hasMore = ($offset + count($rows)) < $total;
        return json_encode(['images' => $images, 'hasMore' => $hasMore], JSON_UNESCAPED_UNICODE);
    }
}
