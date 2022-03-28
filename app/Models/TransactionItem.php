<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'admin_id',
        'name',
        'quantity',
        'price',
        'image'
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
