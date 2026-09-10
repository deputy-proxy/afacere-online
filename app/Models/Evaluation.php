<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => EvaluationStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /** @return BelongsTo<EvaluationVersion, $this> */
    public function version(): BelongsTo
    {
        return $this->belongsTo(EvaluationVersion::class, 'evaluation_version_id');
    }

    /** @return HasMany<EvaluationAnswer, $this> */
    public function answers(): HasMany
    {
        return $this->hasMany(EvaluationAnswer::class);
    }

    /** @return HasMany<EvaluationFinding, $this> */
    public function findings(): HasMany
    {
        return $this->hasMany(EvaluationFinding::class);
    }
}
