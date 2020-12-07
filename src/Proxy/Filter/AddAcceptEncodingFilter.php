<?php

namespace App\Proxy\Filter;

use Proxy\Filter\FilterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddAcceptEncodingFilter implements FilterInterface
{
    protected const HEADER = 'accept-encoding';

    protected const ENCODING = ['gzip', 'deflate'];

    /**
     * @inheritDoc
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $request = $request->withHeader(self::HEADER, self::ENCODING);
        $response = $next($request, $response);

        return $response;
    }
}
