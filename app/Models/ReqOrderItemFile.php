<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqOrderItemFile extends Model
{
    use HasFactory;
    protected $table = 'reqorder_item_files';
    protected $fillable = ['reqorder_item_id', 'file_path'];

    public function reqOrderItem()
    {
        return $this->belongsTo(ReqOrderItem::class, 'reqorder_item_id');
    }
}
