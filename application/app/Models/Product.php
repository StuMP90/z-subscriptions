<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_physical' => 'boolean',
        'is_digital' => 'boolean',
        'has_variants' => 'boolean',
        'track_stock' => 'boolean',
        'allow_backorders' => 'boolean',
        'is_available_on_web' => 'boolean',
        'stock_quantity' => 'integer',
        'global_region_ids' => 'array',
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }
}
