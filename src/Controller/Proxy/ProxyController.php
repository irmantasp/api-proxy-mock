<?php

namespace App\Controller\Proxy;

use App\Entity\Mock;
use App\Entity\Origin;
use App\Manager\OriginManagerInterface;
use App\Manager\RequestMockManager;
use App\Service\ProxyClientInterface;
use App\Utility\FilePathUtility;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ProxyController extends AbstractProxyController
{

    private RequestMockManager $mockManager;

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
            $mockId = $this->mockManager->getId($request,  $origin->getIgnore(), $origin->getTransformOptions());

            if ($this->recordExits($mockId, $origin_id) === false) {
                $mock = $this->createRecord($request, $response, $mockId, $origin);
            }
            else if ($origin->isOverwriteRecord() === true) {
                $mock = $this->updateRecord($request, $response, $mockId, $origin);
            }

            if (isset($mock) && $origin->isReadFromRecord() === true) {
                return new Response($mock->getContent(), (int) $mock->getStatus(), $mock->getHeaders());
            }
        }

        return $response;
    }

    private function recordExits(string $mockId, string $origin_id): bool
    {
        return $this->mockManager->exists($mockId, $origin_id);
    }

    private function createRecord(ServerRequestInterface $request, Response $response, string $mockId, Origin $origin): ?Mock
    {
        $mock = new Mock();
        $mock
            ->setId($mockId)
            ->setUuid(Uuid::v4())
            ->setDate(date('Y-m-d H:i:s'))
            ->setFilePath(FilePathUtility::name($request, $origin->getIgnore(), $origin->getTransformOptions()))
            ->setOriginId($origin->getName())
            ->setUri($request->getRequestTarget())
            ->setMethod($request->getMethod())
            ->setStatus($response->getStatusCode())
            ->setHeaders($response->headers->all());

        if ($origin->getSaveOriginalRequest() === true) {
            $mock->setRequest($request);
        }

        $content = $response->getContent();
        $mock->setContent($content);

        $status = $this->mockManager->save($mock);

        return $status ? $mock : null;
    }

    private function updateRecord(ServerRequestInterface $request, Response $response, string $mockId, Origin $origin): ?Mock
    {
        $mock = $this->mockManager->load($mockId, $origin->getName());
        $mock
            ->setDate(date('Y-m-d H:i:s'))
            ->setStatus($response->getStatusCode())
            ->setHeaders($response->headers->all());

        if ($origin->getSaveOriginalRequest() === true) {
            $mock->setRequest($request);
        }

        $content = $response->getContent();
        $mock->setContent($content);

        $status = $this->mockManager->save($mock);

        return $status ? $mock : null;
    }
}
