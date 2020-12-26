<?php

namespace App\Manager;

use League\Flysystem\FilesystemInterface;

class MockManager
{
    private FilesystemInterface $storage;

    public function __construct(FilesystemInterface $defaultStorage)
    {
        $this->storage = $defaultStorage;
    }

    final public function getStorage(): FilesystemInterface
    {
        return $this->storage;
    }

}