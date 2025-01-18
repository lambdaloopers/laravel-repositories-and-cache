<?php

namespace Domain\JobPosts\Services;

use Domain\JobPosts\Entities\JobPost;

interface JobPostRepository
{
    public function getById(string $id): ?JobPost;

    public function search(JobPostSearchParams $searchParams): array;

    public function save(JobPost $jobPost): bool;
}
