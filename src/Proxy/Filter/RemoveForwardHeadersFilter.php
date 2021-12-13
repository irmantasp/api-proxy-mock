<?php

namespace App\Proxy\Filter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RemoveForwardHeadersFilter
{

    private const HEADERS = [
        'x-real-ip',
        'x-forwarded-server',
        'x-forwarded-proto',
        'x-forwarded-port',
        'x-forwarded-for',
    ];

    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        foreach (static::HEADERS as $header) {
            $request = $request->withoutHeader($header);
        }

        return $next($request, $response);
    }
}