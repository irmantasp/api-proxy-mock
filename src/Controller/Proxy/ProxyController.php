<?php

namespace App\Controller\Proxy;

use App\Repository\RepositoryInterface;
use App\Service\HostConfigServiceInterface;
use App\Service\ProxyClientInterface;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ProxyController extends AbstractProxyController
{
    protected RepositoryInterface $repository;

    protected SlugifyInterface $slugify;

    public function __construct(
        HostConfigServiceInterface $hostConfigService,
        ProxyClientInterface $proxyService,
        RepositoryInterface $repository,
        SlugifyInterface $slugify
    ) {
        parent::__construct($hostConfigService, $proxyService);

        $this->repository = $repository;
        $this->slugify = $slugify;
    }

    final public function proxy(string $origin, ?string $url): Response
    {
        if (!$host = $this->hostConfig->getHostFromOrigin($origin)) {
            return new Response('No host origin found', 500);
        }

        $request = $this->getRequest($url);

        $name = $this->repository->getName($origin, $request);
        $file = $this->repository->load($name);
        $slug = $this->slugify->slugify($url, ['lowercase' => true, 'separator' => '-']);

        return $this->proxy->forwardRequest($request, $host);
    }

}
