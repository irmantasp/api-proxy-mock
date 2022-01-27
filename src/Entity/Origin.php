<?php

namespace App\Entity;

class Origin
{
    private ?string $name = null;
    private ?string $label = null;
    private ?string $host = null;
    private ?bool $record = false;
    private ?bool $saveOriginalRequest = false;
    private ?bool $log = false;
    private array $ignoreHeaders = [];
    private array $ignoreContent = [];
    private bool $ignoreFiles = false;

    /**
     * @return string|null
     */
    final public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Origin
     */
    final public function setName(string $name): Origin
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    final public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Origin
     */
    final public function setLabel(string $label): Origin
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    final public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return Origin
     */
    final public function setHost(string $host): Origin
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return bool|null
     */
    final public function getRecord(): ?bool
    {
        return $this->record;
    }

    /**
     * @param bool|null $record
     * @return Origin
     */
    final public function setRecord(?bool $record = false): Origin
    {
        $this->record = $record;
        return $this;
    }

    /**
     * @return bool|null
     */
    final public function getSaveOriginalRequest(): ?bool
    {
        return $this->saveOriginalRequest;
    }

    /**
     * @param bool|null $saveOriginalRequest
     * @return Origin
     */
    final public function setSaveOriginalRequest(?bool $saveOriginalRequest = false): Origin
    {
        $this->saveOriginalRequest = $saveOriginalRequest;
        return $this;
    }

    /**
     * @return bool|null
     */
    final public function getLog(): ?bool
    {
        return $this->log;
    }

    /**
     * @param bool|null $log
     * @return Origin
     */
    final public function setLog(?bool $log): Origin
    {
        $this->log = $log;
        return $this;
    }

    final public function isNew(): bool
    {
        return $this->getName() === null;
    }

    /**
     * @return array
     */
    final public function getIgnoreHeaders(): array
    {
        return $this->ignoreHeaders;
    }

    /**
     * @param array $ignoreHeaders
     * @return Origin
     */
    final public function setIgnoreHeaders(array $ignoreHeaders): Origin
    {
        $this->ignoreHeaders = $ignoreHeaders;
        return $this;
    }

    /**
     * @return array
     */
    final public function getIgnoreContent(): array
    {
        return $this->ignoreContent;
    }

    /**
     * @param array $ignoreContent
     * @return Origin
     */
    final public function setIgnoreContent(array $ignoreContent): Origin
    {
        $this->ignoreContent = $ignoreContent;
        return $this;
    }

    /**
     * @return bool
     */
    final public function getIgnoreFiles(): bool
    {
        return $this->ignoreFiles;
    }

    /**
     * @param bool $ignoreFiles
     * @return Origin
     */
    final public function setIgnoreFiles(bool $ignoreFiles): Origin
    {
        $this->ignoreFiles = $ignoreFiles;
        return $this;
    }

    final public function getIgnore(): array
    {
        return [
            'headers' => $this->getIgnoreHeaders(),
            'content' => $this->getIgnoreContent(),
            'files' => $this->getIgnoreFiles(),
        ];
    }


}
