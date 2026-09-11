<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GuideTag extends Model
{
    protected $guarded = ['id'];

    /** @return BelongsToMany<Guide, $this> */
    public function guides(): BelongsToMany
    {
        return $this->belongsToMany(Guide::class, 'guide_tag_assignments');
    }
}
