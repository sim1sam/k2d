<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentProductDetail extends Model
{
    use HasFactory;

    protected $table = 'shipment_product_details'; // Ensure the table name matches your migration

    protected $fillable = [
        'shipment_id',
        'purchase_amount',
        'shipping_cost_bdt',
        'shipping_cost_inr',
        'conversion_rate',
        'total_cogs',
    ];
}
