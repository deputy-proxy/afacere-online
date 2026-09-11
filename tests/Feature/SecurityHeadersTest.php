<?php

declare(strict_types=1);

it('adds baseline security headers to web responses', function (): void {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');
});
