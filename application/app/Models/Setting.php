<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'shop_id' => 'integer',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
