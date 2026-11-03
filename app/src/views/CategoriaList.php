<?php
use Salas\Controllers\CategoriaController;
use Salas\Controllers\SalaController;
?>

 <?php
  $titulo = 'Cadastro de Categorias';
  include __DIR__ . "/components/cabecalho2.php";
  ?>

<?php
$acoesLista = ['createUrl' => 'categoria_form.php'];
include __DIR__ . '/components/AcoesLista.php';
?>

<div class="container_conteudo lista_comum" style="max-height:60vh; overflow-y:auto;">
    <!-- Usa componente ListaComum para tabela de categorias -->
    <?php
    $colunas = [
        ['chave' => 'nome', 'label' => 'Nome'],
        ['chave' => 'descricao', 'label' => 'Descrição'],
        ['chave' => 'id', 'label' => 'ID', 'largura' => '50'],
    ];
    $url_editar = app_url('?acao=categoria_form&id=%id%');
    include __DIR__ . '/components/ListaComum.php';
    ?>
</div>
