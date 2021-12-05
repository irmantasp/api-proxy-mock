<?php

namespace App\Service;

use App\Proxy\Filter\AddAcceptEncodingFilter;
use GuzzleHttp\Client;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
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
            'verify' => false,
            'allow_redirects' => true,
        ];

        return new Client($clientConfig);
    }

    private function initProxyFilters(Proxy $proxy): void
    {
        $proxy->filter(new RemoveEncodingFilter());
        $proxy->filter(new RemoveLocationFilter());
        $proxy->filter(new AddAcceptEncodingFilter());
    }
}
