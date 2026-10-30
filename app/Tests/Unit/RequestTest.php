<?php

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Http\Request;

class RequestTest extends TestCase
{
    public function testActionFromQuery(): void
    {
        $req = new Request(['acao' => 'sala_form']);
        $this->assertSame('sala_form', $req->action());
    }

    public function testActionNullWhenEmpty(): void
    {
        $req = new Request([]);
        $this->assertNull($req->action());
    }

    public function testQuery(): void
    {
        $req = new Request(['acao' => 'login', 'id' => '5']);
        $this->assertSame('login', $req->query('acao'));
        $this->assertSame('5', $req->query('id'));
        $this->assertNull($req->query('missing'));
    }

    public function testAllQuery(): void
    {
        $data = ['acao' => 'x', 'y' => 'z'];
        $req = new Request($data);
        $this->assertSame($data, $req->allQuery());
    }

    public function testPost(): void
    {
        $req = new Request([], ['nome' => 'Joao']);
        $this->assertSame('Joao', $req->post('nome'));
        $this->assertNull($req->post('missing'));
    }

    public function testAllPost(): void
    {
        $data = ['a' => 'b'];
        $req = new Request([], $data);
        $this->assertSame($data, $req->allPost());
    }

    public function testIsAjaxTrue(): void
    {
        $req = new Request([], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertTrue($req->isAjax());
    }

    public function testIsAjaxFalse(): void
    {
        $req = new Request([], [], [], []);
        $this->assertFalse($req->isAjax());
    }

    public function testFile(): void
    {
        $files = ['imagem' => ['name' => 'a.jpg']];
        $req = new Request([], [], $files);
        $this->assertSame(['name' => 'a.jpg'], $req->file('imagem'));
        $this->assertNull($req->file('missing'));
    }

    public function testServer(): void
    {
        $req = new Request([], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->assertSame('POST', $req->server('REQUEST_METHOD'));
    }

    public function testSession(): void
    {
        $req = new Request([], [], [], [], ['user_id' => 42]);
        $this->assertSame(42, $req->session('user_id'));
    }
}
