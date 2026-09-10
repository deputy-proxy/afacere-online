<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuideSection extends Model
{
    protected $guarded = ['id'];
    /** @return BelongsTo<Guide, $this> */
    public function guide(): BelongsTo { return $this->belongsTo(Guide::class); }
    /** @return HasMany<GuideStep, $this> */
    public function steps(): HasMany { return $this->hasMany(GuideStep::class)->orderBy('position'); }
}
