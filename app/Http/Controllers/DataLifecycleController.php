<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DataLifecycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DataLifecycleController
{
    public function export(Request $request, DataLifecycleService $service): JsonResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return response()->json($service->export($user));
    }

    public function requestDeletion(Request $request, DataLifecycleService $service): JsonResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return response()->json([
            'data_request_id' => $service->requestDeletion($user),
            'status' => 'pending',
        ], 202);
    }
}
