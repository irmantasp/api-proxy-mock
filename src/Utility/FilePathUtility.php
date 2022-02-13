<?php

namespace App\Utility;

use Cocur\Slugify\Slugify;
use Psr\Http\Message\ServerRequestInterface;
use Rexlabs\UtilityBelt\ArrayUtility;

class FilePathUtility
{

    final public static function name(ServerRequestInterface $request, array $ignore = [], array $options = []): string
    {
        return vsprintf('%s/%s/%s-%s-%s.json', static::nameParts($request, $ignore, $options));
    }

    final public static function nameParts(ServerRequestInterface $request, array $ignore = [], array $options = []): array
    {
        $ignoreHeaders = $ignore['headers'] ?? [];
        $ignoreContent = $ignore['content'] ?? [];
        $ignoreFiles = isset($ignore['files']) && $ignore['files'];

        return [
            static::getUriProcessed($request, $options),
            static::getMethod($request),
            static::getHeaderHash($request, $ignoreHeaders),
            static::getContentHash($request, $ignoreContent),
            static::getFilesHash($request, $ignoreFiles),
        ];
    }

    final public static function namePartsKeyed(ServerRequestInterface $request, array $ignore = [], array $options = []): array
    {
        $ignoreHeaders = $ignore['headers'] ?? [];
        $ignoreContent = $ignore['content'] ?? [];
        $ignoreFiles = isset($ignore['files']) && $ignore['files'];

        return [
            'uri'       => static::getUriProcessed($request, $options),
            'method'    => static::getMethod($request),
            'headers'   => static::getHeaderHash($request, $ignoreHeaders),
            'content'   => static::getContentHash($request, $ignoreContent),
            'files'     => static::getFilesHash($request, $ignoreFiles),
        ];
    }

    final public static function originalNameParts(ServerRequestInterface $request, array $ignore = [], array $options = []): array
    {
        $ignoreHeaders = $ignore['headers'] ?? [];
        $ignoreContent = $ignore['content'] ?? [];
        $ignoreFiles = isset($ignore['files']) && $ignore['files'];

        return [
            static::getUriProcessed($request, $options),
            static::getMethod($request),
            static::getHeaders($request, $ignoreHeaders),
            static::getContent($request, $ignoreContent),
            static::getFiles($request, $ignoreFiles),
        ];
    }

    final public static function originalNamePartsKeyed(ServerRequestInterface $request, array $ignore = [], array $options = []): array
    {
        $ignoreHeaders = $ignore['headers'] ?? [];
        $ignoreContent = $ignore['content'] ?? [];
        $ignoreFiles = isset($ignore['files']) && $ignore['files'];

        return [
            'uri'       => static::getUriProcessed($request, $options),
            'method'    => static::getMethod($request),
            'headers'   => static::getHeaders($request, $ignoreHeaders),
            'content'   => static::getContent($request, $ignoreContent),
            'files'     => static::getFiles($request, $ignoreFiles),
        ];
    }

    private static function getUriProcessed(ServerRequestInterface $request, array $options = []): string
    {
        return static::isUriHashRequired($options) ? static::getUriHash($request) : static::getUri($request);
    }

    private static function getUriHash(ServerRequestInterface $request): string
    {
        return static::getHash([static::getUri($request)]);
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
        return static::getHash(static::getHeaders($request, $ignoreHeaders));
    }

    private static function getHeaders(ServerRequestInterface $request, array $ignoreHeaders = []): array
    {
        $headers = $request->getHeaders() ?? [];
        foreach ($ignoreHeaders as $header) {
            unset($headers[$header]);
        }

        ksort($headers);

        return $headers;
    }

    private static function getContentHash(ServerRequestInterface $request, array $ignoreContent = []): string
    {
        return static::getHash(static::getContent($request, $ignoreContent));
    }

    private static function getContent(ServerRequestInterface $request, array $ignoreContent = []): array
    {
        $content = $request->getParsedBody() ?? [];
        if (!empty($ignoreContent)) {
            $content = json_decode(json_encode($content, JSON_THROW_ON_ERROR), true, 2048, JSON_THROW_ON_ERROR);
            foreach ($ignoreContent as $ignoreContentEntry) {
                $content = ArrayUtility::dotWrite($content, $ignoreContentEntry);
            }
        }

        return $content;
    }

    private static function getFilesHash(ServerRequestInterface $request, bool $ignoreFiles = false): string
    {
        return static::getHash(static::getFiles($request, $ignoreFiles));
    }

    private static function getFiles(ServerRequestInterface $request, bool $ignoreFiles = false): array
    {
        return $ignoreFiles ? $request->getUploadedFiles() : [];
    }

    public static function getHash(array $content = []): string
    {
        return md5(json_encode($content, JSON_THROW_ON_ERROR));
    }

    private static function isUriHashRequired(array $options = []): bool
    {
        if (!isset($options['hashUri'])) {
            return false;
        }

        return $options['hashUri'] === true;
    }
}