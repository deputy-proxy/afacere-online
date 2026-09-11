<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiPromptVersion extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['output_schema' => 'array', 'active' => 'boolean']; }
    /** @return BelongsTo<AiPrompt, $this> */
    public function prompt(): BelongsTo { return $this->belongsTo(AiPrompt::class, 'ai_prompt_id'); }
}
