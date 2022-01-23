<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;

class Mock
{
    private ?string $id;
    private ?string $uuid = null;
    private ?string $date = null;
    private ?string $filePath = null;
    private ?string $originId;
    public ?Origin $origin = null;
    private ?string $uri;
    private ?string $method;
    private ?string $status;
    private ?array $headers;
    private $content;

    public function __construct()
    {
        $this->setUuid(Uuid::v4());
        $this->setDate();
    }

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
    final public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     * @return Mock
     */
    final public function setUuid(?string $uuid): Mock
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
     final public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * @param string|null $date
     * @return Mock
     */
    final public function setDate(?string $date = null): Mock
    {
        $this->date = $date;
        return $this;
    }



    /**
     * @return string|null
     */
    final public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string|null $filePath
     * @return Mock
     */
    final public function setFilePath(?string $filePath): Mock
    {
        $this->filePath = $filePath;
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