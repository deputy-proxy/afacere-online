<?php

namespace App\Events;

use App\Models\Business;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class BusinessStageChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Business $business,
        public readonly string $from,
        public readonly string $to,
    ) {}
}
