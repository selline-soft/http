<?php
namespace Selline\Http\Tests\Integration;
use Http\Psr7Test\ServerRequestIntegrationTest;
use Selline\Http\ServerRequest;

class ServerRequestTest extends ServerRequestIntegrationTest
{
    public function createSubject()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        return new ServerRequest('GET', '/', [], null, '1.1', $_SERVER);
    }
}