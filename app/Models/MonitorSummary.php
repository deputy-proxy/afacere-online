<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorSummary extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['summary' => 'array', 'period_start' => 'date', 'period_end' => 'date'];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
