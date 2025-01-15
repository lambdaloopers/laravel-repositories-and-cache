<?php

namespace Domain\FeatureFlags\Services;

use Domain\FeatureFlags\Entities\FeatureFlag;
use Illuminate\Database\Connection;
use Illuminate\Database\UniqueConstraintViolationException;

readonly class LegacyFeatureFlagRepository
{
    public function __construct(
        private Connection $mysql,
        private \Illuminate\Redis\Connections\Connection $redis
    ) {}

    public function getByName(string $name): ?FeatureFlag
    {
        $redisFeatureFlagValue = $this->redis->get($name);

        if ($redisFeatureFlagValue != null) {
            echo "Cache hit\n";

            return new FeatureFlag(
                $name,
                $redisFeatureFlagValue
            );
        }

        echo "Cache miss\n";

        $featureFlagRow = $this->mysql
            ->table('feature_flags')
            ->where('name', $name)
            ->first();

        if (is_null($featureFlagRow)) {
            return null;
        }

        $this->redis->set(
            $name,
            $featureFlagRow->is_active,
            'EX',
            5
        );

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
