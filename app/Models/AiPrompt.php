<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiPrompt extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['active' => 'boolean']; }
    /** @return HasMany<AiRun, $this> */
    public function runs(): HasMany { return $this->hasMany(AiRun::class); }
    /** @return HasMany<AiPromptVersion, $this> */
    public function versions(): HasMany { return $this->hasMany(AiPromptVersion::class); }
}
