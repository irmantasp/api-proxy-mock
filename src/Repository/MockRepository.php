<?php

namespace App\Repository;

use App\Entity\Mock;
use League\Flysystem\FilesystemException;

class MockRepository extends AbstractFileSystemRepository
{
    protected const REPOSITORY = 'mock';

    private function fileName($data, ?string $originId = null, ?string $method = null, ?string $content = null): string
    {
        if ($data instanceof Mock) {
            $originId = $originId ?? $data->getOriginId();
            $method = $data->getMethod();
            $name = sprintf('%s/%s/%s', $originId, strtolower($method), $this->name($data, $content));
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

        if (!empty($content)) {
            $separator = strpos('?', $uri) ? '&' : '?';
            $uri .= sprintf('%srequestContent=%s', $separator, $this->contentId($content));
        }

        return $this->nameProvider->slugify($uri, ['lowercase' => true, 'separator' => '-']);
    }

    final public function contentId(?string $content): string {
        if (empty($content)) {
            return '';
        }

        return md5($content);
    }

    final public function save(Mock $mock): bool
    {
        $headers = $mock->getHeaders();
        unset($headers['content-length']);
        $mock->setHeaders($headers);

        $mock->setId($this->name($mock));
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
            $files = $this->storage->listContents(static::REPOSITORY, true);
            $files = array_filter($files, static function ($file) {
                return isset($file['extension']) && $file['extension'] === static::FORMAT;
            });
            $names = array_map(static function ($file) {
                return $file['path'];
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