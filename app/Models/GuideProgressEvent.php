<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuideProgressEvent extends Model
{
    protected $table = 'guide_progress_events';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    /** @return BelongsTo<GuideProgress, $this> */
    public function progress(): BelongsTo
    {
        return $this->belongsTo(GuideProgress::class, 'guide_progress_id');
    }

    /** @return BelongsTo<Guide, $this> */
    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<GuideStep, $this> */
    public function step(): BelongsTo
    {
        return $this->belongsTo(GuideStep::class, 'guide_step_id');
    }
}
