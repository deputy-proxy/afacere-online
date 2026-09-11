<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class PerformanceMonitoringService
{
    private static bool $registered = false;

    public function register(): void
    {
        if (self::$registered) {
            return;
        }

        self::$registered = true;

        DB::listen(function (QueryExecuted $query): void {
            $threshold = (float) config('performance.slow_query_ms', 500);

            if ($query->time < $threshold) {
                return;
            }

            Log::warning('Slow database query detected.', [
                'connection' => $query->connectionName,
                'duration_ms' => $query->time,
            ]);
        });
    }
}
