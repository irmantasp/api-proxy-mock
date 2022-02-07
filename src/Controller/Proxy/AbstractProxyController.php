<?php

namespace App\Controller\Proxy;

use App\Manager\OriginManagerInterface;
use App\Service\ProxyClientInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AbstractProxyController extends AbstractController
{
    protected OriginManagerInterface $manager;
    protected ProxyClientInterface $proxy;

    public function __construct(OriginManagerInterface $originManager, ProxyClientInterface $proxyService)
    {
        $this->manager = $originManager;
        $this->proxy = $proxyService;
    }

    public function proxy(string $origin_id, ?string $url): Response
    {

        if (!$origin = $this->manager->load($origin_id)) {
            throw new \RuntimeException('No origin found.', 500);
        }

        if (!$host = $origin->getHost()) {
            throw new \RuntimeException('No origin host definition found', 500);
        }

        if ($origin->getLog() === true) {
            $this->proxy->enableLogging();
        }

        $request = $this->getRequest($url);

        return $this->proxy->forwardRequest($request, $host);
    }

    final public function getRequest(?string $url): RequestInterface
    {
        $request = ServerRequestFactory::fromGlobals();
        return $request->withUri($this->getUri($url, $request), true);
    }

    private function getUri(?string $url, RequestInterface $request): UriInterface
    {
        if (is_null($url)) {
            $url = '';
        }

        if (strpos($url, '/') !== 0) {
            $url = '/' . $url;
        }

        return $request->getUri()->withPath($url);
    }
}
