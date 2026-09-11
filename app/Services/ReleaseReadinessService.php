<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Schema;

final class ReleaseReadinessService
{
    /** @return array<string, bool> */
    public function checks(): array
    {
        return [
            'production' => app(ProductionReadinessService::class)->isReady(),
            'payments' => Schema::hasTable('payment_webhook_events'),
            'data_lifecycle' => Schema::hasTable('data_requests'),
            'audit_log' => Schema::hasTable('audit_logs'),
            'users' => Schema::hasTable('users'),
            'businesses' => Schema::hasTable('businesses'),
        ];
    }

    public function isReady(): bool
    {
        return ! in_array(false, $this->checks(), true);
    }
}
