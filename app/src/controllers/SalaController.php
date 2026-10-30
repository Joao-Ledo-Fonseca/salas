<?php
namespace Salas\Controllers;

use Salas\Core\Persistence\Database;
use Salas\Models\Reserva;
use Salas\Models\Sala;
use Salas\Models\Imagem;
use Salas\Core\Support\DateUtil;
use Salas\Core\Support\ImageUtil;
use Salas\Core\Validation\Validator;
use Salas\Core\Auth\AuthContext;
use Salas\Core\Auth\AuthorizationService;

class SalaController
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
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

        $uploadedImage = ImageUtil::imagemUpload($_FILES['imagem']['tmp_name'] ?? '');
        $sala = new Sala($this->db);
        $sala->salvar($id, $nome, $descricao, $categoryId, $places, $active, $imageId, is_array($uploadedImage) ? $uploadedImage : false);

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

        $sala = new Sala($this->db);
        $success = $sala->excluir($id, Validator::string($_POST['imagem_id'] ?? ''));
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
        $sala = new Sala($this->db);
        return $sala->name((int) $sala_id);
    }

    public function abrir(): array
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = Validator::int($_GET['id']);
            $sala = new Sala($this->db);
            return $sala->abrir($id);
        }

        return [];
    }

    public function listarController($filtro_categoria_id = 0, $filtro_activas = 'activas'): array
    {
        $sala = new Sala($this->db);
        return $sala->listar((int) $filtro_categoria_id, (string) $filtro_activas);
    }

    public function listarReservasPorSala(int $id): array
    {
        $reserva = new Reserva($this->db);
        return $reserva->listarPorSala($id);
    }

    public function excluirReservasPorSala(int $id): int
    {
        $reserva = new Reserva($this->db);
        return (int) $reserva->excluirPorSala($id);
    }

    public function listarSalasPorCategoria(int $id): array
    {
        $sala = new Sala($this->db);
        return $sala->listarPorCategoria($id);
    }

    public function excluirImagem(int $imageId): bool
    {
        if ($imageId <= 0) {
            return false;
        }
        $sala = new Sala($this->db);
        $sala->nullifyImageId($imageId);
        $image = new Imagem($this->db);
        $image->excluir($imageId);
        return true;
    }

    public function listarImagensController(): array
    {
        $image = new Imagem($this->db);
        return $image->listar();
    }

    public function listarImagensJson(array $query): string
    {
        $page = isset($query['page']) ? max(1, (int) $query['page']) : 1;
        $perPage = isset($query['per_page']) ? max(1, min(50, (int) $query['per_page'])) : 12;
        $offset = ($page - 1) * $perPage;
        $image = new Imagem($this->db);
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
    public function salaList(array $query, array $post): array
    {
        $categoryId = (int) Validator::string($post['categoria_id'] ?? $query['categoria_id'] ?? 0);
        $activeFilter = (string) Validator::string($post['activas'] ?? $query['activas'] ?? 'activas');
        return [
            'categoria_id' => $categoryId,
            'activas' => $activeFilter,
            'categorias' => (new CategoriaController($this->db))->listarOpcoesController(),
            'lista' => $this->listarController($categoryId, $activeFilter),
        ];
    }

    public function salaForm(array $query, array $post): array
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        $categoryFilter = Validator::string($query['categoria_filtro'] ?? $post['categoria_filtro'] ?? 0);
        $activeFilter = (string) Validator::string($query['activas'] ?? $post['activas'] ?? 'activas');
        $returnTo = Validator::string($query['return_to'] ?? $post['return_to'] ?? '');
        $returnId = (int) Validator::string($query['return_id'] ?? $post['return_id'] ?? 0);
        $controller = $this;
        $auth = new AuthContext();
        $reservasSala = [];
        $podeEliminarReservas = (new AuthorizationService($this->db))->validaPermissao('M_EliminarReservasUtilizador', $auth->level());
        $error = '';
        if (isset($post['eliminar_reservas']) && $podeEliminarReservas) {
            $targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
            $controller->excluirReservasPorSala($targetId);
            $error = 'Reservas da sala eliminadas com sucesso.';
        }
        if (isset($post['eliminar_reserva'])) {
            $reservaId = (int) ($post['reserva_id'] ?? 0);
            if ($reservaId > 0 && $podeEliminarReservas) {
                (new Reserva($this->db))->excluir($reservaId);
                $error = 'Reserva eliminada com sucesso.';
            }
        }
        if (isset($post['eliminar_imagem'])) {
            $imageId = (int) ($post['imagem_id'] ?? 0);
            if ($imageId > 0) {
                $controller->excluirImagem($imageId);
                $error = 'Imagem eliminada com sucesso.';
            }
        }
        if ($post !== []) {
            $controller->cancelar($categoryFilter, $activeFilter, $returnTo, $returnId);
            $deleteResult = $controller->excluir($categoryFilter, $activeFilter, $returnTo, $returnId);
            if ($deleteResult === false) {
                $error = 'Sala não pode ser excluída. Existem reservas associadas.';
            }
            $controller->salvar($categoryFilter, $activeFilter, $returnTo, $returnId);
        }
        $record = $controller->abrir();
        $targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
        if ($targetId > 0) {
            $reservasSala = $controller->listarReservasPorSala($targetId);
        }
        return array_merge([
            'id' => 0,
            'nome' => '',
            'descricao' => '',
            'categoria_id' => $categoryFilter,
            'lugares' => 0,
            'activa' => true,
            'imagem_id' => '',
            'nome_img' => '',
            'tamanho_img' => '',
            'tipo_img' => '',
            'imagem' => '',
            'categoria_filtro' => $categoryFilter,
            'activas_filtro' => $activeFilter,
            'errormsg' => $error !== '' ? $error : (string) ($query['errormsg'] ?? ''),
            'reservasSala' => $reservasSala,
            'podeEliminarReservas' => $podeEliminarReservas,
            'return_to' => $returnTo,
            'return_id' => $returnId,
        ], is_array($record) ? $record[0] ?? [] : [], [
            'categorias' => (new CategoriaController($this->db))->listarOpcoesController(),
        ]);
    }




}
