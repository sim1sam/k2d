<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOut extends Model
{
    use HasFactory;
    protected $fillable = [
        'bank_id',
        'amount',
        'recipient',
        'payment_mode',
        'category',
        'notes',
        'date',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
