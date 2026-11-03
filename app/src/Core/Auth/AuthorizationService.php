<?php
namespace Salas\Core\Auth;


use Salas\Core\Persistence\Database;
use Salas\Models\Permissoes;

/**
 * Classe AuthorizationService.
 */
class AuthorizationService
{
    private const NIVEIS = [
        0 => ['uexterno', 'u', 'User'],
        1 => ['uinterno', 'g', 'Gestor'],
        2 => ['admin', 'a', 'Admin'],
        3 => ['superadmin', 'sa', 'SuperAdmin'],
    ];

    private const ROUTE_PERMISSIONS = [
        'dashboard' => 'M_Dashboard',
        'categoria_list' => 'M_Categorias',
        'categoria_form' => 'M_Categorias',
        'uso_list' => 'M_Usos',
        'uso_form' => 'M_Usos',
        'sala_list' => 'M_Salas',
        'sala_form' => 'M_Salas',
        'imagens_list' => 'M_Salas',
        'periodo_list' => 'M_Periodos',
        'periodo_form' => 'M_Periodos',
        'usuario_list' => 'M_Utilizadores',
        'usuario_form' => 'M_Utilizadores',
        'permissoes_form' => 'M_Permissoes',
        'estatisticas' => 'M_Estatisticas',
        'relatorios' => 'M_Relatorios',
        'temas' => 'M_Temas',
        'reserva_form' => 'M_Reservas',
    ];

    private Database $db;
    private array $permissions = [];
    private array $niveis = self::NIVEIS;

    /**
     * Método __construct.
     * @param Database $db
     * @return void
     */
    public function __construct(Database $db)
    {
        $this->db = $db;

        $model = new Permissoes($db);
        $linhas = $model->listar();

        foreach ($linhas as $l) {
            $this->permissions[$l['nome']] = [
                $l[$this->niveis[0][0]],
                $l[$this->niveis[1][0]],
                $l[$this->niveis[2][0]],
            ];
        }

        foreach (Permissoes::DEFAULTS as $nome => $default) {
            if (!array_key_exists($nome, $this->permissions)) {
                $this->permissions[$nome] = [
                    (int) (bool) $default[1],
                    (int) (bool) $default[2],
                    (int) (bool) $default[3],
                ];
            }
        }
    }
    /**
     * Método niveis.
     * @return array
     */
    public function niveis(): array
    {
        return $this->niveis;
    }



    /**
     * Método validaPermissao.
     * @param string $permissao
     * @param int $nivel
     * @return bool
     */
    public function validaPermissao(string $permissao, int $nivel): bool
    {
        if (
            !array_key_exists($permissao, Permissoes::DEFAULTS)
            || !array_key_exists($nivel, $this->niveis)
        ) {
            return false;
        }

        return $this->niveis[$nivel][0] === 'superadmin'
            || !empty($this->permissions[$permissao][$nivel]);
    }

    public function registoPublicoAtivo(): bool
    {
        return $this->validaPermissao('A_RegNovoUtilizador', 0);
    }

    public function validaRota(
        string $action,
        int $nivel,
        int $userId = 0,
        array $query = [],
        array $post = []
    ): bool {
        $permission = self::permissionForRoute($action);
        if ($permission === null || !array_key_exists($permission, Permissoes::DEFAULTS)) {
            return false;
        }

        if (
            $action === 'usuario_form'
            && $userId > 0
            && (int) ($post['id'] ?? $query['id'] ?? 0) === $userId
        ) {
            return true;
        }

        return $this->validaPermissao($permission, $nivel);
    }

    public static function permissionForRoute(string $action): ?string
    {
        return self::ROUTE_PERMISSIONS[$action] ?? null;
    }

    public static function routeWarnings(array $routeActions): array
    {
        $warnings = [];
        foreach ($routeActions as $action) {
            $permission = self::permissionForRoute($action);
            if ($permission === null) {
                $warnings[$action] = 'Sem permissão configurada.';
            } elseif (!array_key_exists($permission, Permissoes::DEFAULTS)) {
                $warnings[$action] = "A permissão '{$permission}' não existe em DEFAULTS.";
            }
        }

        return $warnings;
    }

    /**
     * Método nomeNivel.
     * @param int $nivel
     * @param int $tipoNome
     * @return string
     */
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

    /**
     * Método maxLevel.
     * @param int $userLevel
     * @return int
     */
    public function maxLevel(int $userLevel): int
    {
        return min(count($this->niveis) - 1, $userLevel);
    }

    public static function canAssignLevel(int $actorLevel, int $assignedLevel): bool
    {
        return array_key_exists($actorLevel, self::NIVEIS)
            && array_key_exists($assignedLevel, self::NIVEIS)
            && $assignedLevel <= $actorLevel;
    }
}
