<?php
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



<?php
$acoesLista = ['createUrl' => 'usuario_form.php'];
include __DIR__ . '/components/AcoesLista.php';
?>

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
