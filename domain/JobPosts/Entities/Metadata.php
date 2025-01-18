<?php

namespace Domain\JobPosts\Entities;

class Metadata
{
    public function __construct(
        private array $metadata
    ) {}

    public function getAll(): array
    {
        return $this->metadata;
    }

    public function has(string $property): bool
    {
        return array_key_exists($property, $this->metadata);
    }

    public function get(string $property): mixed
    {
        return $this->metadata[$property];
    }

    public function set(string $property, mixed $value): void
    {
        $this->metadata[$property] = $value;
    }
}
