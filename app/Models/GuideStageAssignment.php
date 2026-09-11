<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuideStageAssignment extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    /** @return BelongsTo<Guide, $this> */
    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }
}
