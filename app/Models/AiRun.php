<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AiRun extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['input' => 'array', 'output' => 'array', 'cost' => 'decimal:6']; }
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
    /** @return BelongsTo<AiPrompt, $this> */
    public function prompt(): BelongsTo { return $this->belongsTo(AiPrompt::class, 'ai_prompt_id'); }
    /** @return HasOne<AiRecommendation, $this> */
    public function aiRecommendation(): HasOne { return $this->hasOne(AiRecommendation::class); }
}
