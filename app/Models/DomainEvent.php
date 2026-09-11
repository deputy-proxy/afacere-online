<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainEvent extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['payload' => 'array', 'occurred_at' => 'datetime']; }
    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
}
