<?php

namespace App\Service;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Response;

interface ProxyClientInterface
{
    public function forwardRequest(RequestInterface $request, string $host): Response;
}
