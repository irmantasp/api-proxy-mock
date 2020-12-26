<?php

namespace App\Manager;

use App\Entity\Origin;
use App\Repository\OriginRepository;

class OriginManager implements OriginManagerInterface
{
    private $repository;

    public function __construct(OriginRepository $originRepository)
    {
        $this->repository = $originRepository;
    }

    final public function save(Origin $origin): bool
    {
        return $this->repository->save($origin);
    }

    final public function load(string $originId): ?Origin
    {
        return $this->repository->load($originId);
    }

    final public function loadMultiple(?array $originIds = []): ?array
    {
        return $this->repository->loadMultiple($originIds);
    }

    final public function delete(Origin $origin): bool
    {
        return $this->repository->delete($origin);
    }

    final public function exists(string $originId): bool
    {
        return $this->repository->exists($originId);
    }

}