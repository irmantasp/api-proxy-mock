<?php

namespace App\Controller\Proxy;

use App\Entity\Mock;
use App\Manager\MockManager;
use App\Manager\OriginManagerInterface;
use App\Service\ProxyClientInterface;
use Symfony\Component\HttpFoundation\Response;

class ProxyMockController extends AbstractProxyController
{
    private MockManager $mockManager;

    public function __construct(
        OriginManagerInterface $originManager,
        ProxyClientInterface $proxyService,
        MockManager $mockManager
    ) {
        parent::__construct($originManager, $proxyService);
        $this->mockManager = $mockManager;
    }

    final public function proxyMock(string $origin_id, ?string $url): Response
    {
        if (!$origin = $this->manager->load($origin_id)) {
            throw new \RuntimeException('No origin found.', 500);
        }

        if (!$host = $origin->getHost()) {
            throw new \RuntimeException('No origin host definition found', 500);
        }

        $request = $this->getRequest($url);

        $mockId = $this->mockManager->nameFromUri($request->getRequestTarget());

        $response = $this->proxy->forwardRequest($request, $host);

        if (!$this->mockManager->load($mockId, $origin->getName())) {
            $mock = new Mock();
            $mock
                ->setId($mockId)
                ->setOriginId($origin_id)
                ->setUri($request->getRequestTarget())
                ->setMethod($request->getMethod())
                ->setStatus($response->getStatusCode())
                ->setHeaders($response->headers->all());

            $content = $response->getContent();
            $mock->setContent($content);

            $this->mockManager->save($mock);
        }

        return $response;
    }

}
