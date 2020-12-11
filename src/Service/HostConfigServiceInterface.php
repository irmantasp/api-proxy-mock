<?php

namespace App\Service;

use App\Entity\Origin;

interface HostConfigServiceInterface
{
    public function getHostFromOrigin(string $origin): ?string;

    public function getOrigins(): array;

    public function getOrigin(string $id): ?Origin;
}