<?php

namespace App\Entity;

class Mock
{
    private ?string $id;
    private ?string $originId;
    public ?Origin $origin = null;
    private ?string $uri;
    private ?string $method;
    private ?string $status;
    private ?array $headers;
    private $content;

    /**
     * @return string|null
     */
    final public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Mock
     */
    final public function setId(string $id): Mock
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOriginId(): ?string
    {
        return $this->originId;
    }

    /**
     * @param string|null $originId
     * @return Mock
     */
    public function setOriginId(?string $originId): Mock
    {
        $this->originId = $originId;
        return $this;
    }

    /**
     * @return Origin|null
     */
    public function getOrigin(): ?Origin
    {
        return $this->origin;
    }

    /**
     * @param Origin|null $origin
     * @return Mock
     */
    public function setOrigin(?Origin $origin = null): Mock
    {
        $this->origin = $origin;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @param string|null $uri
     * @return Mock
     */
    public function setUri(?string $uri): Mock
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string|null $method
     * @return Mock
     */
    public function setMethod(?string $method): Mock
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    final public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Mock
     */
    final public function setStatus(string $status): Mock
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    final public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return Mock
     */
    final public function setHeaders(array $headers): Mock
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed|null $content
     * @return Mock
     */
    public function setContent($content): Mock
    {
        $this->content = $content;
        return $this;
    }



}