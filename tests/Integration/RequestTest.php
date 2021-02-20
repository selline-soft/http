<?php
namespace Selline\Http\Tests\Integration;
use Http\Psr7Test\RequestIntegrationTest;
use Selline\Http\Request;

class RequestTest extends RequestIntegrationTest
{
    public function createSubject()
    {
        return new Request('GET', '/');
    }
}