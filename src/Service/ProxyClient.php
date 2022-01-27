<?php

namespace App\Service;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Proxy\Proxy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProxyClient implements ProxyClientInterface
{
    private Proxy $proxy;
    private LoggerInterface $logger;
    private $isLogging = false;
    private ?Serializer $dataProcessor = null;

    public function __construct(ProxyClientFactoryInterface $proxyFactory, LoggerInterface $logger)
    {
        $this->proxy = $proxyFactory->createProxy();
        $this->logger = $logger;
    }

    final public function forwardRequest(RequestInterface $request, string $host): Response
    {
        try {
            $response = $this->proxy->forward($request)->to($host);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
        } catch (RequestException $exception) {
            if ($this->isLogging()) {
                $this->logger->error($exception->getMessage());
            }
            $response = new GuzzleResponse(500);
        }

        if ($this->isLogging()) {
            $requestLog = $this->dataProcessor->serialize($request, 'json');
            $this->logger->info(sprintf('Request: %s', $requestLog));

            $responseLog = $this->dataProcessor->serialize($response, 'json');
            $this->logger->info(sprintf('Response %s', $responseLog));
        }

        return $this->convertResponse($response);
    }

    final public function enableLogging(bool $log = true): void
    {
        $this->isLogging = $log;
        $this->dataProcessor = $this->getDataProcessor();
    }

    private function isLogging(): bool
    {
        return $this->isLogging ?? false;
    }

    private function convertResponse(ResponseInterface $response): Response
    {
        $httpFoundation = new HttpFoundationFactory();

        return $httpFoundation->createResponse($response);
    }

    private function getDataProcessor(): Serializer
    {
        return new Serializer($this->getNormalizers(), $this->getEncoders());
    }

    private function getEncoders(): array
    {
        return [new JsonEncoder()];
    }

    private function getNormalizers(): array
    {
        return [
            new PropertyNormalizer(null, null, null, null, null, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'stream',
                    'resource',
                ]
            ]),
            new ObjectNormalizer(null, null, null, null, null, null, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'stream',
                    'resource',
                ]
            ]),
        ];
    }
}
