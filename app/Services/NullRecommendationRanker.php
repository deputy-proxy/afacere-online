<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RecommendationRanker;
use App\Models\Business;
use App\Models\User;

final class NullRecommendationRanker implements RecommendationRanker
{
    public function rank(Business $business, User $user, array $candidates): array
    {
        return $candidates;
    }
}
