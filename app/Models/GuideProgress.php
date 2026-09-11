<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuideProgress extends Model
{
    protected $table = 'guide_progress';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'completed_steps' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Guide, $this> */
    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /** @return BelongsTo<GuideStep, $this> */
    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(GuideStep::class, 'current_step_id');
    }

    /** @return HasMany<GuideProgressEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(GuideProgressEvent::class)->orderBy('occurred_at');
    }
}
