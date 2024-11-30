<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'quantity',
        'total_price',
        'transaction_date'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'transaction_date' => 'datetime'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
