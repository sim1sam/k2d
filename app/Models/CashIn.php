<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashIn extends Model
{
    use HasFactory;
    protected $fillable = [
        'bank_id',
        'amount',
        'payment_mode',
        'category',
        'notes',
        'date',
        'source',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
