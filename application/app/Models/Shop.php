<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function defaultCurrency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }

    public function globalRegion()
    {
        return $this->belongsTo(GlobalRegion::class, 'global_region_id');
    }

    public function domains()
    {
        return $this->hasMany(ShopDomain::class);
    }
}
