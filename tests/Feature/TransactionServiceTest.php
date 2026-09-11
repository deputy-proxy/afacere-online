<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('is idempotent and grants no access before confirmation', function (): void {
    $user = User::factory()->create();
    $service = app(TransactionService::class);
    $first = $service->initiate($user, 'event', 10, 'test', 2500, 'checkout-1');
    $second = $service->initiate($user, 'event', 10, 'test', 2500, 'checkout-1');
    expect($second)->toBe($first);
    expect(DB::table('commerce_entitlements')->count())->toBe(0);
    $service->confirm($first, 'provider-1');
    expect(DB::table('commerce_transactions')->where('id', $first)->value('status'))->toBe('confirmed');
});
