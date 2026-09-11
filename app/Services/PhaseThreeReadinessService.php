<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

final class PhaseThreeReadinessService
{
    /** @return array<string, bool> */
    public function checks(): array
    {
        return [
            'experts' => $this->tableExists('experts'),
            'marketplace' => $this->tableExists('marketplace_providers'),
            'community' => $this->tableExists('community_posts'),
            'events' => $this->tableExists('platform_events'),
            'documents' => $this->tableExists('document_shares'),
            'commerce' => $this->tableExists('commerce_transactions'),
            'analytics' => $this->tableExists('product_metric_snapshots'),
            'trust' => $this->tableExists('trust_actions'),
        ];
    }

    public function isReady(): bool
    {
        return array_sum(array_map(static fn (bool $ready): int => (int) $ready, $this->checks())) === 8;
    }

    private function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }
}
