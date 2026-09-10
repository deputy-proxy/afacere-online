<?php

namespace App\Models;

use App\Enums\BusinessStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property BusinessStage $stage
 * @property array<string, mixed>|null $profile
 * @property array<string, mixed>|null $preferences
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Business extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'stage' => BusinessStage::class,
            'profile' => 'array',
            'preferences' => 'array',
        ];
    }

    /** @return BelongsToMany<User, $this> */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_members')
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    /** @return HasMany<BusinessStageHistory, $this> */
    public function stageHistory(): HasMany
    {
        return $this->hasMany(BusinessStageHistory::class);
    }

    /** @return HasMany<BusinessGoal, $this> */
    public function goals(): HasMany
    {
        return $this->hasMany(BusinessGoal::class);
    }

    /** @return HasMany<BusinessMetric, $this> */
    public function metrics(): HasMany
    {
        return $this->hasMany(BusinessMetric::class);
    }

    /** @return HasMany<Evaluation, $this> */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
