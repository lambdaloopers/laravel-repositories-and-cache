<?php

namespace Domain\FeatureFlags\Services;

use Domain\FeatureFlags\Entities\FeatureFlag;

interface FeatureFlagRepository
{
    public function getByName(string $name): ?FeatureFlag;

    public function save(FeatureFlag $featureFlag): bool;
}
