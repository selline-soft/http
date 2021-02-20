<?php
namespace Selline\Http\Tests\Integration;
use Http\Psr7Test\UploadedFileIntegrationTest;
use Selline\Http\Factory;
use Selline\Http\Stream;

class UploadedFileTest extends UploadedFileIntegrationTest
{
    public function createSubject()
    {
        return (new Factory())->createUploadedFile(Stream::create('writing to tempfile'));
    }
}