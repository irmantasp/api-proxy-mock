<?php

namespace App\Manager;

use App\Entity\Mock;
use App\Repository\MockRepository;

class MockManager
{
    private $repository;
    private $originManager;

    public function __construct(MockRepository $mockRepository, OriginManagerInterface $originManager)
    {
        $this->repository = $mockRepository;
        $this->originManager = $originManager;
    }

    final public function save(Mock $mock): bool
    {
        return $this->repository->save($mock);
    }

    final public function load(string $mockId, ?string $originId = null, ?string $method = null, ?string $content = null): ?Mock
    {
        if ($mock = $this->repository->load($mockId, $originId, $method, $content)) {
            $origin = $this->originManager->load($mock->getOriginId());
            $mock->setOrigin($origin);
        }

        return $mock;
    }

    final public function loadMultiple(?array $mockIds = [], ?string $originId = null): ?array
    {
        $mocks = $this->repository->loadMultiple($mockIds, $originId);
        array_map(function ($mock) use ($originId) {
            $origin = $this->originManager->load($mock->getOriginId() ?? $originId);
            $mock->setOrigin($origin);
        }, $mocks);

        return $mocks;
    }

    final public function loadByUri(string $uri, ?string $originId = null, ?string $method = null): ?Mock
    {
        $mockId = $this->nameFromUri($uri);

        return $this->load($mockId, $originId, $method);
    }

    final public function delete(Mock $mock): bool
    {
        return $this->repository->delete($mock);
    }

    final public function exists(string $mockId, ?string $originId = null): bool
    {
        return $this->repository->exists($mockId, $originId);
    }

    final public function nameFromUri(string $uri): string
    {
        return $this->repository->name($uri);
    }

}