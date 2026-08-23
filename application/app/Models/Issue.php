<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'publication_date' => 'date',
        'is_active' => 'boolean',
        'is_available_on_web' => 'boolean',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}
