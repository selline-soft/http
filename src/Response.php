<?php
namespace Selline\Http;
use Psr\Http\Message\ResponseInterface;
use Selline\Http\Traits\MessageInternalTrait;

final class Response implements ResponseInterface
{
    use MessageInternalTrait;

    public function getStatusCode()
    {
        // TODO: Implement getStatusCode() method.
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        // TODO: Implement withStatus() method.
    }

    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
    }
}