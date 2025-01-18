<?php

namespace Domain\JobPosts\Services;

use Domain\JobPosts\Entities\JobRemoteStatus;
use Domain\JobPosts\Entities\SalaryRange;

readonly class JobPostSearchParams
{
    public function __construct(
        private ?string $query = null,
        private ?string $jobPosition = null,
        private ?SalaryRange $salaryRange = null,
        private ?JobRemoteStatus $remoteStatus = JobRemoteStatus::Unknown
    ) {}

    public function query(): ?string
    {
        return $this->query;
    }

    public function jobPosition(): ?string
    {
        return $this->jobPosition;
    }

    public function salaryRange(): ?SalaryRange
    {
        return $this->salaryRange;
    }

    public function remoteStatus(): ?JobRemoteStatus
    {
        return $this->remoteStatus;
    }
}
