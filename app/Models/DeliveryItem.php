<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id_delivery',
        'id_item',
        'quantity',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'id_delivery');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }
}

