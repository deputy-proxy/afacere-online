<?php

declare(strict_types=1);

use App\Livewire\Business\Monitor;
use App\Models\Business;
use App\Models\MonitorConfiguration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('monitor shows an explicit empty state when not configured', function (): void {
    $user = User::factory()->admin()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);

    $this->actingAs($user)
        ->get(route('business.monitor'))
        ->assertOk()
        ->assertSee('Monitor is not configured');
});

test('monitor renders configured check-in controls and trend state', function (): void {
    $user = User::factory()->admin()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    MonitorConfiguration::query()->create([
        'business_id' => $business->id,
        'enabled' => true,
        'cadence' => 'weekly',
        'next_check_in_at' => now()->addWeek(),
    ]);

    Livewire::actingAs($user)
        ->test(Monitor::class)
        ->assertSee('Check-in')
        ->assertSee('Record at least two check-ins to see changes over time.');
});

test('monitor validates check-in input before recording data', function (): void {
    $user = User::factory()->admin()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    MonitorConfiguration::query()->create([
        'business_id' => $business->id,
        'enabled' => true,
        'cadence' => 'weekly',
        'next_check_in_at' => now()->addWeek(),
    ]);

    Livewire::actingAs($user)
        ->test(Monitor::class)
        ->call('checkIn')
        ->assertHasErrors(['revenue', 'cash', 'customers', 'confidence']);

    $this->assertDatabaseCount('monitor_check_ins', 0);
});
