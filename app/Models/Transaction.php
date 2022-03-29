<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'type',
        'total',
        'balance'
    ];

    protected $casts = [
        'total' => 'double',
        'balance' => 'balance',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
