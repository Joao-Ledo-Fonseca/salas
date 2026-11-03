
<?php
use Salas\Controllers\UsoController;
?>

<?php
$titulo = 'Cadastro de Usos';
include __DIR__ . "/components/cabecalho2.php";
?>

<?php
$acoesLista = ['createUrl' => 'uso_form.php'];
include __DIR__ . '/components/AcoesLista.php';
?>
<div class="container_conteudo lista_comum" style="max-height:60vh; overflow-y:auto;">
    <?php
    $colunas = [
        ['chave' => 'nome', 'label' => 'Nome'],
        ['chave' => 'descricao', 'label' => 'Descrição'],
        ['chave' => 'id', 'label' => 'ID', 'largura' => '50'],
    ];
    $url_editar = app_url('?acao=uso_form&id=%id%');
    include __DIR__ . '/components/ListaComum.php';
    ?>
</div>
