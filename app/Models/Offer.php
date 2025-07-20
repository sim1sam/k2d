<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{

    use HasFactory;
    protected $fillable = [
        'image',
        'title',
        'discount',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    public function customers()
    {
        return $this->belongsToMany(User::class, 'offer_user', 'offer_id', 'user_id');
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'offer_user', 'offer_id', 'user_id');
    }

}
