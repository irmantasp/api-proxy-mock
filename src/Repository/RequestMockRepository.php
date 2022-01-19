<?php

namespace App\Repository;

use App\Entity\Mock;
use League\Flysystem\FilesystemException;

class RequestMockRepository extends AbstractFileSystemRepository
{
    protected const REPOSITORY = 'mock';

    final public function exists(string $filePath): bool
    {
        return $this->storage->fileExists($filePath);
    }

    final public function save(Mock $mock): bool
    {
        $mockFilePath = $this->getFilePathFromMock($mock);
        try {
            $this->storage->write($mockFilePath, $this->dataProcessor->serialize($mock, static::FORMAT));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    final public function load(string $filePath): ?Mock
    {
        try {
            $data = $this->storage->read($filePath);
            $mock = $this->dataProcessor->deserialize($data, Mock::class, static::FORMAT);
        }
        catch (FilesystemException $e) {
            return null;
        }

        $headers = $mock->getHeaders();
        $headers['content-length'][] = strlen($mock->getContent());
        $mock->setHeaders($headers);

        return $mock;
    }

    final public function getFilePathFromMock(Mock $mock): string
    {
        return $this->filePath(sprintf('%s/%s', $mock->getOriginId(), $mock->getFilePath()));
    }

    final public function getFilePath(string $originId, string $mockFilePath): string
    {
        return $this->filePath(sprintf('%s/%s', $originId, $mockFilePath));
    }

}