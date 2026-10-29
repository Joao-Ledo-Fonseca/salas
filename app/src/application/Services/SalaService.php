<?php
namespace Salas\Application\Services;


use Salas\Infrastructure\Persistence\Database;
use Salas\Infrastructure\Persistence\Repositories\ImageRepository;
use Salas\Infrastructure\Persistence\Repositories\SalaRepository;

class SalaService
{
    private SalaRepository $rooms;
    private ImageRepository $images;

    public function __construct(Database $db)
    {
        $this->rooms = new SalaRepository($db);
        $this->images = new ImageRepository($db);
    }

    public function list(int $categoryId = 0, string $activeFilter = 'activas'): array
    {
        return $this->rooms->list($categoryId, $activeFilter);
    }

    public function open(int $id): array
    {
        $room = $this->rooms->find($id);
        if (!isset($room[0])) {
            return [];
        }

        $imageId = $room[0]['imagem_id'] ?? null;
        $image = ['id' => 0, 'nome_imagem' => '', 'tamanho_imagem' => '', 'tipo_imagem' => '', 'imagem' => ''];

        if ($imageId !== null) {
            $found = $this->images->find((int) $imageId);
            if (isset($found[0])) {
                $image = $found[0];
            }
        }

        return array_merge($image, $room[0]);
    }

    public function save(int $id, string $nome, string $descricao, int $categoryId, int $places, bool $active, $imageId, $uploadedImage): void
    {
        if (is_array($uploadedImage)) {
            if ($imageId !== 0 && $imageId !== '' && $imageId !== null) {
                $this->images->delete((int) $imageId);
            }

            $imageId = $this->images->save(
                0,
                $uploadedImage['nome_img'],
                $uploadedImage['tamanho_img'],
                $uploadedImage['tipo_img'],
                $uploadedImage['conteudo']
            );
        }

        if (strlen($nome) > 0) {
            $this->rooms->save($id, $nome, $descricao, $categoryId, $places, $active, $imageId);
        }
    }

    public function delete(int $id, $imageId): bool
    {
        try {
            $this->rooms->delete($id);
        } catch (\PDOException $e) {
            return false;
        }

        if ($imageId !== 0 && $imageId !== '' && $imageId !== null) {
            $this->images->delete((int) $imageId);
        }

        return true;
    }

    public function name(int $id): string
    {
        return $this->rooms->name($id);
    }
}
