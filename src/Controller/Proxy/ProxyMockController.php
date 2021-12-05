<?php

namespace App\Controller\Proxy;

use App\Entity\Mock;
use App\Entity\Origin;
use App\Manager\MockManager;
use App\Manager\OriginManagerInterface;
use App\Service\ProxyClientInterface;
use Symfony\Component\HttpFoundation\Response;

class ProxyMockController extends AbstractProxyController
{

    private $mockManager;

    public function __construct(OriginManagerInterface $originManager, ProxyClientInterface $proxyService, MockManager $mockManager)
    {
        parent::__construct($originManager, $proxyService);

        $this->mockManager = $mockManager;
    }

    final public function proxy(string $origin_id, ?string $url): Response
    {
        $response = parent::proxy($origin_id, $url);

        $origin = $this->manager->load($origin_id);
        if ($origin && $origin->getRecord() === true) {
            $request = $this->getRequest($url);
            $requestContent = $request->getBody()->getContents();
            $mockId = $this->mockManager->nameFromUri($request->getRequestTarget(), $requestContent);

            $mock = $this->mockManager->load($mockId, $origin->getName(), $request->getMethod(), $requestContent);
            if (!$mock) {
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

                $this->mockManager->save($mock, $request);
            }
            else {
                return new Response($mock->getContent(), (int) $mock->getStatus(), $mock->getHeaders());
            }
        }

        return $response;
    }
}
