
<?php
use Salas\Controllers\UsoController;
?>

<?php
$titulo = 'Cadastro de Usos';
include __DIR__ . "/components/cabecalho2.php";
?>

<div class="container_top lista_comum" style="max-height:60vh; overflow-y:auto;">
    <form class="form_sel" name="form1" method="post" target="_self">
        <span style="float:left;">
            <input type="button" name="novo" value="Novo" class="btn1" onclick="abre('uso_form.php')" />
        </span>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Salas\Core\Support\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
    </form>
</div>
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
