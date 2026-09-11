<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'cancelled_at' => 'datetime'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }
}