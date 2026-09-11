<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceService extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['eligibility' => 'array', 'price' => 'decimal:2', 'is_published' => 'boolean'];
    }
}