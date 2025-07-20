<?php

namespace App\Models;

use App\Models\ReqOrderItemFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqOrderItem extends Model
{
    use HasFactory;

    protected $table = 'reqorder_items';
    protected $fillable = [
        'req_order_id','order_no', 'product_name', 'product_link', 'size', 'quantity', 'price_bdt',
        'note', 'purchase_amount', 'bank_id', 'vendor_id', 'brand_id', 'shipment_id',
        'payment_status', 'paid_amount', 'due', 'payment_method' ,'total' // Add these columns to $fillable
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id')->where('user_type', 'seller');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }


    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id'); // ✅ Correct field name
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    public function files()
    {
        return $this->hasMany(ReqOrderItemFile::class, 'reqorder_item_id');
    }

    public function order()
    {
        return $this->belongsTo(ReqOrder::class, 'reqorder_id'); // Ensure correct foreign key
    }

    public function reqOrder()
    {
        return $this->belongsTo(ReqOrder::class, 'reqorder_id'); // Correct foreign key
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
