<?php

namespace Domain\FeatureFlags\Entities;

readonly class FeatureFlag
{
    public function __construct(
        private string $name,
        private bool $isActive
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
