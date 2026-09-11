<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BusinessStage;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Business> */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 999999),
            'description' => fake()->optional()->sentence(),
            'website' => fake()->optional()->url(),
            'stage' => BusinessStage::Idea,
            'profile' => [],
            'context' => [],
            'preferences' => [],
        ];
    }

    /** @return self */
    public function atStage(BusinessStage $stage): self
    {
        return $this->state(fn (): array => ['stage' => $stage]);
    }
}
