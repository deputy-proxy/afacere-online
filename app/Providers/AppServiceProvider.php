<?php

namespace App\Providers;

use App\Contracts\RecommendationRanker;
use App\Services\NullRecommendationRanker;
use App\Services\ObservabilityService;
use App\Services\PerformanceMonitoringService;
use Carbon\CarbonImmutable;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
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
        $this->registerOperationalMonitoring();
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

    private function registerOperationalMonitoring(): void
    {
        Queue::failing(function (JobFailed $event): void {
            app(ObservabilityService::class)->record('queue.job_failed', [
                'connection' => $event->connectionName,
                'job' => $event->job->resolveName(),
                'queue' => $event->job->getQueue(),
                'exception' => $event->exception::class,
            ], 'error');
        });
    }
}
