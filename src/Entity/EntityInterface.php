<?php

declare(strict_types=1);

namespace Camoo\Hosting\Entity;

interface EntityInterface
{
    public function has(string $property): bool;

    public function get(string $property): mixed;

    /**
     * @param string|array<string,mixed> $data
     * @param ?mixed                     $value
     */
    public function set(array|string $data, mixed $value = null): void;

    public function convert(mixed $data): ?self;
}
