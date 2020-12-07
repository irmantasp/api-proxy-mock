<?php

namespace App\Service;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Proxy\Proxy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;

class ProxyClient implements ProxyClientInterface
{
    private Proxy $proxy;

    public function __construct(ProxyClientFactoryInterface $proxyFactory)
    {
        $this->proxy = $proxyFactory->createProxy();
    }

    final public function forwardRequest(RequestInterface $request, string $host): Response
    {
        try {
            $response = $this->proxy->forward($request)->to($host);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
        } catch (RequestException $exception) {
            $response = new GuzzleResponse(500);
        }

        return $this->convertResponse($response);
    }

    private function convertResponse(ResponseInterface $response): Response
    {
        $httpFoundation = new HttpFoundationFactory();

        return $httpFoundation->createResponse($response);
    }
}
