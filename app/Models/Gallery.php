<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    protected $table = 'galleries'; // Ensure correct table name

    protected $fillable = [
        'title', 'description', 'images'
    ];

    protected $casts = [
        'images' => 'array' // Converts JSON stored in DB to array
    ];
}
