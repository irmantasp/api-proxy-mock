<?php

namespace App\Controller\Proxy;

use App\Service\HostConfigServiceInterface;
use App\Service\ProxyClientInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AbstractProxyController extends AbstractController
{
    protected HostConfigServiceInterface $hostConfig;
    protected ProxyClientInterface $proxy;

    public function __construct(HostConfigServiceInterface $hostConfigService, ProxyClientInterface $proxyService)
    {
        $this->hostConfig = $hostConfigService;
        $this->proxy = $proxyService;
    }

    public function proxy(string $origin, ?string $url): Response
    {

        if (!$host = $this->hostConfig->getHostFromOrigin($origin)) {
            return new Response('No host origin found', 500);
        }

        $request = $this->getRequest($url);

        return $this->proxy->forwardRequest($request, $host);
    }

    final public function getRequest(?string $url): RequestInterface
    {
        $request = ServerRequestFactory::fromGlobals();
        if ($url) {
            $request = $request->withUri($this->getUri($url, $request), true);
        }

        return $request;
    }

    private function getUri(string $url, RequestInterface $request): UriInterface
    {
        if (strpos($url, '/') !== 0) {
            $url = '/' . $url;
        }

        $uri = $request->getUri();
        $uri = $uri->withPath($url);

        return $uri;
    }
}
