<?php

namespace App\Models;

use App\Models\ShipmentProductDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Iyzipay\Model\OrderItem;

class Shipment extends Model
{
    use HasFactory;

    protected $table = 'shipments'; // Table name

    protected $fillable = [
         'name',
        'shipment_note'
    ];


    public function details(): HasMany
    {
        return $this->hasMany(ShipmentProductDetail::class, 'shipment_id', 'id');
    }


    public function items()
    {
        return $this->hasMany(ReqOrderItem::class, 'shipment_id');
    }


}
