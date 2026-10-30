<?php
namespace Salas\Controllers;


use Salas\Core\Auth\AuthContext;
use Salas\Core\Auth\AuthorizationService;
use Salas\Core\Persistence\Database;
use Salas\Models\Reserva;
use Salas\Models\Usuario;
use Salas\Core\Validation\Validator;


class UsuarioController
	{
	private Database $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	public function salvar(?array $post = null)
		{
		$post = $post ?? $_POST;
		if (isset($post['salvar']) || isset($post['validar'])) {

			$nome = Validator::string($post['nome']);
			$email = Validator::string($post['email']);
			$senha = isset($post['senha']) ? trim($post['senha']) : null;

			$id = Validator::string($post['id']);


			if (isset($post['salvar'])) {
				$telefone = Validator::string(isset($post['telefone']) ? $post['telefone'] : '');
				$NIF = Validator::string(isset($post['NIF']) ? $post['NIF'] : null);
				$nivel = Validator::string((isset($post['nivel']) ? $post['nivel'] : ''));
				}
			else {
				$telefone = '';
				$NIF = '';
				$nivel = 0;
				}

			if ($senha === '') {
				$senha = null;
				}

			$usuario = new Usuario($this->db);

			$resultado = $usuario->findByLoginIdentifier($email);
			if (isset($resultado[0]['id']) && $resultado[0]['id'] != $id) {
				return 0;  // não pode criar um usuário com email ou nome já existente
				}

			// O nome é obrigatório
			if (strlen($nome) > 0) {
				$resultado = $usuario->salvar($id, $nome, $email, $senha, $telefone, $NIF, $nivel);
				unset($_SESSION['dashboard_cache_' . (int) ($_SESSION['user_id'] ?? 0)]);
				}

			// Se a gravação foi feita do ecrã de login
			// Ainda não está logado e não tem sessão -> nada a fazer
			if (isset($post['validar'])) {
				return $resultado;
				}

						if ($id == (new AuthContext())->id()) {
				// se alterou o nome do usuario logado, atualiza o nome na sessao
				$this->startSession(array('id' => $id, 'nome' => $nome, 'nivel' => $nivel));
				}

			return $resultado;

			}
		}

		public function excluir()
	{
		if (isset($_POST['excluir'])) {
			$id = Validator::string($_POST['id']);

			$usuario = new Usuario($this->db);
			
			// CORREÇÃO 1: Try/Catch para capturar o erro fatal do PHP moderno
			try {
				$res = $usuario->excluir($id);
			} catch (\PDOException $e) {
				// Se a base de dados bloquear devido a reservas associadas, 
				// definimos uma string de erro para simular o comportamento antigo
				$res = "Utilizador não pode ser excluído. Existem reservas em seu nome.";
			}

			if (is_numeric($res)) {
				// CORREÇÃO 2: Atualizar o redirecionamento para a nova rota pública
				header('Location: ' . app_url('?acao=usuario_list'));
				exit();
			} else {
				return $res;
			}
		}
		return false;
	}

	public function cancelar()
		{
		if (isset($_POST['cancelar'])) {

			header('Location: ' . app_url('?acao=usuario_list'));
			exit();
			}
		return false;
		}

	public function abrir()
		{

		if (isset($_GET['id']) && is_numeric($_GET['id'])) {
			$usuario = new Usuario($this->db);
			return $usuario->abrir($_GET['id']);
			}

		}

	// listagem
	public function listarController()
		{
		$usuario = new Usuario($this->db);
		return $usuario->listar();
		}

	public function startSession(array $r)
		{
		$this->sessionAdapter()->updateIdentity($r);
		}

	//  NOVO MÉTODO CENTRALIZADO DE LOGOUT
	public function logout()
		{
		$this->sessionAdapter()->logout();

		// 4. Redireciona com segurança para a rota pública do login
		header('Location: ' . app_url('?acao=login'));
		exit;
		}

	private function sessionAdapter(): \Salas\Core\Auth\SalasSessionAdapter
		{
		return new \Salas\Core\Auth\SalasSessionAdapter();
		}


	    public function listarReservasPorUsuario(int $id): array
    {
        $reserva = new Reserva($this->db);
        return $reserva->listarPorUsuario($id);
    }

    public function excluirReservasPorUsuario(int $id): int
    {
        $reserva = new Reserva($this->db);
        return (int) $reserva->excluirPorUsuario($id);
    }

    public function usuarioForm(array $query, array $post): array
    {
        $controller = $this;
        if ($post !== []) {
            $controller->cancelar();
        }
        $auth = new AuthContext();
        $reservasUsuario = [];
        $podeEliminarReservas = (new AuthorizationService($this->db))->validaPermissao('M_EliminarReservasUtilizador', $auth->level());
        $error = '';
        $isSelf = (int) ($post['id'] ?? $query['id'] ?? 0) === $auth->id();
        if (isset($post['eliminar_reservas']) && !$isSelf && $podeEliminarReservas) {
            $targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
            $controller->excluirReservasPorUsuario($targetId);
            $error = 'Reservas do utilizador eliminadas com sucesso.';
        }
        if (isset($post['eliminar_reserva'])) {
            $reservaId = (int) ($post['reserva_id'] ?? 0);
            if ($reservaId > 0 && ($podeEliminarReservas || $isSelf)) {
                (new Reserva($this->db))->excluir($reservaId);
                $error = 'Reserva eliminada com sucesso.';
            }
        }

        $deleteResult = ($post !== [] && !$isSelf) ? $controller->excluir() : false;
        $error = isset($post['excluir']) && is_string($deleteResult)
            ? 'Utilizador não pode ser excluído. Existem reservas em seu nome.'
            : $error;

        if (isset($post['excluir']) && is_string($deleteResult)) {
            $targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
            $reservasUsuario = $controller->listarReservasPorUsuario($targetId);
        }
        if ($isSelf && $post !== []) {
            $post['nivel'] = (string) $auth->level();
        }
        $saveResult = $post !== [] ? $controller->salvar($post) : null;
        if ($saveResult === 0) {
            $error = 'Não é possível criar/editar: Nome ou E-mail já existentes no sistema!';
        } elseif ($saveResult !== null && isset($post['salvar'])) {
            header('Location: ' . app_url('?acao=usuario_list'));
            exit;
        }

        $record = [];
        $targetId = (int) ($post['id'] ?? $query['id'] ?? 0);
        if (!isset($_GET['id']) && $targetId > 0) {
            $_GET['id'] = $targetId;
        }
        if (!isset($post['salvar'])) {
            $opened = $controller->abrir();
            $record = $opened[0] ?? [];
            if (empty($record) && isset($post['id'])) {
                $record = [
                    'id' => Validator::int($post['id'] ?? 0),
                    'nome' => Validator::string($post['nome'] ?? ''),
                    'email' => Validator::string($post['email'] ?? ''),
                    'telefone' => Validator::string($post['telefone'] ?? ''),
                    'NIF' => Validator::string($post['NIF'] ?? ''),
                    'nivel' => Validator::int($post['nivel'] ?? 0),
                ];
            }
        } else {
            $record = [
                'id' => (int) ($post['id'] ?? 0),
                'nome' => Validator::string($post['nome'] ?? ''),
                'email' => Validator::string($post['email'] ?? ''),
                'telefone' => Validator::string($post['telefone'] ?? ''),
                'NIF' => Validator::string($post['NIF'] ?? ''),
                'nivel' => (int) ($post['nivel'] ?? 0),
            ];
        }

        if ($targetId > 0) {
            $reservasUsuario = $controller->listarReservasPorUsuario($targetId);
        }

        $permissionController = new AuthorizationService($this->db);
        $maxLevel = min(count($permissionController->niveis()) - 1, (new AuthContext())->level());
        $levelOptions = [];
        for ($level = 0; $level <= $maxLevel; $level++) {
            $levelOptions[] = [
                'value' => $level,
                'label' => $permissionController->nomeNivel($level, 2) . ' (' . $permissionController->nomeNivel($level, 1) . ')',
            ];
        }

        return array_merge([
            'id' => 0,
            'nome' => '',
            'email' => '',
            'senha' => '',
            'telefone' => '',
            'NIF' => '',
            'nivel' => 0,
            'errormsg' => $error,
            'reservasUsuario' => $reservasUsuario,
            'podeEliminarReservas' => $podeEliminarReservas,
            'levelOptions' => $levelOptions,
            'sessionUserName' => (new AuthContext())->name(),
            'sessionUserLevel' => (new AuthContext())->level(),
        ], $record);
    }




}


?>
