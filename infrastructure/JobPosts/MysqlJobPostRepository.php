<?php

namespace Infrastructure\JobPosts;

use Domain\JobPosts\Entities\JobPost;
use Domain\JobPosts\Entities\Metadata;
use Domain\JobPosts\Entities\SalaryRange;
use Domain\JobPosts\Services\JobPostRepository;
use Domain\JobPosts\Services\JobPostSearchParams;
use Illuminate\Database\Connection;
use Illuminate\Database\UniqueConstraintViolationException;

readonly class MysqlJobPostRepository implements JobPostRepository
{
    public function __construct(
        private Connection $mysql
    ) {}

    public function getById(string $id): ?JobPost
    {
        $jobPostRow = $this->mysql
            ->table('job_posts')
            ->where('id', $id)
            ->first();

        if (is_null($jobPostRow)) {
            return null;
        }

        return new JobPost(
            $jobPostRow->id,
            $jobPostRow->title,
            $jobPostRow->description,
            $jobPostRow->position,
            new SalaryRange(
                $jobPostRow->min_salary,
                $jobPostRow->max_salary
            ),
            $jobPostRow->remote_status, // This should be transformed from string
            new Metadata($jobPostRow->metadata) // Maybe a json field?
        );
    }

    public function search(JobPostSearchParams $searchParams): array
    {
        $builder = $this->mysql
            ->table('job_posts');

        if (! is_null($searchParams->query())) {
            $builder->where('title', 'LIKE', '%'.$searchParams->query().'%')
                ->orWhere('description', 'LIKE', '%'.$searchParams->query().'%');

            // Search inside the metadata for the search query
        }

        if (! is_null($searchParams->jobPosition())) {
            $builder->where('job_position', $searchParams->jobPosition());
        }

        // ...

        $jobPostRows = $builder->get();

        $jobPosts = [];
        foreach ($jobPostRows as $jobPostRow) {
            $jobPosts[] = new JobPost(
                $jobPostRow->id,
                $jobPostRow->title,
                $jobPostRow->description,
                $jobPostRow->position,
                new SalaryRange(
                    $jobPostRow->min_salary,
                    $jobPostRow->max_salary
                ),
                $jobPostRow->remote_status,
                new Metadata($jobPostRow->metadata)
            );
        }

        return $jobPosts;
    }

    public function save(JobPost $jobPost): bool
    {
        try {
            $this->mysql
                ->table('feature_flags')
                ->insert([
                    'id' => $jobPost->id(),
                    'title' => $jobPost->title(),
                    'description' => $jobPost->description(),
                    'position' => $jobPost->jobPosition(),
                    'min_salary' => $jobPost->minSalary(),
                    'max_salary' => $jobPost->maxSalary(),
                    'remote_status' => $jobPost->remoteStatus(),
                    'metadata' => $jobPost->metadata(),
                ]);

            return true;
        } catch (UniqueConstraintViolationException) {
            return false;
        }
    }
}
