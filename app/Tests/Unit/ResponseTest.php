<?php

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Http\Response;

class ResponseTest extends TestCase
{
    public function testRedirectStatusCode(): void
    {
        $res = Response::redirect('?acao=login');
        $this->assertSame(302, $res->getStatusCode());
    }

    public function testRedirectLocationHeader(): void
    {
        $res = Response::redirect('?acao=login');
        $this->assertSame('?acao=login', $res->getHeaders()['Location']);
    }

    public function testHtmlContentType(): void
    {
        $res = Response::html('<h1>hi</h1>');
        $this->assertSame(200, $res->getStatusCode());
        $this->assertSame('text/html; charset=UTF-8', $res->getHeaders()['Content-Type']);
        $this->assertSame('<h1>hi</h1>', $res->getContent());
    }

    public function testTextContentType(): void
    {
        $res = Response::text('hello');
        $this->assertSame('text/plain; charset=UTF-8', $res->getHeaders()['Content-Type']);
    }

    public function testWithStatus(): void
    {
        $res = Response::html('x')->withStatus(404);
        $this->assertSame(404, $res->getStatusCode());
    }

    public function testWithHeader(): void
    {
        $res = Response::html('x')->withHeader('X-Custom', 'val');
        $this->assertSame('val', $res->getHeaders()['X-Custom']);
    }

    public function testConstructor(): void
    {
        $res = new Response('body', 201, ['X-Foo' => 'bar']);
        $this->assertSame('body', $res->getContent());
        $this->assertSame(201, $res->getStatusCode());
        $this->assertSame('bar', $res->getHeaders()['X-Foo']);
    }
}
