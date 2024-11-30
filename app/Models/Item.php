<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'initial_stock',
        'current_stock',
        'category',
        'image_url'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'initial_stock' => 'integer',
        'current_stock' => 'integer'
    ];
}
