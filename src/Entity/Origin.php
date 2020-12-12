<?php

namespace App\Entity;

class Origin
{
    private ?string $name = null;
    private ?string $label = null;
    private ?string $host = null;
    private ?string $mode = 'mock';

    /**
     * @return string|null
     */
    final public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    final public function setName(string $name): void
    {
        $this->name = $name;
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
     */
    final public function setLabel(string $label): void
    {
        $this->label = $label;
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
     */
    final public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return string|null
     */
    final public function getMode(): ?string
    {
        return $this->mode;
    }

    /**
     * @param string|null $mode
     */
    final public function setMode(?string $mode): void
    {
        $this->mode = $mode;
    }

    final public function isNew(): bool
    {
        return $this->getName() === null;
    }
}
