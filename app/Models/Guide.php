<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guide extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /** @return HasMany<GuideSection, $this> */
    public function sections(): HasMany
    {
        return $this->hasMany(GuideSection::class)->orderBy('position');
    }

    /** @return HasMany<GuideRevision, $this> */
    public function revisions(): HasMany
    {
        return $this->hasMany(GuideRevision::class)->orderByDesc('version');
    }

    /** @return HasMany<GuideProgress, $this> */
    public function progress(): HasMany
    {
        return $this->hasMany(GuideProgress::class);
    }

    /** @return BelongsToMany<GuideTag, $this> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(GuideTag::class, 'guide_tag_assignments');
    }

    /** @return HasMany<GuideStageAssignment, $this> */
    public function stageAssignments(): HasMany
    {
        return $this->hasMany(GuideStageAssignment::class);
    }

    /** @return BelongsToMany<Priority, $this> */
    public function priorities(): BelongsToMany
    {
        return $this->belongsToMany(Priority::class, 'guide_priority_assignments');
    }

    /** @return BelongsToMany<Action, $this> */
    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class, 'guide_action_assignments');
    }
}
