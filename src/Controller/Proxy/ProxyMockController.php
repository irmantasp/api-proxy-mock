<?php

namespace App\Controller\Proxy;

use App\Entity\Mock;
use App\Manager\MockManager;
use App\Manager\OriginManagerInterface;
use App\Service\ProxyClientInterface;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ProxyMockController extends AbstractProxyController
{

    private SlugifyInterface $slugify;
    private MockManager $mockManager;
    private Serializer $dataProcessor;

    public function __construct(
        OriginManagerInterface $originManager,
        ProxyClientInterface $proxyService,
        SlugifyInterface $slugify,
        MockManager $mockManager
    ) {
        parent::__construct($originManager, $proxyService);

        $this->slugify = $slugify;
        $this->mockManager = $mockManager;
        $this->dataProcessor = $this->getDataProcessor();
    }

    final public function proxyMock(string $origin_id, ?string $url): Response
    {
        if (!$origin = $this->manager->load($origin_id)) {
            throw new \RuntimeException('No origin found.', 500);
        }

        if (!$host = $origin->getHost()) {
            throw new \RuntimeException('No origin host definition found', 500);
        }

        $request = $this->getRequest($url);

        $request_url = $this->slugify->slugify($request->getRequestTarget(), ['lowercase' => true, 'separator' => '-']);

        $response = $this->proxy->forwardRequest($request, $host);

        $storage = $this->mockManager->getStorage();

        if (!$storage->has(sprintf('%s/%s/response.yaml', $origin_id, $request_url))) {
            $mock = new Mock();
            $mock->setStatus($response->getStatusCode());
            $mock->setHeaders($response->headers->all());

            $data_content = $response->getContent();

            $mock_data = $this->dataProcessor->serialize($mock, 'yaml');

            $storage->createDir(sprintf('%s/%s', $origin_id, $request_url));
            $storage->write(sprintf('%s/%s/response.yaml', $origin_id, $request_url), $mock_data);
            $storage->write(sprintf('%s/%s/content.json', $origin_id, $request_url), $data_content);
        }

        return $response;
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
