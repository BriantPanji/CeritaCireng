<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryMistakeItem extends Model
{
    use HasFactory;

    protected $table = 'delivery_mistake_items';
    public $timestamps = false;

    protected $fillable = [
        'id_delivery_mistake',
        'id_item',
        'quantity',
    ];

    public function mistake()
    {
        return $this->belongsTo(DeliveryMistake::class, 'id_delivery_mistake');
    }

    public function item()
    {
        return $this->belongsTo(\App\Models\Item::class, 'id_item');
    }
}
