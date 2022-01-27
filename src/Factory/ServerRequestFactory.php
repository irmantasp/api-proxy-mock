<?php

namespace App\Factory;

use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactory
{
    final public static function createFromParsedRequestArray(array $parsedRequest): ?ServerRequestInterface
    {
        if (!static::validateParsedRequestParams($parsedRequest)) {
            return null;
        }

        $attributes = $parsedRequest['attributes'];
        $cookieParams = $parsedRequest['cookieParams'];
        $parsedBody = $parsedRequest['parsedBody'];
        $queryParams = $parsedRequest['queryParams'];
        $serverParams = $parsedRequest['serverParams'];
        $uploadedFiles = $parsedRequest['uploadedFiles'];
        $method = $parsedRequest['method'];
        $requestTarget = $parsedRequest['requestTarget'];
        $uri = $parsedRequest['uri'];
        $headers = $parsedRequest['headers'];
        $protocol = $parsedRequest['protocol'] ?? '1.1';

        $request = new ServerRequest($method, '', $headers, null, $protocol, $serverParams);

        static::setAttributes($request, $attributes);
        static::setCookieParams($request, $cookieParams);
        static::setParsedBody($request, $parsedBody);
        static::setQueryParams($request, $queryParams);
        static::setUploadedFiles($request, $uploadedFiles);
        static::setRequestTarget($request, $requestTarget);
        static::setUri($request, $uri);
        static::setHeaders($request, $headers);

        return $request;
    }

    private static function validateParsedRequestParams(array $parsedRequest): bool
    {
        return isset(
            $parsedRequest['method'],
            $parsedRequest['uri'],
            $parsedRequest['headers'],
            $parsedRequest['parsedBody'],
            $parsedRequest['protocol'],
            $parsedRequest['serverParams'],
        );
    }

    private static function setAttributes(ServerRequestInterface $request, array $attributes = []): void
    {
        foreach ($attributes as $attribute => $value) {
            $request->withAttribute($attribute, $value);
        }
    }

    private static function setCookieParams(ServerRequestInterface $request, $cookieParams): void
    {
        if (!empty($cookieParams) && is_array($cookieParams)) {
            $request->withCookieParams($cookieParams);
        }
    }

    private static function setParsedBody(ServerRequestInterface $request, $parsedBody): void
    {
        if (!empty($parsedBody)) {
            $request->withParsedBody($parsedBody);
        }
    }

    private static function setQueryParams(ServerRequestInterface $request, $queryParams): void
    {
        if (!empty($queryParams)) {
            $request->withQueryParams($queryParams);
        }
    }

    private static function setUploadedFiles(ServerRequestInterface $request, $uploadedFiles): void
    {
        if (!empty($uploadedFiles)) {
            $request->withUploadedFiles($uploadedFiles);
        }
    }

    private static function setRequestTarget(ServerRequestInterface $request, $requestTarget): void
    {
        if (!empty($requestTarget)) {
            $request->withRequestTarget($requestTarget);
        }
    }

    private static function setUri(ServerRequestInterface $request, $uriParams): void
    {
        if (empty($uriParams)) {
            return;
        }

        $scheme = $uriParams['scheme'] ?? null;
        $host = $uriParams['host'] ?? null;
        $path = $uriParams['path'] ?? null;
        if (is_null($scheme) && is_null($host) && is_null($path)) {
            return;
        }

        $uri = new Uri();
        $uri
            ->withScheme($scheme)
            ->withHost($host)
            ->withPath($path);

        $port = $uriParams['port'] ?? null;
        if (!is_null($port)) {
            $uri->withPort($port);
        }

        $query = $uriParams['query'] ?? null;
        if (!is_null($query)) {
            $uri->withQuery($query);
        }

        $userInfo = $uriParams['userInfo'] ?? null;
        if (!is_null($userInfo)) {
            $uri->withUserInfo($userInfo);
        }

        $fragment = $uriParams['fragment'] ?? null;
        if (!is_null($fragment)) {
            $uri->withFragment($fragment);
        }

        $request->withUri($uri);
    }

    private static function setHeaders(ServerRequestInterface $request, array $headers = []): void
    {
        foreach ($headers as $header => $value) {
            $request->withHeader($header, $value);
        }
    }
}