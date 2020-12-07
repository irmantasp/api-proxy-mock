<?php

namespace App\Repository;

use Cocur\Slugify\SlugifyInterface;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\RequestInterface;

abstract class AbstractFileSystemRepository implements RepositoryInterface
{
    protected FilesystemInterface $storage;
    protected SlugifyInterface $nameProvider;

    public function __construct(FilesystemInterface $defaultStorage, SlugifyInterface $slugify)
    {
        $this->storage = $defaultStorage;
        $this->nameProvider = $slugify;
    }

    final public function getName(string $origin, RequestInterface $request): string
    {
        $components = [
            'origin' => $origin,
            'method' => $request->getMethod(),
            'url' => $request->getUri(),
            'body' => $request->getBody(),
        ];

        $component_string = json_encode($components);

        return md5($component_string);
    }

    abstract public function load(string $name): array;

    abstract public function loadMultiple(?array $names = null): array;
}
