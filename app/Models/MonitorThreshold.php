<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorThreshold extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['threshold' => 'decimal:6', 'enabled' => 'boolean']; }
    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
}
