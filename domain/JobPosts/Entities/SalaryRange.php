<?php

namespace Domain\JobPosts\Entities;

readonly class SalaryRange
{
    public function __construct(
        private ?float $minSalary = null,
        private ?float $maxSalary = null
    ) {}

    public function minSalary(): ?float
    {
        return $this->minSalary;
    }

    public function maxSalary(): ?float
    {
        return $this->maxSalary;
    }
}
