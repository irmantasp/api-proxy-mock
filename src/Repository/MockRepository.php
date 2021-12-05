<?php

namespace App\Repository;

use App\Entity\Mock;
use Laminas\Diactoros\Uri;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use Psr\Http\Message\RequestInterface;

class MockRepository extends AbstractFileSystemRepository
{
    protected const REPOSITORY = 'mock';

    private function fileName($data, ?string $originId = null, ?string $method = null, ?string $content = null): string
    {
        if ($data instanceof Mock) {
            $originId = $originId ?? $data->getOriginId();
            $method = $method ?? $data->getMethod();
            $fileName = is_null($data->getId()) ? $this->name($data, $content) : $data->getId();
            $name = sprintf('%s/%s/%s', $originId, strtolower($method), $fileName);
        } else {
            $name = (string) $data;
            if ($originId) {
                $name = sprintf('%s/%s/%s', $originId, strtolower($method), $this->name($name, $content));
            }
        }

        return trim(sprintf('/%s.%s', trim($name), static::FORMAT));
    }

    final public function name($data, ?string $content = null): string
    {
        if ($data instanceof Mock) {
            $uri = $data->getUri();
        } else {
            $uri = (string) $data;
        }

        $id = $this->contentId($content);
        if (strpos($uri, $id) !== false) {
            return $uri;
        }

        $nameParts[] = $this->nameProvider->slugify($uri, ['lowercase' => true, 'separator' => '-']);
        $nameParts[] = $id;

        return implode('--', $nameParts);
    }

    final public function contentId(?string $content): string {
        if (is_null($content)) {
            $content = '';
        }

        return md5($content);
    }

    final public function save(Mock $mock, ?RequestInterface $request = null): bool
    {
        $headers = $mock->getHeaders();
        unset($headers['content-length']);
        $mock->setHeaders($headers);

        $name = is_null($request) ? $this->name($mock) : $this->name($mock, $request->getBody()->getContents());
        $mock->setId($name);
        $mock->setOrigin();

        if (($fileName = $this->fileName($mock)) && $this->storage->fileExists($this->filePath($fileName))) {
            try {
                $this->storage->write($this->filePath($fileName), $this->dataProcessor->serialize($mock, static::FORMAT));
                return true;
            } catch (FilesystemException $e) {
                return false;
            }
        }

        try {
            $this->storage->write($this->filePath($this->fileName($mock)), $this->dataProcessor->serialize($mock, static::FORMAT));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    final public function load(string $name, ?string $originId = null, ?string $method = null, ?string $content = null): ?Mock
    {
        try {
            if (strpos($name, '/') !== false) {
                $path = $name;
            } else {
                $path = $this->filePath($this->fileName($name, $originId, $method, $content));
            }

            $data = $this->storage->read($path);
            $mock = $this->dataProcessor->deserialize($data, Mock::class, static::FORMAT);
        } catch (FilesystemException $e) {
            return null;
        }

        $headers = $mock->getHeaders();
        $headers['content-length'][] = strlen($mock->getContent());
        $mock->setHeaders($headers);

        return $mock;
    }

    final public function loadMultiple(?array $names = [], ?string $originId = null): array
    {
        if (empty($names)) {
            $directory_listing = $this->storage->listContents(static::REPOSITORY, true);
            $files = $directory_listing->toArray();
            $files = array_filter($files, static function (StorageAttributes $file) {
                $file_info = pathinfo($file->path());
                return $file->isFile() && isset($file_info['extension']) && $file_info['extension'] === static::FORMAT;
            });
            $names = array_map(static function (StorageAttributes $file) {
                return $file->path();
            }, $files);

        }

        $mocks = array_map(function ($name) use ($originId) {
            return $this->load($name, $originId);
        }, $names);

        return array_filter($mocks, static function ($mock) use ($originId) {
            if (!$originId) {
                return true;
            }
            return $mock->getOriginId() === $originId;
        });
    }

    final public function delete(Mock $mock): bool
    {
        try {
            $this->storage->delete($this->filePath($this->fileName($mock)));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    final public function exists(string $name, ?string $originId = null): bool
    {
        return $this->storage->fileExists($this->filePath($this->fileName($name)));
    }

}