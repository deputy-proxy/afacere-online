<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitorConfiguration extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['enabled' => 'boolean', 'next_check_in_at' => 'datetime']; }
    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
    /** @return HasMany<MonitorCheckIn, $this> */
    public function checkIns(): HasMany { return $this->hasMany(MonitorCheckIn::class, 'business_id', 'business_id'); }
}
