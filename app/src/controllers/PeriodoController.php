<?php
namespace Salas\Controllers;

use Salas\Infrastructure\Persistence\Database;
use Salas\Models\Periodo;
use Salas\Infrastructure\Validation\Validator;

class PeriodoController
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

        $id = Validator::int($_POST['id'] ?? 0);
        $seq = Validator::int($_POST['seq'] ?? 0);
        $nome = Validator::string($_POST['nome'] ?? '');

        $periodo = new Periodo($this->db);
        $periodo->salvar($id, $nome, $seq);

        unset($_SESSION['dashboard_cache_' . (int) ($_SESSION['user_id'] ?? 0)]);
        header('Location: ' . app_url('?acao=periodo_list'));
        exit();
    }

    public function cancelar(): bool
    {
        if (isset($_POST['cancelar'])) {
            header('Location: ' . app_url('?acao=periodo_list'));
            exit();
        }

        return false;
    }

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

    public function nomePeriodo($periodo_id): string
    {
        $periodo = new Periodo($this->db);
        return $periodo->name((int) $periodo_id);
    }

    public function abrir(): array
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = Validator::int($_GET['id']);
            $periodo = new Periodo($this->db);
            return $periodo->abrir($id);
        }

        return [];
    }

    public function listarController(): array
    {
        $periodo = new Periodo($this->db);
        return $periodo->listar();
    }
}
