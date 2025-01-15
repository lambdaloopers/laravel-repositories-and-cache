<?php

namespace App\Console\Commands\Backoffice;

use Domain\FeatureFlags\Services\FeatureFlagRepository;
use Illuminate\Console\Command;

class BackofficeGetFeatureFlagByName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:get-feature-flag-by-name {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a feature flag by its name';

    /**
     * Execute the console command.
     */
    public function handle(FeatureFlagRepository $featureFlagRepository)
    {
        $name = $this->argument('name');

        $featureFlag = $featureFlagRepository->getByName($name);

        if (is_null($featureFlag)) {
            $this->error('KO');

            return;
        }

        $name = $featureFlag->name();
        $isActive = $featureFlag->isActive() ? 'true' : 'false';

        $this->info("$name | $isActive");
    }
}
