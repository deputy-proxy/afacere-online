<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\Product;
use App\Models\ProductPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exposes the public acquisition pages without authentication', function (string $uri, string $text): void {
    $this->get($uri)->assertOk()->assertSee($text);
})->with([
    ['/', 'Turn business uncertainty into your next best action.'],
    ['/how-it-works', 'Understand the business. Choose the priority. Do the work.'],
    ['/pricing', 'Plans that follow the product configuration.'],
    ['/about', 'A calmer way to work on the business.'],
    ['/faq', 'Questions entrepreneurs reasonably ask.'],
    ['/contact', 'Need help with afacere.online?'],
    ['/legal', 'Privacy, terms and responsible product use.'],
]);

it('keeps entrepreneur application routes behind authentication', function (): void {
    $this->get('/dashboard')->assertRedirect(route('login'));
});

it('provides the primary public conversion route to account creation', function (): void {
    $this->get('/')
        ->assertSee('href="'.route('register').'"', false)
        ->assertSee('data-analytics-event="public.evaluator.cta"', false);
});

it('provides coherent public navigation for discovery areas', function (): void {
    $this->get('/')
        ->assertSee('How it works')
        ->assertSee('Ecosystem')
        ->assertSee('Pricing')
        ->assertSee('FAQ')
        ->assertSee('Guides')
        ->assertSee('Opportunities')
        ->assertSee('Funding')
        ->assertSee('Experts')
        ->assertSee('Marketplace')
        ->assertSee('Community')
        ->assertSee('Events')
        ->assertSee('Evaluator')
        ->assertSee('Sign in')
        ->assertDontSee('Laravel Repository')
        ->assertDontSee('Laravel Documentation');
});

it('renders only active configured commercial plans', function (): void {
    $product = Product::query()->create(['key' => 'business', 'name' => 'Business progression']);
    ProductPlan::query()->create([
        'product_id' => $product->id,
        'key' => 'starter',
        'name' => 'Starter',
        'price_minor' => 2900,
        'currency' => 'EUR',
        'entitlements' => ['business_limit' => 1, 'monitor' => true],
        'active' => true,
    ]);
    ProductPlan::query()->create([
        'product_id' => $product->id,
        'key' => 'retired',
        'name' => 'Retired',
        'price_minor' => 7900,
        'currency' => 'EUR',
        'entitlements' => ['business_limit' => 'unlimited'],
        'active' => false,
    ]);

    $this->get(route('public.pricing'))
        ->assertOk()
        ->assertSee('Starter')
        ->assertSee('29,00 EUR')
        ->assertSee('Monitor')
        ->assertDontSee('Retired')
        ->assertDontSee('79,00 EUR');
});

it('does not expose private business data on public discovery pages', function (): void {
    $business = Business::factory()->create(['name' => 'Private business name']);

    $this->get('/')->assertOk()->assertDontSee($business->name);
    $this->get(route('public.how-it-works'))->assertOk()->assertDontSee($business->name);
});
