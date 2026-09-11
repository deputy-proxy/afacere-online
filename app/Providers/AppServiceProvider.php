<?php

namespace App\Providers;

use App\Contracts\RecommendationRanker;
use App\Services\NullRecommendationRanker;
use App\Services\PerformanceMonitoringService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RecommendationRanker::class, NullRecommendationRanker::class);
    }

    public function boot(): void
    {
        $this->configureDefaults();
        app(PerformanceMonitoringService::class)->register();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
