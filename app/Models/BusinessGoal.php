<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BusinessGoalStatus;
use App\Enums\BusinessStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property BusinessGoalStatus $status
 * @property BusinessStage|null $stage
 */
class BusinessGoal extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'target' => 'decimal:4',
            'deadline' => 'date',
            'status' => BusinessGoalStatus::class,
            'stage' => BusinessStage::class,
        ];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
