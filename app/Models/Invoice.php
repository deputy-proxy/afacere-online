<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['issued_at' => 'datetime', 'due_at' => 'datetime', 'paid_at' => 'datetime']; }
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    /** @return BelongsTo<Subscription, $this> */
    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }
}
