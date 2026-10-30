<?php
namespace Salas\Models;

use Salas\Core\Persistence\Database;

class Permissoes
{
	
	private $db = null;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	public function listar($tipo = 't')
	{
		//  Permissoes são tipo do tipo 
		// 		s -> simples  (só o campo uexterno é usado)
		// 		n -> por niveis (são usados os três campos uexterno, uinterno, admin)
        if ($tipo !== 't') {
            $sql = 'select seq, nome, tipo, uexterno, uinterno, admin from permissoes where tipo = ? order by seq;';
            return $this->db->query($sql, [$tipo]);
        }

        $sql = 'select seq, nome, tipo, uexterno, uinterno, admin from permissoes order by seq;';
        return $this->db->query($sql);
    }


    public function salvar($seq, $nome, $tipo, $uexterno, $uinterno, $admin)
    {
        // inserir nova permissao
        $sql = 'insert into permissoes (seq, nome, tipo, uexterno, uinterno, admin) values (?, ?, ?, ?, ?, ?)';
        return $this->db->query_insert($sql, [$seq, $nome, $tipo, (int) $uexterno, (int) $uinterno, (int) $admin]);
    }

    public function update($seq, $nome, $tipo, $uexterno, $uinterno, $admin, $comando)
    {
        // atualizar permissao existente
        if ($comando == "Update") {
            $sql = 'update permissoes set uexterno = ?, uinterno = ?, admin = ? where nome = ?';
            return $this->db->query_update($sql, [(int) $uexterno, (int) $uinterno, (int) $admin, $nome]);
        }
        // actualizar apenas sequência
        else if ($comando == "Renumera") {
            $sql = 'update permissoes set seq = ? where nome = ?';
            return $this->db->query_update($sql, [$seq, $nome]);
        }

    }

    public function renumerar($seq, $nome)
    {		
        // actualizar apenas sequência		
        $sql = 'update permissoes set seq = ? where nome = ?';
        return $this->db->query_update($sql, [$seq, $nome]);
    }

    public function excluir($nome)
    {
        $sql = 'delete from permissoes where nome = ?';
        return $this->db->query_update($sql, [$nome]);
    }



    public const DEFAULTS = [
        "M_Categorias" => ['n', false, true, false, "Autoriza acesso ao Menu de Categorias"],
        "M_Usos" => ['n', false, true, false, "Autoriza a gestão de Usos"],
        "M_Salas" => ['n', false, true, false, "Autoriza acesso ao Menu de Salas"],
        "M_Periodos" => ['n', false, true, false, "Autoriza acesso ao Menu de Períodos"],
        "M_Utilizadores" => ['n', false, true, false, "Autoriza acesso ao Menu de Utilizadores"],
        "M_Permissoes" => ['n', false, true, false, "Autoriza acesso ao Menu de Permissões"],
        "M_Estatisticas" => ['n', false, true, false, "Autoriza acesso ao Menu de Estatísticas"],
        "M_Relatorios" => ['n', false, true, false, "Autoriza acesso ao Menu de Relatórios"],
        "M_Temas" => ['n', false, true, true, "Autoriza acesso à gestão de temas"],
        "M_Reservas" => ['n', true, true, true, "Autoriza acesso ao formulário de reservas e exclusão de reservas"],
        "M_EliminarReservasUtilizador" => ['n', false, true, true, "Autoriza eliminar todas as reservas de um utilizador"],
        "M_FiltroUtilizador" => ['n', false, true, true, "Autoriza o filtro por utilizador no dashboard"],
        "A_RegNovoUtilizador" => ['s', true, null, null, "Activa o registo de novos utilizadores no ecrã de login. (não implementado)"],
        "A_PerfilUtilizador" => ['s', true, null, null, "Autoriza a edição do próprio Perfil, diretamente no Menu. (não implementado)"],
    ];

    public function ensureDefaults(): void
    {
        $linhas = $this->listar();
        $ctrl_seq = ['' => '0'];
        foreach ($linhas as $linha) {
            if (!array_key_exists($linha['nome'], self::DEFAULTS)) {
                $this->excluir($linha['nome']);
            } else {
                $ctrl_seq[$linha['nome']] = $linha['seq'];
            }
        }

        $i = 0;
        foreach (self::DEFAULTS as $nome => $value) {
            if (!array_key_exists($nome, $ctrl_seq)) {
                $this->salvar(
                    $i,
                    $nome,
                    $value[0],
                    $value[1],
                    $value[2],
                    $value[3]
                );
            } elseif ($i != $ctrl_seq[$nome]) {
                $this->renumerar($i, $nome);
            }
            $i++;
        }
    }
}
?>
