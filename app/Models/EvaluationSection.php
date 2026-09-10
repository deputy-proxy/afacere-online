<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationSection extends Model
{
    protected $guarded = ['id'];

    /** @return BelongsTo<EvaluationVersion, $this> */
    public function version(): BelongsTo
    {
        return $this->belongsTo(EvaluationVersion::class, 'evaluation_version_id');
    }

    /** @return HasMany<EvaluationQuestion, $this> */
    public function questions(): HasMany
    {
        return $this->hasMany(EvaluationQuestion::class)->orderBy('position');
    }
}
