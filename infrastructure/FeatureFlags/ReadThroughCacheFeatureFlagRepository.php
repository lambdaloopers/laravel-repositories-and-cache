<?php

namespace Infrastructure\FeatureFlags;

use Domain\FeatureFlags\Entities\FeatureFlag;
use Domain\FeatureFlags\Services\FeatureFlagRepository;

class ReadThroughCacheFeatureFlagRepository implements FeatureFlagRepository
{
    public function __construct(
        private FeatureFlagRepository $persistenceRepository,
        private FeatureFlagRepository $cacheRepository
    ) {}

    public function getByName(string $name): ?FeatureFlag
    {
        $featureFlag = $this->cacheRepository->getByName($name);

        if (! is_null($featureFlag)) {
            echo "Cache hit\n";

            return $featureFlag;
        }

        echo "Cache miss\n";

        $featureFlag = $this->persistenceRepository->getByName($name);

        if (is_null($featureFlag)) {
            return null;
        }

        $this->cacheRepository->save($featureFlag);

        return $featureFlag;
    }

    public function save(FeatureFlag $featureFlag): bool
    {
        return $this->persistenceRepository->save($featureFlag);
    }
}
