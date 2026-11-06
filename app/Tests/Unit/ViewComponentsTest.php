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

    public function testListActionsRenderCreateButtonAndCsrfField(): void
    {
        $html = $this->render('AcoesLista.php', [
            'acoesLista' => [
                'createUrl' => 'categoria_form.php',
                'containerClass' => 'container_top lista_comum',
            ],
        ]);

        $this->assertStringContainsString('class="container_top lista_comum"', $html);
        $this->assertStringContainsString('method="post" target="_self"', $html);
        $this->assertStringContainsString('name="novo" value="Novo"', $html);
        $this->assertStringContainsString('abre(&quot;categoria_form.php&quot;)', $html);
        $this->assertMatchesRegularExpression('/name="csrf_token" value="[a-f0-9]{64}"/', $html);
    }

    public function testCrudFormActionsSupportRedirectAndSubmitCancel(): void
    {
        $redirectHtml = $this->render('AcoesFormulario.php', [
            'acoesFormulario' => [
                'salvar' => true,
                'excluir' => true,
                'cancelar' => [
                    'mode' => 'redirect',
                    'url' => '/?acao=sala_list&categoria_id=2',
                ],
            ],
        ]);
        $submitHtml = $this->render('AcoesFormulario.php', [
            'acoesFormulario' => [
                'salvar' => true,
                'excluir' => true,
                'cancelar' => [
                    'mode' => 'submit',
                    'class' => 'btn1 inline-form',
                    'id' => 'cancelar',
                    'clearRequired' => true,
                ],
            ],
        ]);

        $this->assertStringContainsString('type="submit" name="salvar" value="Salvar"', $redirectHtml);
        $this->assertStringContainsString('type="submit" name="excluir" value="Excluir"', $redirectHtml);
        $this->assertStringContainsString('window.location.href=', $redirectHtml);
        $this->assertStringContainsString('categoria_id=2', $redirectHtml);
        $this->assertStringContainsString('type="submit" name="cancelar"', $submitHtml);
        $this->assertStringContainsString('class="btn1 inline-form" id="cancelar"', $submitHtml);
        $this->assertStringContainsString('onclick="cancelaInputsRequired()"', $submitHtml);
    }

    public function testReportDateNavigationUsesThemeAwareSvgButtons(): void
    {
        $html = $this->render('cabecalho.php', [
            'acaoPagina' => 'relatorios',
            'tipo' => 'd',
            'hoje' => new \DateTimeImmutable('2025-01-01'),
            'hoje_pt' => '01/01/2025',
        ]);

        $this->assertSame(2, substr_count($html, '<svg viewBox="0 0 24 24" aria-hidden="true">'));
        $this->assertSame(2, substr_count($html, 'aria-label='));
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
        $this->assertStringContainsString('data-upload-url=', $html);
        $this->assertStringContainsString('name="imagem_id" id="imagem_id" value="8" form="sala-form"', $html);
        $this->assertStringContainsString('name="imagem" id="newimagem" type="file"', $html);
        $this->assertStringContainsString('src="data:image/jpeg;base64,' . base64_encode('room-image') . '"', $html);
        $this->assertStringContainsString('id="overlayImagens"', $html);
    }

    public function testRoomFormLoadsImageSelectorScriptAndImmediateUpload(): void
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
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $this->assertNull(
            $xpath->query('//*[@id="newimagem"]')->item(0)?->attributes?->getNamedItem('form')
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

    public function testPeriodoFormDisplaysEscapedDeleteError(): void
    {
        $html = $this->renderView('PeriodoForm.php', [
            'title' => 'Cadastro de Períodos',
            'id' => 14,
            'nome' => 'Tarde 1',
            'seq' => 5,
            'errormsg' => '<Período> tem reservas associadas.',
        ]);

        $this->assertStringContainsString('&lt;Período&gt; tem reservas associadas.', $html);
        $this->assertStringNotContainsString('<Período>', $html);
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
