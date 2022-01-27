<?php

namespace App\Utility;

use Cocur\Slugify\Slugify;
use Psr\Http\Message\ServerRequestInterface;
use Rexlabs\UtilityBelt\ArrayUtility;

class FilePathUtility
{

    final public static function name(ServerRequestInterface $request, array $ignore = []): string
    {
        return vsprintf('%s/%s/%s-%s-%s.json', static::nameParts($request, $ignore));
    }

    final public static function nameParts(ServerRequestInterface $request, array $ignore = []): array
    {
        $ignoreHeaders = $ignore['headers'] ?? [];
        $ignoreContent = $ignore['content'] ?? [];
        $ignoreFiles = (bool) $ignore['files'];

        return [
            static::getUri($request),
            static::getMethod($request),
            static::getHeaderHash($request, $ignoreHeaders),
            static::getContentHash($request, $ignoreContent),
            static::getFilesHash($request, $ignoreFiles),
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

    private static function getHeaderHash(ServerRequestInterface $request, array $ignoreHeaders = []): string
    {
        $headers = $request->getHeaders() ?? [];
        foreach ($ignoreHeaders as $header) {
            unset($headers[$header]);
        }

        return static::getHash($headers);
    }

    private static function getContentHash(ServerRequestInterface $request, array $ignoreContent = []): string
    {
        $content = $request->getParsedBody() ?? [];
        if (!empty($ignoreContent)) {
            $content = json_decode(json_encode($content), true);
            foreach ($ignoreContent as $ignoreContentEntry) {
                $content = ArrayUtility::dotWrite($content, $ignoreContentEntry);
            }
        }

        return static::getHash($content);
    }

    private static function getFilesHash(ServerRequestInterface $request, bool $ignoreFiles = false): string
    {
        $files = $ignoreFiles ? $request->getUploadedFiles() : [];

        return static::getHash($files);
    }

    public static function getHash(array $content = []): string
    {
        return md5(json_encode($content, JSON_THROW_ON_ERROR));
    }
}