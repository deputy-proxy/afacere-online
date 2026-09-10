<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPlan extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['entitlements' => 'array', 'active' => 'boolean']; }
    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
