<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Guide;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class UnifiedSearchService
{
    /** @return Collection<int, array{type: string, id: int, title: string}> */
    public function search(User $user, string $term): Collection
    {
        unset($user);
        $term = trim($term);
        if ($term === '') {
            return collect();
        }

        $guides = Guide::query()->whereNotNull('published_at')->where(function (Builder $query) use ($term): void {
            $query->where('title', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%");
        })->limit(20)->get()->map(fn (Guide $guide): array => ['type' => 'guide', 'id' => $guide->id, 'title' => $guide->title]);
        $opportunities = Opportunity::query()->where('is_published', true)->where(function (Builder $query) use ($term): void {
            $query->where('title', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%");
        })->limit(20)->get()->map(fn (Opportunity $opportunity): array => ['type' => 'opportunity', 'id' => $opportunity->id, 'title' => $opportunity->title]);

        return $guides->concat($opportunities)->values();
    }
}