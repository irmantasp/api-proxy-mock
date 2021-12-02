<?php

namespace App\Repository;

use App\Entity\Origin;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

class OriginRepository extends AbstractFileSystemRepository
{
    protected const REPOSITORY = 'origin';
    protected const FORMAT = 'yaml';

    private function fileName($data): string
    {
        if ($data instanceof Origin) {
            $name = $data->getName();
        } else {
            $name = (string) $data;
        }

        return sprintf('%s.%s', $name, static::FORMAT);
    }

    private function name(Origin $origin): string
    {
        return $this->nameProvider->slugify($origin->getLabel(), ['regexp' => '/([^a-z0-9]|-)+/']);
    }

    final public function save(Origin $origin): bool
    {
        if (!$origin->isNew() && ($fileName = $this->fileName($origin)) && $this->storage->fileExists($this->filePath($fileName))) {
            try {
                $this->storage->write($this->filePath($fileName), $this->dataProcessor->serialize($origin, static::FORMAT));
                return true;
            } catch (FilesystemException $e) {
                return false;
            }
        }

        $origin->setName($this->name($origin));
        try {
            $this->storage->write($this->filePath($this->fileName($origin)), $this->dataProcessor->serialize($origin, static::FORMAT));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    final public function load(string $name): ?Origin
    {
        try {
            $data = $this->storage->read($this->filePath($this->fileName($name)));
            $origin = $this->dataProcessor->deserialize($data, Origin::class, static::FORMAT);
        } catch (FilesystemException $e) {
            return null;
        }

        return $origin;
    }

    final public function loadMultiple(?array $names = []): array
    {
        if (empty($names)) {
            $directory_listing = $this->storage->listContents(static::REPOSITORY);
            $files = $directory_listing->toArray();
            $files = array_filter($files, static function (StorageAttributes $file) {
                $file_info = pathinfo($file->path());
                return isset($file_info['extension']) && $file_info['extension'] === static::FORMAT;
            });
            $names = array_map(static function (StorageAttributes $file) {
                $file_info = pathinfo($file->path());
                return $file_info['filename'];
            }, $files);
        }

        return array_map(function ($name) {
            return $this->load($name);
        }, $names);
    }

    final public function delete(Origin $origin): bool
    {
        try {
            $this->storage->delete($this->filePath($this->fileName($origin)));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    final public function exists(string $name): bool
    {
        return $this->storage->fileExists($this->filePath($this->fileName($name)));
    }
}
