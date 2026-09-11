<?php

declare(strict_types=1);

use App\Models\Guide;
use App\Models\Opportunity;
use App\Models\User;
use App\Services\UnifiedSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('searches only published public resources', function (): void {
    $user = User::factory()->create();
    Guide::query()->create(['title' => 'Sales Guide', 'slug' => 'sales-guide', 'description' => 'Improve sales', 'published_at' => now()]);
    Guide::query()->create(['title' => 'Private Guide', 'slug' => 'private-guide', 'description' => 'Improve sales', 'published_at' => null]);
    Opportunity::query()->create(['title' => 'Sales Opportunity', 'description' => 'Sales', 'is_published' => true]);
    Opportunity::query()->create(['title' => 'Hidden Opportunity', 'description' => 'Sales', 'is_published' => false]);
    $results = app(UnifiedSearchService::class)->search($user, 'Sales');
    expect($results)->toHaveCount(2);
});
