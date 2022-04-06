<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'type',
        'name',
        'note',
        'payment_date'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
