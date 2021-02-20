<?php
namespace Selline\Http\Tests\Integration;
use Http\Psr7Test\ResponseIntegrationTest;
use Selline\Http\Response;

class ResponseTest extends ResponseIntegrationTest
{
    public function createSubject()
    {
        return new Response();
    }
}