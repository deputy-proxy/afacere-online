<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BusinessMetricAggregation;
use App\Enums\BusinessStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property BusinessMetricAggregation $aggregation
 * @property BusinessStage|null $stage
 */
class BusinessMetric extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'aggregation' => BusinessMetricAggregation::class,
            'stage' => BusinessStage::class,
        ];
    }

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
