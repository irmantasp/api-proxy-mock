<?php

namespace App\Utility;

use Cocur\Slugify\Slugify;
use Psr\Http\Message\ServerRequestInterface;

class FilePathUtility
{

    final public static function name(ServerRequestInterface $request): string
    {
        return vsprintf('%s/%s/%s-%s-%s.json', static::nameParts($request));
    }

    final public static function nameParts(ServerRequestInterface $request): array
    {
        return [
            static::getUri($request),
            static::getMethod($request),
            static::getHeaderHash($request),
            static::getContentHash($request),
            static::getFilesHash($request),
        ];
    }

    private static function getUri(ServerRequestInterface $request): string
    {
        $slugify = new Slugify();
        $uri = $request->getUri();
        $url = sprintf('%s?%s', $uri->getPath(), $uri->getQuery());

        return $slugify->slugify($url);
    }

    private static function getMethod(ServerRequestInterface $request): string
    {
        return strtolower($request->getMethod());
    }

    private static function getHeaderHash(ServerRequestInterface $request): string
    {
        $headers = $request->getHeaders() ?? [];
        unset($headers['date'], $headers['cookie']);

        return static::getHash($headers);
    }

    private static function getContentHash(ServerRequestInterface $request): string
    {
        $content = $request->getParsedBody() ?? [];
        return static::getHash($content);
    }

    private static function getFilesHash(ServerRequestInterface $request): string
    {
        $files = $request->getUploadedFiles() ?? [];
        return static::getHash($files);
    }

    public static function getHash(array $content = []): string
    {
        return md5(json_encode($content, JSON_THROW_ON_ERROR));
    }
}