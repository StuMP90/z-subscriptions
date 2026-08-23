<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $table = 'product_offers';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_setup_offer' => 'boolean',
        'is_available_on_web' => 'boolean',
        'base_price' => 'decimal:2',
        'price' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'setup_config' => 'array',
        'global_region_ids' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function frequency()
    {
        return $this->belongsTo(PublicationFrequency::class, 'frequency_id');
    }
}
