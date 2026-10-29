<?php
namespace Salas\Application\Services;


use Salas\Infrastructure\Persistence\Repositories\CategoryRepository;
use Salas\Infrastructure\Persistence\Database;
use Salas\Infrastructure\Persistence\Repositories\ImageRepository;

class CategoryService
{
    private CategoryRepository $categories;
    private ImageRepository $images;

    public function __construct(Database $db)
    {
        $this->categories = new CategoryRepository($db);
        $this->images = new ImageRepository($db);
    }

    public function list(): array
    {
        return $this->categories->list();
    }

    public function open(int $id): array
    {
        $category = $this->categories->find($id);
        if (!isset($category[0])) {
            return [];
        }

        $imageId = $category[0]['imagem_id'] ?? null;
        $image = ['id' => 0, 'nome_imagem' => '', 'tamanho_imagem' => '', 'tipo_imagem' => '', 'imagem' => ''];

        if ($imageId !== null) {
            $found = $this->images->find((int) $imageId);
            if (isset($found[0])) {
                $image = $found[0];
            }
        }

        return array_merge($image, $category[0]);
    }

    public function save(int $id, string $nome, string $descricao, $imagemId, $uploadedImage): void
    {
        if (is_array($uploadedImage)) {
            if ($imagemId !== 0 && $imagemId !== '' && $imagemId !== null) {
                $this->images->delete((int) $imagemId);
            }

            $imagemId = 0;
            $imagemId = $this->images->save(
                0,
                $uploadedImage['nome_img'],
                $uploadedImage['tamanho_img'],
                $uploadedImage['tipo_img'],
                $uploadedImage['conteudo']
            );
        }

        if (strlen($nome) > 0) {
            $this->categories->save($id, $nome, $descricao, $imagemId);
        }
    }

    public function delete(int $id, $imagemId): bool
    {
        try {
            $this->categories->delete($id);
        } catch (\PDOException $e) {
            return false;
        }

        if ($imagemId !== 0 && $imagemId !== '' && $imagemId !== null) {
            $this->images->delete((int) $imagemId);
        }

        return true;
    }
}
