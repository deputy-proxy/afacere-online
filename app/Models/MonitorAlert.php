<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorAlert extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['context' => 'array', 'triggered_at' => 'datetime', 'resolved_at' => 'datetime']; }
    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
    /** @return BelongsTo<MonitorThreshold, $this> */
    public function threshold(): BelongsTo { return $this->belongsTo(MonitorThreshold::class); }
}
