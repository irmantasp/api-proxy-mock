<?php

namespace App\Controller\Proxy;

use App\Manager\OriginManagerInterface;
use App\Service\ProxyClientInterface;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\HttpFoundation\Response;


class ProxyController extends AbstractProxyController
{

    protected SlugifyInterface $slugify;

    public function __construct(
        OriginManagerInterface $originManager,
        ProxyClientInterface $proxyService,
        SlugifyInterface $slugify
    ) {
        parent::__construct($originManager, $proxyService);

        $this->slugify = $slugify;
    }

    final public function proxy(string $origin_id, ?string $url): Response
    {
        if (!$origin = $this->manager->load($origin_id)) {
            throw new \RuntimeException('No origin found.', 500);
        }

        if (!$host = $origin->getHost()) {
            throw new \RuntimeException('No origin host definition found', 500);
        }

        $request = $this->getRequest($url);

        $slug = $this->slugify->slugify($url, ['lowercase' => true, 'separator' => '-']);

        return $this->proxy->forwardRequest($request, $host);
    }

}
