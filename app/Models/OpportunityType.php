<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpportunityType extends Model
{
    protected $guarded = ['id'];

    /** @return HasMany<Opportunity, $this> */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }
}
