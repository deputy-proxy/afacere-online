<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMetricValue extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['value' => 'decimal:6', 'measured_at' => 'datetime', 'context' => 'array'];
    }

    /** @return BelongsTo<BusinessMetric, $this> */
    public function metric(): BelongsTo
    {
        return $this->belongsTo(BusinessMetric::class, 'business_metric_id');
    }
}
