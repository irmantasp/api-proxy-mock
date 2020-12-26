<?php

namespace App\Entity;

class Origin
{
    private ?string $name = null;
    private ?string $label = null;
    private ?string $host = null;

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

    final public function isNew(): bool
    {
        return $this->getName() === null;
    }
}
