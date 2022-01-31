<?php

namespace App\Service;

use App\Entity\Origin;
use App\Manager\OriginManagerInterface;
use App\Proxy\Adapter\GuzzleAdapter;
use App\Proxy\Filter\AddAcceptEncodingFilter;
use App\Proxy\Filter\RemoveDevelopmentHeadersFilter;
use App\Proxy\Filter\RemoveForwardHeadersFilter;
use App\Proxy\Filter\RemoveHeadersFilter;
use App\Proxy\Filter\RemoveOriginFilter;
use GuzzleHttp\Client;
use Proxy\Filter\RemoveEncodingFilter;
use Proxy\Filter\RemoveLocationFilter;
use Proxy\Proxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ProxyClientFactory implements ProxyClientFactoryInterface
{
    private RequestStack $requestStack;

    private OriginManagerInterface $originManager;

    public function __construct(RequestStack $requestStack, OriginManagerInterface $originManager)
    {
        $this->requestStack = $requestStack;
        $this->originManager = $originManager;
    }

    final public function createProxy(): Proxy
    {
        $adapter = new GuzzleAdapter($this->getClient());
        $proxy = new Proxy($adapter);
        $this->initProxyFilters($proxy);

        return $proxy;
    }

    private function getClient(): Client
    {
        return new Client($this->getClientConfiguration());
    }

    private function getClientConfiguration(): array
    {
        $clientConfig = [
            'verify'            => (bool) $_ENV['APP_PROXY_ALLOW_VERIFY_SSL'],
            'allow_redirects'   => (bool) $_ENV['APP_PROXY_ALLOW_REDIRECTS'],
        ];

        $origin = $this->getOrigin();
        if (!$origin instanceof Origin) {
            return $clientConfig;
        }

        if ($origin->isProxyConfig() === false) {
            return $clientConfig;
        }

        $clientConfig = [
            'verify'        => $origin->isProxyVerify(),
            'http_errors'   => $origin->isProxyErrors(),
            'debug'         => !$origin->isProxyDebug() ?: fopen('php://stderr', 'wb'),
            'timeout'       => $origin->getProxyTimeout(),
            'version'       => $origin->getProxyVersion(),
        ];

        if ($origin->isProxyAllowRedirect() === false) {
            return $clientConfig;
        }

        $allowRedirectPrams = $origin->getProxyRedirectParams();
        if (empty($allowRedirectPrams)) {
            return $clientConfig + [
                'allow_redirect' => true,
            ];
        }

        return $clientConfig + [
            'max'             => $allowRedirectPrams['max'] ?? 5,
            'strict'          => $allowRedirectPrams['strict'] ?? false,
            'referer'         => $allowRedirectPrams['referer'] ?? false,
            'protocols'       => $allowRedirectPrams['protocols'] ?? ['http', 'https'],
            'track_redirects' => $allowRedirectPrams['track_redirects'] ?? false,
        ];
    }

    private function initProxyFilters(Proxy $proxy): void
    {
        $proxy->filter(new RemoveEncodingFilter());
        $proxy->filter(new RemoveLocationFilter());
        $proxy->filter(new RemoveOriginFilter());
        $proxy->filter(new RemoveForwardHeadersFilter());
        $proxy->filter(new RemoveDevelopmentHeadersFilter());
        $proxy->filter(new RemoveHeadersFilter());
        $proxy->filter(new AddAcceptEncodingFilter());
    }

    private function getOrigin(): ?Origin
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return NULL;
        }

        $originId = $currentRequest->get('origin_id');
        if (!$originId) {
            return NULL;
        }

        $origin = $this->originManager->load($originId);
        if (!$origin instanceof Origin) {
            return NULL;
        }

        return $origin;
    }
}
