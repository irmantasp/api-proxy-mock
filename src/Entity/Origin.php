<?php

namespace App\Entity;

class Origin
{
    private string $origin;
    private string $host;

    /**
     * @return string
     */
    final public function getOrigin(): string
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     */
    final public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    /**
     * @return string
     */
    final public function getHost(): string
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
}