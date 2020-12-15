<?php

namespace App\Repository;

use App\Entity\Origin;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;

class OriginRepository extends AbstractFileSystemRepository
{
    private function fileName($data): string
    {
        if ($data instanceof Origin) {
            $name = $data->getName();
        } else {
            $name = (string) $data;
        }

        return sprintf('%s.yaml', $name);
    }

    private function name(Origin $origin): string
    {
        return $this->nameProvider->slugify($origin->getLabel(), ['regexp' => '/([^a-z0-9]|-)+/']);
    }

    final public function save(Origin $origin): bool
    {
        if (!$origin->isNew() && ($fileName = $this->fileName($origin)) && $this->storage->has($fileName)) {
            try {
                return $this->storage->update($fileName, $this->dataProcessor->serialize($origin, 'yaml'));
            } catch (FileNotFoundException $e) {
                return false;
            }
        }

        $origin->setName($this->name($origin));
        try {
            return $this->storage->write($this->fileName($origin), $this->dataProcessor->serialize($origin, 'yaml'));
        } catch (FileExistsException $e) {
            return false;
        }
    }

    final public function load(string $name): ?Origin
    {
        try {
            $data = $this->storage->read($this->fileName($name));
            $origin = $this->dataProcessor->deserialize($data, Origin::class, 'yaml');
        } catch (FileNotFoundException $e) {
            return null;
        }

        return $origin;
    }

    final public function loadMultiple(?array $names = []): array
    {
        if (empty($names)) {
            $files = $this->storage->listContents();
            $files = array_filter($files, static function ($file) {
                return isset($file['extension']) && $file['extension'] === 'yaml';
            });
            $names = array_map(static function ($file) {
                return $file['filename'];
            }, $files);
        }

        return array_map(function ($name) {
            return $this->load($name);
        }, $names);
    }

    final public function delete(Origin $origin): bool
    {
        try {
            return $this->storage->delete($this->fileName($origin));
        } catch (FileNotFoundException $e) {
            return false;
        }
    }

    final public function exists(string $name): bool
    {
        return $this->storage->has($this->fileName($name));
    }
}
