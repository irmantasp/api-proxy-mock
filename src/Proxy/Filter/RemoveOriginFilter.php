<?php

namespace App\Proxy\Filter;

use Proxy\Filter\FilterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RemoveOriginFilter implements FilterInterface
{

    private const HOST_HEADER = 'host';

    private const ORIGIN_HEADER = 'origin';

    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {

        $request = $this->withoutHeaderIfExists(static::HOST_HEADER, $request);
        $request = $this->withoutHeaderIfExists(static::ORIGIN_HEADER, $request);

        return $next($request, $response);
    }

    private function withoutHeaderIfExists(string $header, RequestInterface $request): RequestInterface {
        if ($request->hasHeader($header)) {
            $request = $request->withoutHeader($header);
        }

        return $request;
    }
}