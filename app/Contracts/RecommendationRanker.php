<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Business;
use App\Models\User;

interface RecommendationRanker
{
    /**
     * @phpstan-param array<int, array<string, mixed>> $candidates
     *
     * @phpstan-return array<int, array<string, mixed>>
     */
    public function rank(Business $business, User $user, array $candidates): array;
}
