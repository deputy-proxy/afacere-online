<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['starts_at' => 'datetime', 'ends_at' => 'datetime']; }
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    /** @return BelongsTo<ProductPlan, $this> */
    public function plan(): BelongsTo { return $this->belongsTo(ProductPlan::class, 'product_plan_id'); }
    public function isActive(?Carbon $at = null): bool
    {
        $at ??= now();
        return $this->status === 'active' && $this->starts_at->lte($at) && ($this->ends_at === null || $this->ends_at->gt($at));
    }
}
