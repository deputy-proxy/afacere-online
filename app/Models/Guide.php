<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guide extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['published_at' => 'datetime']; }
    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    /** @return HasMany<GuideSection, $this> */
    public function sections(): HasMany { return $this->hasMany(GuideSection::class)->orderBy('position'); }
}
