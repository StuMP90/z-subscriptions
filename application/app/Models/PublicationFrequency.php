<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationFrequency extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function publications()
    {
        return $this->hasMany(Publication::class);
    }
}
