<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminSupportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminSupportController
{
    public function summary(User $user, AdminSupportService $service): JsonResponse
    {
        return response()->json($service->summary($user));
    }

    public function passwordRecovery(Request $request, User $user, AdminSupportService $service): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User && $actor->isAdmin(), 403);

        return response()->json([
            'status' => $service->sendPasswordRecovery($actor, $user),
        ]);
    }
}
