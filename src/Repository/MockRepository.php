<?php

namespace App\Repository;

use App\Entity\Mock;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;

class MockRepository extends AbstractFileSystemRepository
{
    protected const REPOSITORY = 'mock';

    private function fileName($data, ?string $originId = null): string
    {
        if ($data instanceof Mock) {
            $originId = $data->getOriginId();
            $name = sprintf('%s/%s', $originId, $this->name($data));
        } else {
            $name = (string) $data;
            if ($originId) {
                $name = sprintf('%s/%s', $originId, $this->name($name));
            }
        }

        return sprintf('%s.%s', $name, static::FORMAT);
    }

    final public function name($data): string
    {
        if ($data instanceof Mock) {
            $uri = $data->getUri();
        } else {
            $uri = (string) $data;
        }

        return $this->nameProvider->slugify($uri, ['lowercase' => true, 'separator' => '-']);
    }

    final public function save(Mock $mock): bool
    {
        $headers = $mock->getHeaders();
        unset($headers['content-length']);
        $mock->setHeaders($headers);

        if (($fileName = $this->fileName($mock)) && $this->storage->has($this->filePath($fileName))) {
            try {
                return $this->storage->update($this->filePath($fileName), $this->dataProcessor->serialize($mock, static::FORMAT));
            } catch (FileNotFoundException $e) {
                return false;
            }
        }

        try {
            return $this->storage->write($this->filePath($this->fileName($mock)), $this->dataProcessor->serialize($mock, static::FORMAT));
        } catch (FileExistsException $e) {
            return false;
        }
    }

    final public function load(string $name, ?string $originId = null): ?Mock
    {
        try {
            if (strpos($name, '/') !== false) {
                $path = $name;
            } else {
                $path = $this->filePath($this->fileName($name, $originId));
            }

            $data = $this->storage->read($path);
            $mock = $this->dataProcessor->deserialize($data, Mock::class, static::FORMAT);
        } catch (FileNotFoundException $e) {
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
            $names = array_map(static function ($file) use($originId) {
                return !$originId ? $file['path'] : $file['filename'];
            }, $files);
        }

        return array_map(function ($name) use ($originId) {
            return $this->load($name, $originId);
        }, $names);
    }

    final public function delete(Mock $mock): bool
    {
        try {
            return $this->storage->delete($this->filePath($this->fileName($mock)));
        } catch (FileNotFoundException $e) {
            return false;
        }
    }

    final public function exists(string $name, ?string $originId = null): bool
    {
        return $this->storage->has($this->filePath($this->fileName($name)));
    }

}