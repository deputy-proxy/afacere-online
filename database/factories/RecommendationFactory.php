<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Business;
use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Recommendation> */
class RecommendationFactory extends Factory
{
    protected $model = Recommendation::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'source_type' => 'system',
            'status' => 'suggested',
            'priority' => fake()->numberBetween(1, 5),
            'title' => fake()->sentence(4),
            'reason' => fake()->sentence(),
            'recommended_action' => fake()->sentence(),
            'expected_outcome' => fake()->sentence(),
            'confidence' => fake()->randomFloat(4, 0, 1),
            'context' => [],
        ];
    }
}
