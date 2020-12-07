<?php

namespace App\Service;

class HostConfigService implements HostConfigServiceInterface
{

    final public function getHostFromOrigin(string $origin): ?string
    {
        $map = $this->getOrigins();

        return $map[$origin] ?? null;
    }

    final public function getOrigins(): array
    {
        return [
            'myjsonservertypecodecom' => 'https://my-json-server.typicode.com/',
            'myjsonservertypecodecomtypecodedemo' => 'https://my-json-server.typicode.com/typecode/demo/',
            'localhost8088mockservicesoapbinding' => 'http://localhost:8088/mockServiceSoapBinding'
        ];
    }
}
