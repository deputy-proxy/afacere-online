<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Priority extends Model
{
    protected $guarded = ['id'];
    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
    /** @return BelongsTo<Recommendation, $this> */
    public function recommendation(): BelongsTo { return $this->belongsTo(Recommendation::class); }
}
