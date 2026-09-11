<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthIndicator extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['value' => 'decimal:4', 'context' => 'array', 'measured_at' => 'datetime']; }
    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
}
