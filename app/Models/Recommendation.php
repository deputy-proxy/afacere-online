<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recommendation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['confidence' => 'decimal:4', 'context' => 'array', 'accepted_at' => 'datetime', 'rejected_at' => 'datetime'];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
    /** @return BelongsTo<Evaluation, $this> */
    public function evaluation(): BelongsTo { return $this->belongsTo(Evaluation::class); }
    /** @return BelongsTo<EvaluationFinding, $this> */
    public function finding(): BelongsTo { return $this->belongsTo(EvaluationFinding::class, 'evaluation_finding_id'); }
    /** @return HasMany<Priority, $this> */
    public function priorities(): HasMany { return $this->hasMany(Priority::class); }
}
