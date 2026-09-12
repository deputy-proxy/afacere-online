<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DataRequest;
use App\Models\User;
use App\Services\DataLifecycleService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
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

        $dataRequest = $service->requestDeletion($user);

        return response()->json([
            'data_request_id' => $dataRequest->id,
            'status' => $dataRequest->status,
        ], 202);
    }

    public function deletionStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $dataRequest = DataRequest::query()
            ->where('user_id', $user->id)
            ->where('type', DataRequest::TYPE_DELETION)
            ->latest('requested_at')
            ->first();

        return response()->json([
            'data_request_id' => $dataRequest?->id,
            'status' => $dataRequest?->status,
            'requested_at' => $this->formatDate($dataRequest?->requested_at),
            'completed_at' => $this->formatDate($dataRequest?->completed_at),
        ]);
    }

    public function approveDeletion(Request $request, DataRequest $dataRequest, DataLifecycleService $service): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User && $actor->isAdmin(), 403);
        abort_unless($dataRequest->type === DataRequest::TYPE_DELETION, 404);

        $dataRequest = $service->approveDeletion($dataRequest, $actor);

        return response()->json([
            'data_request_id' => $dataRequest->id,
            'status' => $dataRequest->status,
        ]);
    }

    public function rejectDeletion(Request $request, DataRequest $dataRequest, DataLifecycleService $service): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User && $actor->isAdmin(), 403);
        abort_unless($dataRequest->type === DataRequest::TYPE_DELETION, 404);

        $dataRequest = $service->rejectDeletion($dataRequest, $actor);

        return response()->json([
            'data_request_id' => $dataRequest->id,
            'status' => $dataRequest->status,
        ]);
    }

    private function formatDate(CarbonInterface|string|null $value): ?string
    {
        if ($value instanceof CarbonInterface) {
            return $value->toIso8601String();
        }

        return $value === null ? null : Carbon::parse($value)->toIso8601String();
    }
}
