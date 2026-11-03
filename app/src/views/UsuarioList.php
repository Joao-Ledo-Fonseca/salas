<?php
use Salas\Core\Support\Csrf;
use Salas\Models\Reserva;
use Salas\Models\Sala;
use Salas\Models\Categoria;
use Salas\Models\Usuario;
use Salas\Models\Periodo;
use Salas\Models\Uso;
use Salas\Controllers\SalaController;
use Salas\Controllers\CategoriaController;
use Salas\Controllers\UsuarioController;
use Salas\Controllers\ReservaController;
use Salas\Controllers\PeriodoController;
use Salas\Controllers\UsoController;
use Salas\Controllers\PermissoesController;
use Salas\Core\Validation\Validator;
?>

 <?php
  $titulo = 'Lista de Utilizadores';
  include __DIR__ . "/components/cabecalho2.php";
  ?>



<div class="lista_comum container_top" style="max-height:60vh; overflow-y:auto;">
    <form class="form_sel" name="form1" method="post" target="_self">
        <input type="button" name="novo" value="Novo" class="btn1" onclick="abre('usuario_form.php')" />
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
    </form>
</div>

<div class="container_conteudo" style="max-height:60vh; overflow-y:auto;">
    <?php
    $colunas = [
        ['chave' => 'nome', 'label' => 'Nome', 'largura' => '150px', 'url' => app_url('?acao=usuario_form&id=%id%')],
        ['chave' => 'email', 'label' => 'E-mail', 'largura' => '150px'],
        ['chave' => 'nivel_nome', 'label' => 'Nível', 'largura' => '100px'],
        ['chave' => 'id', 'label' => 'ID', 'largura' => '50px', 'formato' => 'int'],
    ];
    $classe_tabela = 'lista_comum';
    include __DIR__ . '/components/ListaComum.php';
    ?>
</div>
