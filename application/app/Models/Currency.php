<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_base_currency' => 'boolean',
            'conversion_rate' => 'decimal:10',
            'is_active' => 'boolean',
        ];
    }
}
