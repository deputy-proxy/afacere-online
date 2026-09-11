<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Schema;

final class ProductionReadinessService
{
    /** @return array<string, bool> */
    public function checks(): array
    {
        return [
            'application_key' => (bool) config('app.key'),
            'storage' => (bool) config('filesystems.default'),
            'queue' => (bool) config('queue.default'),
            'cache' => (bool) config('cache.default'),
            'session' => (bool) config('session.driver'),
            'mail' => (bool) config('mail.default'),
            'users' => Schema::hasTable('users'),
            'businesses' => Schema::hasTable('businesses'),
            'phase_three' => app(PhaseThreeReadinessService::class)->isReady(),
        ];
    }

    public function isReady(): bool
    {
        return ! in_array(false, $this->checks(), true);
    }
}
