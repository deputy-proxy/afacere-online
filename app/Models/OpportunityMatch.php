<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpportunityMatch extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['score' => 'decimal:3', 'criteria_results' => 'array', 'matched_at' => 'datetime'];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }

    /** @return BelongsTo<Opportunity, $this> */
    public function opportunity(): BelongsTo { return $this->belongsTo(Opportunity::class); }
}
