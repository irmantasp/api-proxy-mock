<?php

namespace App\Controller\Mock;

use App\Manager\OriginManagerInterface;
use App\Manager\RequestMockManager;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MockController extends AbstractController
{

    protected OriginManagerInterface $manager;
    protected RequestMockManager $mockManager;

    public function __construct(
        OriginManagerInterface $originManager,
        RequestMockManager $mockManager
    ) {
        $this->manager = $originManager;
        $this->mockManager = $mockManager;
    }

    final public function mock(string $origin_id, ?string $url): Response
    {
        if (!$origin = $this->manager->load($origin_id)) {
            throw new \RuntimeException('No origin found.', 500);
        }

        if (!$origin->getHost()) {
            throw new \RuntimeException('No origin host definition found', 500);
        }

        /** @var ServerRequestInterface $request */
        $request = $this->getRequest($url);
        $mockId = $this->mockManager->getId($request, $origin->getIgnore());

        if (!$mock = $this->mockManager->load($mockId, $origin->getName())) {
            throw new \RuntimeException('No mock record found', 404);
        }

        return new Response($mock->getContent(), (int) $mock->getStatus(), $mock->getHeaders());
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
