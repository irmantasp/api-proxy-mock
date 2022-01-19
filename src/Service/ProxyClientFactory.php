<?php

namespace App\Service;

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

class ProxyClientFactory implements ProxyClientFactoryInterface
{
    final public function createProxy(): Proxy
    {
        $adapter = new GuzzleAdapter($this->getClient());
        $proxy = new Proxy($adapter);
        $this->initProxyFilters($proxy);

        return $proxy;
    }

    private function getClient(): Client
    {
        $clientConfig = [
            'verify' => (bool) $_ENV['APP_PROXY_ALLOW_VERIFY_SSL'],
            'allow_redirects' => (bool) $_ENV['APP_PROXY_ALLOW_REDIRECTS'],
        ];

        return new Client($clientConfig);
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
}
