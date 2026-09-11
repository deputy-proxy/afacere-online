<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationShare extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['fields' => 'array', 'consented_at' => 'datetime'];
    }

    /** @return BelongsTo<Consultation, $this> */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }
}