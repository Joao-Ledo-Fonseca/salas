<?php
declare(strict_types=1);

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ViewComponentsTest extends TestCase
{
    public function testCommonListRendersEscapedValuesLinksAndIntegerFormats(): void
    {
        $html = $this->render('ListaComum.php', [
            'lista' => [[
                'id' => 12,
                'nome' => '<Período>',
                'seq' => '03',
            ]],
            'colunas' => [
                ['chave' => 'nome', 'label' => 'Nome', 'url' => '/?acao=periodo_form&id=%id%'],
                ['chave' => 'seq', 'label' => 'Sequência', 'formato' => 'int'],
                ['chave' => 'id', 'label' => 'ID', 'formato' => 'int'],
            ],
            'classe_tabela' => 'lista_comum',
        ]);

        $this->assertStringContainsString('class="lista_comum"', $html);
        $this->assertStringContainsString('href="/?acao=periodo_form&amp;id=12"', $html);
        $this->assertStringContainsString('&lt;Período&gt;', $html);
        $this->assertStringNotContainsString('<Período>', $html);
        $this->assertMatchesRegularExpression('/<td>\s*3\s*<\/td>/', $html);
        $this->assertMatchesRegularExpression('/<td>\s*12\s*<\/td>/', $html);
        $this->assertSame(1, substr_count($html, '<th></th>'));
    }

    public function testCommonListKeepsLegacyEditUrlForFirstColumn(): void
    {
        $html = $this->render('ListaComum.php', [
            'lista' => [['id' => 5, 'nome' => 'Uso']],
            'colunas' => [
                ['chave' => 'nome', 'label' => 'Nome'],
            ],
            'url_editar' => '/?acao=uso_form&id=%id%',
        ]);

        $this->assertStringContainsString('href="/?acao=uso_form&amp;id=5"', $html);
        $this->assertStringContainsString('<th></th>', $html);
        $this->assertStringContainsString('<td></td>', $html);
    }

    public function testRoomImageSelectorRendersFormConfigurationAndPreview(): void
    {
        $html = $this->render('SeletorImagemSala.php', [
            'id' => 4,
            'imagem_id' => 8,
            'imagem' => 'room-image',
            'categoria_filtro' => '2&x',
            'activas_filtro' => 'activas',
        ]);

        $this->assertStringContainsString('data-sala-id="4"', $html);
        $this->assertStringContainsString('data-category-filter="2&amp;x"', $html);
        $this->assertStringContainsString('name="imagem_id" id="imagem_id" value="8" form="sala-form"', $html);
        $this->assertStringContainsString('name="imagem" id="newimagem" type="file"', $html);
        $this->assertStringContainsString('src="data:image/jpeg;base64,' . base64_encode('room-image') . '"', $html);
        $this->assertStringContainsString('id="overlayImagens"', $html);
    }

    public function testRoomFormLoadsImageSelectorScriptAndAssociatesUploadWithForm(): void
    {
        $html = $this->renderView('SalaForm.php', [
            'id' => 4,
            'errormsg' => '',
            'categoria_filtro' => '0',
            'activas_filtro' => 'activas',
            'return_to' => '',
            'return_id' => 0,
            'categorias' => [],
            'categoria_id' => 0,
            'lugares' => 0,
            'activa' => true,
            'nome' => '',
            'descricao' => '',
            'imagem_id' => '',
            'imagem' => '',
            'reservasSala' => [],
            'podeEliminarReservas' => false,
        ]);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $this->assertSame(
            'sala-form',
            $xpath->query('//*[@id="newimagem"]')->item(0)?->attributes?->getNamedItem('form')?->nodeValue
        );
        $this->assertSame(
            'sala-form',
            $xpath->query('//*[@id="imagem_id"]')->item(0)?->attributes?->getNamedItem('form')?->nodeValue
        );
        $this->assertSame(
            1,
            $xpath->query('//script[contains(@src, "/js/sala-imagens.js")]')->length
        );
    }

    private function render(string $component, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src'
            . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'components'
            . DIRECTORY_SEPARATOR . $component;

        return (string) ob_get_clean();
    }

    private function renderView(string $view, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src'
            . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view;

        return (string) ob_get_clean();
    }
}
