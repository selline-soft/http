<?php
namespace Selline\Http\Tests\Integration;
use Http\Psr7Test\UriIntegrationTest;
use Selline\Http\Uri;

class UriTest extends UriIntegrationTest
{
    public function createUri($uri)
    {
        return new Uri($uri);
    }
}