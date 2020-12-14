<?php

namespace App\Controller\Mock;

use App\Entity\Mock;
use App\Manager\MockManager;
use App\Manager\OriginManagerInterface;
use Cocur\Slugify\SlugifyInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MockController extends AbstractController
{

    protected OriginManagerInterface $manager;
    protected MockManager $mockManager;
    protected SlugifyInterface $slugify;
    private Serializer $dataProcessor;

    public function __construct(
        OriginManagerInterface $originManager,
        MockManager $mockManager,
        SlugifyInterface $slugify
    ) {
        $this->manager = $originManager;
        $this->mockManager = $mockManager;
        $this->slugify = $slugify;
        $this->dataProcessor = $this->getDataProcessor();
    }

    final public function mock(string $origin_id, ?string $url): Response
    {
        if (!$origin = $this->manager->load($origin_id)) {
            throw new \RuntimeException('No origin found.', 500);
        }

        if (!$host = $origin->getHost()) {
            throw new \RuntimeException('No origin host definition found', 500);
        }

        $request = $this->getRequest($url);

        $request_url = $this->slugify->slugify($request->getRequestTarget(), ['lowercase' => true, 'separator' => '-']);

        $storage = $this->mockManager->getStorage();

        if (!$storage->has(sprintf('%s/%s', $origin_id, $request_url))) {
            $storage->createDir(sprintf('%s/%s', $origin_id, $request_url));
        }

        if ($storage->has(sprintf('%s/%s/response.yaml', $origin_id, $request_url))) {
            $data = $storage->read(sprintf('%s/%s/response.yaml', $origin_id, $request_url));
            $data_content = $storage->read(sprintf('%s/%s/content.json', $origin_id, $request_url));
        }

        if (!($data && $data_content)) {
            throw new \RuntimeException('No mock data', 500);
        }

        $mock = $this->dataProcessor->deserialize($data, Mock::class, 'yaml');

        return new Response($data_content, $mock->getStatus(), $mock->getHeaders());
    }

    final public function getRequest(?string $url): RequestInterface
    {
        $request = ServerRequestFactory::fromGlobals();
        if ($url) {
            $request = $request->withUri($this->getUri($url, $request), true);
        }

        return $request;
    }

    private function getUri(string $url, RequestInterface $request): UriInterface
    {
        if (strpos($url, '/') !== 0) {
            $url = '/' . $url;
        }

        $uri = $request->getUri();
        $uri = $uri->withPath($url);

        return $uri;
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
