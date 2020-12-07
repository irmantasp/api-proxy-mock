<?php

namespace App\Service;

use Proxy\Proxy;

interface ProxyClientFactoryInterface
{
    public function createProxy(): Proxy;
}
