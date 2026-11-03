<?php
namespace Salas\Controllers;

use Salas\Core\Persistence\Database;
use Salas\Models\Periodo;
use Salas\Core\Validation\Validator;

/**
 * Classe PeriodoController.
 */
class PeriodoController
{
    private Database $db;

    /**
     * Método __construct.
     * @param Database $db
     * @return void
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Método salvar.
     * @return bool
     */
    public function salvar(): bool
    {
        if (!isset($_POST['salvar'])) {
            return false;
        }

        $id = Validator::int($_POST['id'] ?? 0);
        $seq = Validator::int($_POST['seq'] ?? 0);
        $nome = Validator::string($_POST['nome'] ?? '');

        $periodo = new Periodo($this->db);
        $periodo->salvar($id, $nome, $seq);

        unset($_SESSION['dashboard_cache_' . (int) ($_SESSION['user_id'] ?? 0)]);
        header('Location: ' . app_url('?acao=periodo_list'));
        exit();
    }

    /**
     * Método cancelar.
     * @return bool
     */
    public function cancelar(): bool
    {
        if (isset($_POST['cancelar'])) {
            header('Location: ' . app_url('?acao=periodo_list'));
            exit();
        }

        return false;
    }

    /**
     * Método excluir.
     * @return bool
     */
    public function excluir(): bool
    {
        if (!isset($_POST['excluir'])) {
            return false;
        }

        $id = Validator::int($_POST['id'] ?? 0);
        $periodo = new Periodo($this->db);
        $success = $periodo->excluir($id);
        if (!$success) {
            $errormsg = 'Erro ao excluir o período! Existem reservas associadas.';
            header('Location: ' . app_url('?acao=periodo_form&id=' . $id . '&errormsg=' . urlencode($errormsg)));
            exit();
        }

        header('Location: ' . app_url('?acao=periodo_list'));
        exit();
    }

    /**
     * Método nomePeriodo.
     * @param mixed $periodo_id
     * @return string
     */
    public function nomePeriodo($periodo_id): string
    {
        $periodo = new Periodo($this->db);
        return $periodo->name((int) $periodo_id);
    }

    /**
     * Método abrir.
     * @return array
     */
    public function abrir(): array
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = Validator::int($_GET['id']);
            $periodo = new Periodo($this->db);
            return $periodo->abrir($id);
        }

        return [];
    }

    /**
     * Método listarController.
     * @return array
     */
    public function listarController(): array
    {
        $periodo = new Periodo($this->db);
        return $periodo->listar();
    }
    /**
     * Método periodoForm.
     * @param array $query
     * @param array $post
     * @return array
     */
    public function periodoForm(array $query, array $post = []): array
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
            'seq' => 0,
            'errormsg' => (string) ($query['errormsg'] ?? ''),
        ], isset($record[0]) ? $record[0] : []);
    }




}
