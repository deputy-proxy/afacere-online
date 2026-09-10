<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActionPlan extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /** @return HasMany<Action, $this> */
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    /** @return HasMany<ActionPlanRevision, $this> */
    public function revisions(): HasMany
    {
        return $this->hasMany(ActionPlanRevision::class)->orderByDesc('version');
    }
}
