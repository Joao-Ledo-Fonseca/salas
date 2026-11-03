<?php
namespace Salas\Controllers;

use Salas\Core\Auth\AuthorizationService;
use Salas\Core\Persistence\Database;
use Salas\Core\Router;
use Salas\Models\Permissoes;

class PermissoesController
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function cancelar(array $post): bool
    {
        if (isset($post['cancelar'])) {
            header('Location: ' . app_url());
            exit;
        }

        return false;
    }

    public function salvar(array $post): bool
    {
        if (!isset($post['salvar'])) {
            return false;
        }

        $permissoes = new Permissoes($this->db);
        foreach (self::permissoesDoFormulario($post) as $nome => $valores) {
            $permissoes->update(
                null,
                $nome,
                null,
                $valores[0],
                $valores[1],
                $valores[2],
                'Update'
            );
        }

        header('Location: ' . app_url('?acao=permissoes_form'));
        exit;
    }

    public static function permissoesDoFormulario(array $post): array
    {
        $resultado = [];
        foreach (Permissoes::DEFAULTS as $nome => $default) {
            $resultado[$nome] = [
                isset($post[$nome . '0']),
                $default[0] === 'n' && isset($post[$nome . '1']),
                $default[0] === 'n' && isset($post[$nome . '2']),
            ];
        }

        return $resultado;
    }

    public function validarTabelaPermissoes(): void
    {
        (new Permissoes($this->db))->ensureDefaults();
    }

    public function listar(string $tipo): array
    {
        $this->validarTabelaPermissoes();
        $linhas = (new Permissoes($this->db))->listar($tipo);

        foreach ($linhas as &$linha) {
            $linha['descricao'] = Permissoes::DEFAULTS[$linha['nome']][4];
        }
        unset($linha);

        return $linhas;
    }

    public function permissoesForm(array $post = []): array
    {
        if ($post !== []) {
            $this->cancelar($post);
            $this->salvar($post);
        }

        $authorization = new AuthorizationService($this->db);

        return [
            'tabela_n' => $this->listar('n'),
            'tabela_s' => $this->listar('s'),
            'nivelLabels' => [
                $authorization->nomeNivel(0, 2),
                $authorization->nomeNivel(1, 2),
                $authorization->nomeNivel(2, 2),
                $authorization->nomeNivel(3, 2),
            ],
            'routeWarnings' => AuthorizationService::routeWarnings(Router::routeActions()),
        ];
    }
}
