<?php

namespace App\Models;

use App\Enums\BusinessStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessStageHistory extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'from_stage' => BusinessStage::class,
            'to_stage' => BusinessStage::class,
            'changed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
