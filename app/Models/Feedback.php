<?php


namespace App\Models; // Ensure this is correct

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks'; // Ensure table name matches your database

    protected $fillable = [
        'name',
        'mobile_number',
        'service_rating',
        'suggestion',
        'birthday',
        'anniversary',
    ];
}
