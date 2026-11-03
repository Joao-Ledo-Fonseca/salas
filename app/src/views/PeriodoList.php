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
  $titulo = 'Cadastro de Períodos';
  include __DIR__ . "/components/cabecalho2.php";
  ?>



<div class="container_top lista_comum" style="max-height:60vh; overflow-y:auto;">
    <div class="form_sel">
        <input type="button" name="novo" value="Novo" class="btn1" onclick='abre("periodo_form.php")' />
    </div>
</div>

<div class="container_conteudo lista_comum" style="max-height:60vh; overflow-y:auto;">
    <?php
    $colunas = [
        ['chave' => 'nome', 'label' => 'Nome', 'url' => app_url('?acao=periodo_form&id=%id%')],
        ['chave' => 'seq', 'label' => 'Sequência', 'formato' => 'int'],
        ['chave' => 'id', 'label' => 'ID', 'formato' => 'int'],
    ];
    include __DIR__ . '/components/ListaComum.php';
    ?>
</div>
