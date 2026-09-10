<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Opportunity extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'criteria' => 'array',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'verified_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    /** @return BelongsTo<OpportunityType, $this> */
    public function type(): BelongsTo
    {
        return $this->belongsTo(OpportunityType::class, 'opportunity_type_id');
    }

    public function isCurrent(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->is_published
            && ($this->valid_from === null || $this->valid_from->lte($at))
            && ($this->valid_until === null || $this->valid_until->gte($at));
    }
}
