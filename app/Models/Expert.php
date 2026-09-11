<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expert extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['expertise' => 'array', 'verified_at' => 'datetime'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<ExpertAvailability, $this> */
    public function availabilities(): HasMany
    {
        return $this->hasMany(ExpertAvailability::class);
    }

    /** @return HasMany<Consultation, $this> */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
}