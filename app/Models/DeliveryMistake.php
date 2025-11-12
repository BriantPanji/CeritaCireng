<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryMistake extends Model
{
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function reportedBy() {
        return $this->belongsTo(User::class, 'id_staff');
    }

    public function deliveryMistakeItem(){
        return $this->belongsToMany(Item::class, 'delivery_mistake_items', 'id_delivery_mistake','id_item');
    }

    public function deliveryMistakeConfirmation() {
        return $this->hasOne(DeliveryMistakeConfirmation::class, 'id_delivery_mistake');
    }
}
