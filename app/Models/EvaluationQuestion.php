<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationQuestion extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'required' => 'boolean',
            'options' => 'array',
            'validation_rules' => 'array',
        ];
    }

    /** @return BelongsTo<EvaluationSection, $this> */
    public function section(): BelongsTo
    {
        return $this->belongsTo(EvaluationSection::class, 'evaluation_section_id');
    }
}
