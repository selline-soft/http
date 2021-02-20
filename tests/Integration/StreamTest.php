<?php
namespace Selline\Http\Tests\Integration;
use Http\Psr7Test\StreamIntegrationTest;
use Selline\Http\Stream;

class StreamTest extends StreamIntegrationTest
{
    public function createStream($data)
    {
        return Stream::create($data);
    }
}