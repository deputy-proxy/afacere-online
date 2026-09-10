<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessMetric extends Model
{
    protected $guarded = ['id'];

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /** @return HasMany<BusinessMetricValue, $this> */
    public function values(): HasMany
    {
        return $this->hasMany(BusinessMetricValue::class);
    }
}
