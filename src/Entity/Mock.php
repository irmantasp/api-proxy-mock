<?php

namespace App\Entity;

class Mock
{

    private int $status;
    private array $headers;

    /**
     * @return int
     */
    final public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    final public function setStatus(int $status): void
    {
        $this->status = $status;
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
     */
    final public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

}