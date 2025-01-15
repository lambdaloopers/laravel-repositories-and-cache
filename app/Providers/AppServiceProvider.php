<?php

namespace App\Providers;

use App\Console\Commands\API\APICreateFeatureFlag;
use App\Console\Commands\API\APIGetFeatureFlagByName;
use App\Console\Commands\Backoffice\BackofficeCreateFeatureFlag;
use App\Console\Commands\Backoffice\BackofficeGetFeatureFlagByName;
use Domain\FeatureFlags\Services\FeatureFlagRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;
use Infrastructure\FeatureFlags\MysqlFeatureFlagRepository;
use Infrastructure\FeatureFlags\ReadThroughCacheFeatureFlagRepository;
use Infrastructure\FeatureFlags\RedisFeatureFlagRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app
            ->when([
                APICreateFeatureFlag::class,
                APIGetFeatureFlagByName::class,
            ])
            ->needs(FeatureFlagRepository::class)
            ->give(function () {
                return new ReadThroughCacheFeatureFlagRepository(
                    new MysqlFeatureFlagRepository(DB::connection('mysql')),
                    new RedisFeatureFlagRepository(Redis::connection('default'))
                );
            });

        $this->app
            ->when([
                BackofficeCreateFeatureFlag::class,
                BackofficeGetFeatureFlagByName::class,
            ])
            ->needs(FeatureFlagRepository::class)
            ->give(function () {
                return new MysqlFeatureFlagRepository(DB::connection('mysql'));
            });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
