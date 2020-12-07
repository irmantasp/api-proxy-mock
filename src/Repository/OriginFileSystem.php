<?php

namespace App\Repository;

class OriginFileSystem extends AbstractFileSystemRepository
{

    final public function load(string $name): array
    {
        $file = $this->storage->has($name . '.txt');
        if ($file === false) {
            $this->storage->write($name . '.txt', 'test');
        }

        return [];
    }

    final public function loadMultiple(?array $names = null): array
    {
        return [];
    }
}