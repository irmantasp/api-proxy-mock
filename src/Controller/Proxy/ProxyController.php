<?php

namespace App\Controller\Proxy;

use App\Entity\Mock;
use App\Entity\Origin;
use App\Manager\MockManager;
use App\Manager\OriginManagerInterface;
use App\Manager\RequestMockManager;
use App\Service\ProxyClientInterface;
use App\Utility\FilePathUtility;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;

class ProxyController extends AbstractProxyController
{

    private $mockManager;

    public function __construct(OriginManagerInterface $originManager, ProxyClientInterface $proxyService, RequestMockManager $mockManager)
    {
        parent::__construct($originManager, $proxyService);

        $this->mockManager = $mockManager;
    }

    final public function proxy(string $origin_id, ?string $url): Response
    {
        $response = parent::proxy($origin_id, $url);

        $origin = $this->manager->load($origin_id);
        if ($origin && $origin->getRecord() === true) {
            /** @var ServerRequestInterface $request */
            $request = $this->getRequest($url);
            $mockId = $this->mockManager->getId($request);

            if (!$this->mockManager->exists($mockId, $origin->getName())) {
                $mock = new Mock();
                $mock
                    ->setId($mockId)
                    ->setFilePath(FilePathUtility::name($request))
                    ->setOriginId($origin_id)
                    ->setUri($request->getRequestTarget())
                    ->setMethod($request->getMethod())
                    ->setStatus($response->getStatusCode())
                    ->setHeaders($response->headers->all());

                $content = $response->getContent();
                $mock->setContent($content);

                $this->mockManager->save($mock);
            }
        }

        return $response;
    }
}
