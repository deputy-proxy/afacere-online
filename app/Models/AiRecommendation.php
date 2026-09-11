<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRecommendation extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['payload' => 'array', 'confirmed_at' => 'datetime']; }
    /** @return BelongsTo<AiRun, $this> */
    public function run(): BelongsTo { return $this->belongsTo(AiRun::class, 'ai_run_id'); }
    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
    /** @return BelongsTo<Recommendation, $this> */
    public function recommendation(): BelongsTo { return $this->belongsTo(Recommendation::class); }
}
