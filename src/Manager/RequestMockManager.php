<?php

namespace App\Manager;

use App\Entity\Mock;
use App\Entity\Origin;
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

    final public function getId(ServerRequestInterface $request, array $ignoreContext = [], array $optionsContext = []): string
    {
        return base64_encode(FilePathUtility::name($request, $ignoreContext, $optionsContext));
    }

    final public function getIdFromMock(Mock $mock): string {
        return base64_encode($mock->getFilePath());
    }

    final public function getPathFromRequest(ServerRequestInterface $request, array $ignoreContext = [], array $optionsContext = []): string
    {
        return FilePathUtility::name($request, $ignoreContext, $optionsContext);
    }

    final public function getPathFromId(string $mockId): string
    {
        return base64_decode($mockId);
    }

    final public function save(Mock $mock): bool
    {
        $mock->setOrigin();
        return $this->repository->save($mock);
    }

    final public function load(string $mockId, string $originId = null): ?Mock
    {
        $mockFilePath = $this->repository->getFilePath($originId, $this->getPathFromId($mockId));

        $mock = $this->repository->load($mockFilePath);
        if ($mock instanceof Mock) {
            $mock->setOrigin($this->originManager->load($originId));
        }

        return $mock;
    }

    final public function loadAll(): array
    {
        $mocks = $this->repository->loadAll();

        return array_map(function ($mock) {
            $originId = $mock->getOriginId();
            $mock->setOrigin($this->originManager->load($originId));

            return $mock;
        }, $mocks);
    }

    final public function loadByOrigin(Origin $origin): array
    {
        $mocks = $this->repository->loadByOriginPath($origin);

        return array_map(static function ($mock) use ($origin) {
            $mock->setOrigin($origin);

            return $mock;
        }, $mocks);
    }

    final public function delete(Mock $mock): bool
    {
        $mockFilePath = $this->repository->getFilePathFromMock($mock);

        return $this->repository->delete($mockFilePath);
    }

    final public function exists(string $mockId, string $originId = null): bool
    {
        $mockFilePath = $this->getPathFromId($mockId);
        $filePath = $this->repository->getFilePath($originId, $mockFilePath);

        return $this->repository->exists($filePath);
    }

    final public function fileExists(Mock $mock): bool {
        $mockFilePath = $this->repository->getFilePathFromMock($mock);

        return $this->repository->exists($mockFilePath);
    }

}