<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RecommendationRanker;
use App\Models\Business;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

final class RecommendationPresentationService
{
    /** @return Collection<int, Recommendation> */
    public function forBusiness(Business $business, User $user): Collection
    {
        Gate::forUser($user)->authorize('view', $business);

        return Recommendation::query()
            ->where('business_id', $business->id)
            ->where('status', 'suggested')
            ->latest()
            ->limit(5)
            ->get();
    }

    /** @return array<string, mixed> */
    public function context(Recommendation $recommendation): array
    {
        $context = $recommendation->getAttribute('context');
        $context = is_array($context) ? $context : [];

        return [
            'reason' => (string) ($recommendation->getAttribute('reason') ?? 'Relevant to the current business context.'),
            'expected_outcome' => (string) ($context['expected_outcome'] ?? 'Create measurable progress on this priority.'),
            'confidence' => (float) ($recommendation->getAttribute('confidence') ?? 0),
            'source' => (string) ($context['source'] ?? 'deterministic'),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $candidates
     * @return array<int, array<string, mixed>>
     */
    public function rank(Business $business, User $user, array $candidates): array
    {
        Gate::forUser($user)->authorize('view', $business);

        return app(RecommendationRanker::class)->rank($business, $user, $candidates);
    }
}
