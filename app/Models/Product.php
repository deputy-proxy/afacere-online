<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = ['id'];

    /** @return HasMany<ProductPlan, $this> */
    public function plans(): HasMany
    {
        return $this->hasMany(ProductPlan::class);
    }
}
