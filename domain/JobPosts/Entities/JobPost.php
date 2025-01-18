<?php

namespace Domain\JobPosts\Entities;

readonly class JobPost
{
    public function __construct(
        private string $id,
        private string $title,
        private string $description,
        private string $jobPosition,
        private SalaryRange $salaryRange,
        private JobRemoteStatus $remoteStatus,
        private Metadata $metadata
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function jobPosition(): string
    {
        return $this->jobPosition;
    }

    public function minSalary(): ?float
    {
        return $this->salaryRange->minSalary();
    }

    public function maxSalary(): ?float
    {
        return $this->salaryRange->maxSalary();
    }

    public function remoteStatus(): JobRemoteStatus
    {
        return $this->remoteStatus;
    }

    public function metadata(): Metadata
    {
        return $this->metadata;
    }
}
