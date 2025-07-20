<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks'; // Table name

    protected $fillable = [
        'name', 'account_number', 'branch', 'country', 'opening_balance', 'current_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2', // Ensure it's stored as decimal
        'current_balance' => 'decimal:2', // Ensure it's stored as decimal
    ];
}
