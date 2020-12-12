<?php

namespace App\Manager;

use App\Entity\Origin;

interface OriginManagerInterface
{
    public function save(Origin $origin): bool;

    public function load(string $originId): ?Origin;

    public function loadMultiple(?array $originIds = []): ?array;

    public function delete(Origin $origin): bool;

    public function exists(string $originId): bool;

}