<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'bank_id',
        'amount',
        'source',
        'transaction_type',
        'category',
        'payment_mode',
        'notes',
        'date',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
