<?php

namespace App\Repository;

use Psr\Http\Message\RequestInterface;

interface RepositoryInterface
{
    public function getName(string $origin, RequestInterface $request): string;

    public function load(string $name): array;

    public function loadMultiple(?array $names = null): array;
}