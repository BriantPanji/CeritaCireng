<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function inventaris()
    {
        return $this->belongsTo(User::class, 'id_inventaris');
    }

    public function kurir()
    {
        return $this->belongsTo(User::class, 'id_kurir');
    }

    public function hasDeliveryConfirmation() {
        return $this->hasOne(DeliveryConfirmation::class, 'id_delivery');
    }

    public function hasMistake()
    {
        return $this->hasOne(DeliveryMistake::class);
    } 

    public function hasDeliveryItem(){
        return $this->belongsToMany(Item::class, 'delivery_items', 'id_delivery', 'id_item');
    }
}
