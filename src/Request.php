<?php
namespace Selline\Http;
use Psr\Http\Message\RequestInterface;
use Selline\Http\Traits\MessageInternalTrait;
use Selline\Http\Traits\RequestInternalTrait;

final class Request implements RequestInterface
{
    use MessageInternalTrait;
    use RequestInternalTrait;
}