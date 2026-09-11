<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceProvider extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    /** @return HasMany<MarketplaceService, $this> */
    public function services(): HasMany
    {
        return $this->hasMany(MarketplaceService::class, 'provider_id');
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
}