<?php
namespace Salas\Core\Auth;

use Salas\Core\Auth\AuthContext;
use Salas\Core\Auth\AuthorizationService;
use Salas\Core\Persistence\Database;


/**
 * Classe AuthorizationMiddleware.
 */
class AuthorizationMiddleware
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
     * Método authorize.
     * @param string $action
     * @param array $query
     * @param array $post
     * @return void
     */
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

        if (!$this->isAllowed($action, $auth->id(), $auth->level(), $query, $post)) {
            $isAjax = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
            if ($isAjax) {
                http_response_code(403);
                exit;
            }
            header('Location: ' . app_url());
            exit;
        }
    }

    public function isAllowed(
        string $action,
        int $userId,
        int $level,
        array $query = [],
        array $post = []
    ): bool {
        return (new AuthorizationService($this->db))->validaRota(
            $action,
            $level,
            $userId,
            $query,
            $post
        );
    }
}
