<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Business;
use App\Models\User;

interface RecommendationRanker
{
    public function rank(Business $business, User $user, array $candidates): array;
}
