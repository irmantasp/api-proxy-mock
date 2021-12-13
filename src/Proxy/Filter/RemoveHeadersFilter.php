<?php

namespace App\Proxy\Filter;

use Proxy\Filter\FilterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RemoveHeadersFilter implements FilterInterface
{

    private const HEADERS = [
        'user-agent',
        'mod-rewrite',
        'accept-language',
        'content-type',
        'content-length',
        'accept',
    ];

    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        foreach (static::HEADERS as $header) {
            $request = $request->withoutHeader($header);
        }

        return $next($request, $response);
    }
}