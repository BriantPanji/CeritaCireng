<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function inventoryBy()
    {
        return $this->belongsTo(User::class, 'id_inventaris');
    }

    public function courierBy()
    {
        return $this->belongsTo(User::class, 'id_kurir');
    }

    public function confirmBy()
    {
        return $this->belongsToMany(User::class, 'delivery_confirmations', 'id_delivery', 'id_staff');
    }

    public function hasMistake()
    {
        return $this->hasOne(DeliveryMistake::class);
    }
}
