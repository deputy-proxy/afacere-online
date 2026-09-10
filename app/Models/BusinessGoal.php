<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessGoal extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['target' => 'decimal:4', 'deadline' => 'date'];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
