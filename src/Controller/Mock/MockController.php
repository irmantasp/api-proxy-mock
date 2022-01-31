<?php

namespace App\Controller\Mock;

use App\Manager\OriginManagerInterface;
use App\Manager\RequestMockManager;
use App\Utility\FilePathUtility;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class MockController extends AbstractController
{

    protected OriginManagerInterface $manager;
    protected RequestMockManager $mockManager;
    protected LoggerInterface $logger;

    public function __construct(
        OriginManagerInterface $originManager,
        RequestMockManager $mockManager,
        LoggerInterface $logger
    ) {
        $this->manager = $originManager;
        $this->mockManager = $mockManager;
        $this->logger = $logger;
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
            $errors = [
                'requestError' => sprintf('No mock record found for request URL: %s', $url),
                'fileNamePartsError' => sprintf('No mock record found, where filepath name parts are: %s', json_encode(FilePathUtility::originalNamePartsKeyed($request, $origin->getIgnore()), JSON_THROW_ON_ERROR)),
                'filePathError' => sprintf('No mock record found on path: %s', $this->mockManager->getPathFromId($mockId)),
            ];

            $causeNumber = Uuid::v4();
            foreach ($errors as $error) {
                $this->logger->critical($error, ['cause' => $causeNumber]);
            }

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
