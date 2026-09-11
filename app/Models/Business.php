<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BusinessStage;
use Database\Factories\BusinessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** @property BusinessStage $stage */
class Business extends Model
{
    /** @use HasFactory<BusinessFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['stage' => BusinessStage::class, 'profile' => 'array', 'context' => 'array', 'preferences' => 'array'];
    }

    /** @return BelongsToMany<User, $this> */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_members')->withPivot(['role', 'joined_at'])->withTimestamps();
    }

    /** @return HasMany<BusinessInvitation, $this> */
    public function invitations(): HasMany
    {
        return $this->hasMany(BusinessInvitation::class);
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

    /** @return HasMany<BusinessDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(BusinessDocument::class);
    }

    /** @return HasOne<MonitorConfiguration, $this> */
    public function monitorConfiguration(): HasOne
    {
        return $this->hasOne(MonitorConfiguration::class);
    }

    /** @return HasMany<MonitorCheckIn, $this> */
    public function monitorCheckIns(): HasMany
    {
        return $this->hasMany(MonitorCheckIn::class);
    }

    /** @return HasMany<OpportunityMatch, $this> */
    public function opportunityMatches(): HasMany
    {
        return $this->hasMany(OpportunityMatch::class);
    }

    /** @return HasMany<OpportunityApplication, $this> */
    public function opportunityApplications(): HasMany
    {
        return $this->hasMany(OpportunityApplication::class);
    }

    /** @return HasMany<HealthIndicator, $this> */
    public function healthIndicators(): HasMany
    {
        return $this->hasMany(HealthIndicator::class);
    }
}
