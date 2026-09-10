<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuideStep extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['resources' => 'array'];
    }

    /** @return BelongsTo<GuideSection, $this> */
    public function section(): BelongsTo
    {
        return $this->belongsTo(GuideSection::class, 'guide_section_id');
    }
}
