<?php

namespace App\Repository;

use Cocur\Slugify\SlugifyInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractFileSystemRepository
{
    protected FilesystemInterface $storage;
    protected SlugifyInterface $nameProvider;
    protected Serializer $dataProcessor;

    public function __construct(FilesystemInterface $defaultStorage, SlugifyInterface $slugify)
    {
        $this->storage = $defaultStorage;
        $this->nameProvider = $slugify;
        $this->dataProcessor = $this->getDataProcessor();
    }

    private function getDataProcessor(): Serializer
    {
        return new Serializer($this->getNormalizers(), $this->getEncoders());
    }

    private function getEncoders(): array
    {
        return [
            new XmlEncoder(),
            new JsonEncoder(),
            new YamlEncoder(null, null, ['yaml_inline' => 1, 'yaml_indent' => 0, 'yaml_flags' => 1]),
        ];
    }

    private function getNormalizers(): array
    {
        return [
          new ObjectNormalizer(),
        ];
    }

}
