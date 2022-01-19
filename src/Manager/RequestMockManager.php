<?php

namespace App\Manager;

use App\Entity\Mock;
use App\Repository\RequestMockRepository;
use App\Utility\FilePathUtility;
use Psr\Http\Message\ServerRequestInterface;

class RequestMockManager
{
    public $repository;
    public $originManager;

    public function __construct(RequestMockRepository $mockRepository, OriginManagerInterface $originManager)
    {
        $this->repository = $mockRepository;
        $this->originManager = $originManager;
    }

    final public function getId(ServerRequestInterface $request): string
    {
        return base64_encode(FilePathUtility::name($request));
    }

    final public function getPathFromRequest(ServerRequestInterface $request): string
    {
        return FilePathUtility::name($request);
    }

    final public function getPathFromId(string $mockId): string
    {
        return base64_decode($mockId);
    }

    final public function save(Mock $mock): bool
    {
        return $this->repository->save($mock);
    }

    final public function load(string $mockId, string $originId = null): ?Mock
    {
        $mockFilePath = $this->repository->getFilePath($originId, $this->getPathFromId($mockId));

        return $this->repository->load($mockFilePath);
    }

    final public function exists(string $mockId, string $originId = null): bool
    {
        $mockFilePath = $this->getPathFromId($mockId);

        return $this->repository->exists($mockFilePath);
    }

}