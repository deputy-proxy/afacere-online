<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ProductionReadinessService;
use Illuminate\Http\JsonResponse;

final class ReadinessController
{
    public function __invoke(ProductionReadinessService $readiness): JsonResponse
    {
        $checks = $readiness->checks();
        $ready = $readiness->isReady();

        return response()->json([
            'status' => $ready ? 'ready' : 'not_ready',
            'checks' => $checks,
        ], $ready ? 200 : 503);
    }
}
