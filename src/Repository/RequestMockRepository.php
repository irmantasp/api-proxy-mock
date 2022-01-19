<?php

namespace App\Repository;

use App\Entity\Mock;
use App\Entity\Origin;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

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

    final public function loadAll(): array
    {
        $directory_listing = $this->storage->listContents(static::REPOSITORY, true);
        $files = $directory_listing->toArray();
        $files = array_filter($files, static function (StorageAttributes $file) {
            $file_info = pathinfo($file->path());
            return $file->isFile() && isset($file_info['extension']) && $file_info['extension'] === static::FORMAT;
        });
        $filePaths = array_map(static function (StorageAttributes $file) {
            return $file->path();
        }, $files);

        return array_map(function ($filePath) {
            return $this->load($filePath);
        }, $filePaths);
    }

    final public function loadByOriginPath(Origin $origin): array
    {
        $directory_listing = $this->storage->listContents(sprintf('%s/%s', static::REPOSITORY, $origin->getName()), true);
        $files = $directory_listing->toArray();
        $files = array_filter($files, static function (StorageAttributes $file) {
            $file_info = pathinfo($file->path());
            return $file->isFile() && isset($file_info['extension']) && $file_info['extension'] === static::FORMAT;
        });
        $filePaths = array_map(static function (StorageAttributes $file) {
            return $file->path();
        }, $files);

        return array_map(function ($filePath) {
            return $this->load($filePath);
        }, $filePaths);
    }

    final public function delete(string $filePath): bool
    {
        try {
            $this->storage->delete($filePath);
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
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