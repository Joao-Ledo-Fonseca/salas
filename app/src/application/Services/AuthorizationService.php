<?php

require_once __DIR__ . '/../../models/Permissoes.php';
require_once __DIR__ . '/../../controllers/PermissoesController.php';

class AuthorizationService
{
    private Database $db;
    private array $permissions = [];

    public array $niveis = [
        0 => ['uexterno', 'u', 'User'],
        1 => ['uinterno', 'g', 'Gestor'],
        2 => ['admin', 'a', 'Admin'],
        3 => ['superadmin', 'sa', 'SuperAdmin'],
    ];

    public function __construct(Database $db)
    {
        $this->db = $db;
        // Garante que a tabela de permissões está inicializada.
        (new PermissoesController($this->db))->validarTabelaPermissoes();

        $model = new Permissoes($db);
        $linhas = $model->listar();

        foreach ($linhas as $l) {
            $this->permissions[$l['nome']] = [
                $l[$this->niveis[0][0]],
                $l[$this->niveis[1][0]],
                $l[$this->niveis[2][0]],
            ];
        }
    }

    public function validaPermissao(string $permissao, int $nivel): bool
    {
        return ($this->niveis[$nivel][0] ?? '') === 'superadmin'
            || (array_key_exists($permissao, $this->permissions) && !empty($this->permissions[$permissao][$nivel]));
    }

    public function nomeNivel(int $nivel, int $tipoNome = 0): string
    {
        if (!array_key_exists($nivel, $this->niveis)) {
            return 'N/A';
        }

        return match ($tipoNome) {
            0 => $this->niveis[$nivel][0],
            1 => $this->niveis[$nivel][1],
            2 => $this->niveis[$nivel][2],
            default => $this->niveis[$nivel][0],
        };
    }

    public function maxLevel(int $userLevel): int
    {
        return min(count($this->niveis) - 1, $userLevel);
    }
}
