<?php

namespace App\Proxy\Adapter;

use Laminas\Diactoros\ServerRequestFactory;
use \Proxy\Adapter\Guzzle\GuzzleAdapter as BaseGuzzleAdapter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class GuzzleAdapter extends BaseGuzzleAdapter
{
    final public function send(RequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        if ($method === 'POST' && !$this->isFormPost()) {
            return parent::send($request);
        }

        return $this->client->request($method, $this->getUri($request), $this->getData($request));
    }

    private function isFormPost(): bool
    {
        $contentType = $this->getHeaderLine('Content-Type');

        return (strpos($contentType, 'form-data') !== false);
    }

    private function getData(RequestInterface $request): array
    {
        return [
            'headers' => $request->getHeaders(),
            'form_params' => $this->getFormPostParams(),
        ];
    }

    private function getHeaderLine(string $header): string
    {
        return ServerRequestFactory::fromGlobals()->getHeaderLine($header);
    }

    private function getFormPostParams(): array
    {
        $request = Request::createFromGlobals();

        return $request->request->all();
    }

    private function getUri(RequestInterface $request): string
    {
        return (string) $request->getUri();
    }
}