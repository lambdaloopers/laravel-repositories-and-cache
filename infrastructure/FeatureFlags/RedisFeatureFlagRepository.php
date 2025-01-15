<?php

namespace Infrastructure\FeatureFlags;

use Domain\FeatureFlags\Entities\FeatureFlag;
use Domain\FeatureFlags\Services\FeatureFlagRepository;
use Illuminate\Redis\Connections\Connection;

class RedisFeatureFlagRepository implements FeatureFlagRepository
{
    public function __construct(private Connection $redis) {}

    public function getByName(string $name): ?FeatureFlag
    {
        $redisFeatureFlagValue = $this->redis->get($name);

        if (is_null($redisFeatureFlagValue)) {
            return null;
        }

        return new FeatureFlag(
            $name,
            $redisFeatureFlagValue
        );
    }

    public function save(FeatureFlag $featureFlag): bool
    {
        $this->redis->set(
            $featureFlag->name(),
            $featureFlag->isActive(),
            'EX',
            5
        );

        return true;
    }
}
