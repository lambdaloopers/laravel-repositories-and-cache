<?php

namespace Infrastructure\FeatureFlags;

use Domain\FeatureFlags\Entities\FeatureFlag;
use Domain\FeatureFlags\Services\FeatureFlagRepository;
use Illuminate\Database\Connection;
use Illuminate\Database\UniqueConstraintViolationException;

class MysqlFeatureFlagRepository implements FeatureFlagRepository
{
    public function __construct(
        private Connection $mysql
    ) {}

    public function getByName(string $name): ?FeatureFlag
    {
        $featureFlagRow = $this->mysql
            ->table('feature_flags')
            ->where('name', $name)
            ->first();

        if (is_null($featureFlagRow)) {
            return null;
        }

        return new FeatureFlag(
            $featureFlagRow->name,
            $featureFlagRow->is_active
        );
    }

    public function save(FeatureFlag $featureFlag): bool
    {
        try {
            $this->mysql
                ->table('feature_flags')
                ->insert([
                    'name' => $featureFlag->name(),
                    'is_active' => $featureFlag->isActive(),
                ]);

            return true;
        } catch (UniqueConstraintViolationException) {
            return false;
        }
    }
}
