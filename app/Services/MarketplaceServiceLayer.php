<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\MarketplaceProvider;
use App\Models\MarketplaceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

final class MarketplaceServiceLayer
{
    /** @return Collection<int, MarketplaceProvider> */
    public function discover(Business $business, string $term = ''): Collection
    {
        $query = MarketplaceProvider::query()->where('verification_status', 'verified')->whereHas('services', function (Builder $serviceQuery): void {
            $serviceQuery->where('is_published', true);
        });
        if ($term !== '') {
            $query->where(function (Builder $providerQuery) use ($term): void {
                $providerQuery->where('name', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%");
            });
        }

        return $query->orderBy('name')->get();
    }

    /** @param array<int, mixed> $sharedContext */
    public function createLead(Business $business, MarketplaceProvider $provider, MarketplaceService $service, string $message, array $sharedContext = []): int
    {
        abort_unless($business->members()->exists(), 403);
        if ($provider->verification_status !== 'verified' || $service->provider_id !== $provider->id || ! $service->is_published) {
            throw ValidationException::withMessages(['service' => 'This service is not currently available.']);
        }

        return (int) $business->newQuery()->findOrFail($business->id)->getConnection()->table('marketplace_leads')->insertGetId([
            'business_id' => $business->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'message' => $message,
            'shared_context' => json_encode($sharedContext, JSON_THROW_ON_ERROR),
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
