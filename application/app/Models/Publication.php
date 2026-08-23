<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_available_on_web' => 'boolean',
    ];

    public function regions()
    {
        return $this->belongsToMany(GlobalRegion::class, 'publication_regions');
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }
}
