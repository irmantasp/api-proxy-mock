<?php

namespace App\Service;

use App\Entity\Origin;

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

    final public function getOrigin(string $id): ?Origin {
        if (!$host = $this->getHostFromOrigin($id)) {
            return null;
        }

        $origin = new Origin();

        $origin->setOrigin($id);
        $origin->setHost($host);

        return $origin;
    }
}
