<?php

namespace App\Service;

interface HostConfigServiceInterface
{
    public function getHostFromOrigin(string $origin): ?string;

    public function getOrigins(): array;
}