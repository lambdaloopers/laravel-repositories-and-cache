<?php

namespace App\Console\Commands\API;

use Domain\FeatureFlags\Entities\FeatureFlag;
use Domain\FeatureFlags\Services\FeatureFlagRepository;
use Illuminate\Console\Command;

class APICreateFeatureFlag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:create-feature-flag {name} {isActive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a feature flag in the database';

    /**
     * Execute the console command.
     */
    public function handle(FeatureFlagRepository $featureFlagRepository)
    {
        $name = $this->argument('name');
        $isActive = $this->argument('isActive') == 'true';

        $featureFlag = new FeatureFlag(
            $name,
            $isActive
        );

        $result = $featureFlagRepository->save($featureFlag);

        if ($result) {
            $this->info('OK');
        } else {
            $this->error('KO');
        }
    }
}
