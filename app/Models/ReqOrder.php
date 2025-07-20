<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqOrder extends Model
{
    use HasFactory;
    protected $table = 'reqorders';

    protected $fillable = [
        'user_id', 'order_no', 'total', 'status',
        'is_admin', 'created_by_name', 'created_by_email', 'name', 'email',
    ];


    public function items()
    {
        return $this->hasMany(ReqOrderItem::class, 'reqorder_id');
    }

public function customer()
{
    return $this->belongsTo(User::class, 'user_id'); // Assuming 'user_id' is the foreign key
}
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

}


