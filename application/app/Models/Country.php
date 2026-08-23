<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function globalRegion()
    {
        return $this->belongsTo(GlobalRegion::class, 'global_region_id');
    }

    public function counties()
    {
        return $this->hasMany(CountyState::class, 'country_id');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
