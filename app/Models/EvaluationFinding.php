<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationFinding extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['score' => 'decimal:2', 'context' => 'array'];
    }

    /** @return BelongsTo<Evaluation, $this> */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }
}
