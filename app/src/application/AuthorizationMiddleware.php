<?php

require_once __DIR__ . '/../infrastructure/Auth/AuthContext.php';
require_once __DIR__ . '/../infrastructure/Auth/SalasSessionAdapter.php';
require_once __DIR__ . '/Services/AuthorizationService.php';

class AuthorizationMiddleware
{
	private const PAGE_PERMISSIONS = [
		'categoria_list' => 'M_Categorias',
		'categoria_form' => 'M_Categorias',
		'sala_list' => 'M_Salas',
		'sala_form' => 'M_Salas',
		'periodo_list' => 'M_Periodos',
		'periodo_form' => 'M_Periodos',
		'usuario_list' => 'M_Utilizadores',
		'permissoes_form' => 'M_Permissoes',
		'estatisticas' => 'M_Estatisticas',
		'relatorios' => 'M_Relatorios',
		'reserva_form' => 'M_Reservas',
	];

    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function authorize(string $action, array $query, array $post): void
    {
        $auth = new AuthContext();
        if (!$auth->isAuthenticated()) {
            $isAjax = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
            if ($isAjax) {
                http_response_code(401);
                exit;
            }
            header('Location: ' . app_url('?acao=login'));
            exit;
        }

        $permission = self::PAGE_PERMISSIONS[$action] ?? null;
        $level = $auth->level();

        if ($action === 'usuario_form') {
            $targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
            if ($targetId === $auth->id()) {
                return;
            }
            $permission = 'M_Utilizadores';
        }

        if ($action === 'dashboard') {
            return;
        }

        if ($permission !== null && !(new AuthorizationService($this->db))->validaPermissao($permission, $level)) {
            $isAjax = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
            if ($isAjax) {
                http_response_code(403);
                exit;
            }
            header('Location: ' . app_url());
            exit;
        }
    }
}
