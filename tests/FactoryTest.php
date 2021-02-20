<?php
namespace Selline\Http\Tests;
use PHPUnit\Framework\TestCase;
use Selline\Http\Factory;

class FactoryTest extends TestCase
{
    public function testCreateResponse()
    {
        $factory = new Factory();
        $r = $factory->createResponse(200);
        $this->assertEquals('OK', $r->getReasonPhrase());

        $r = $factory->createResponse(200, '');
        $this->assertEquals('', $r->getReasonPhrase());

        $r = $factory->createResponse(200, 'Foo');
        $this->assertEquals('Foo', $r->getReasonPhrase());

        /*
         * Test for non-standard response codes
         */
        $r = $factory->createResponse(567);
        $this->assertEquals('', $r->getReasonPhrase());

        $r = $factory->createResponse(567, '');
        $this->assertEquals(567, $r->getStatusCode());
        $this->assertEquals('', $r->getReasonPhrase());

        $r = $factory->createResponse(567, 'Foo');
        $this->assertEquals(567, $r->getStatusCode());
        $this->assertEquals('Foo', $r->getReasonPhrase());
    }
}