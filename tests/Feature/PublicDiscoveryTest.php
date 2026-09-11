<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exposes the public acquisition pages without authentication', function (string $uri, string $text): void {
    $this->get($uri)
        ->assertOk()
        ->assertSee($text);
})->with([
    ['/', 'Turn business uncertainty into your next best action.'],
    ['/how-it-works', 'Understand the business. Choose the priority. Do the work.'],
    ['/pricing', 'Start free. Pay when the business needs more.'],
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
