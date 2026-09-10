<?php

namespace App\Models;

use App\Enums\ActionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** @property ActionStatus $status */
class Action extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => ActionStatus::class,
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<ActionPlan, $this> */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(ActionPlan::class, 'action_plan_id');
    }

    /** @return BelongsTo<Priority, $this> */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    /** @return BelongsTo<Recommendation, $this> */
    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    /** @return HasOne<ActionOutcome, $this> */
    public function outcome(): HasOne
    {
        return $this->hasOne(ActionOutcome::class);
    }

    /** @return HasMany<ActionEvidence, $this> */
    public function evidence(): HasMany
    {
        return $this->hasMany(ActionEvidence::class)->orderByDesc('recorded_at');
    }
}
