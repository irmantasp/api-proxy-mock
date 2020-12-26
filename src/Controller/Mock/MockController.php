<?php

namespace App\Controller\Mock;

use App\Entity\Mock;
use App\Manager\MockManager;
use App\Manager\OriginManagerInterface;
use Cocur\Slugify\SlugifyInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MockController extends AbstractController
{

    protected OriginManagerInterface $manager;
    protected MockManager $mockManager;

    public function __construct(
        OriginManagerInterface $originManager,
        MockManager $mockManager
    ) {
        $this->manager = $originManager;
        $this->mockManager = $mockManager;
    }

    final public function mock(string $origin_id, ?string $url): Response
    {
        if (!$origin = $this->manager->load($origin_id)) {
            throw new \RuntimeException('No origin found.', 500);
        }

        if (!$host = $origin->getHost()) {
            throw new \RuntimeException('No origin host definition found', 500);
        }

        $request = $this->getRequest($url);

        $mockId = $this->mockManager->nameFromUri($request->getRequestTarget());

        if (!$mock = $this->mockManager->load($mockId, $origin->getName())) {
            throw new \RuntimeException('No mock record found', 404);
        }

        return new Response($mock->getContent(), $mock->getStatus(), $mock->getHeaders());
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
