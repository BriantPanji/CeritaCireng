<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryMistake extends Model
{
    public function deliveryBy()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function deliveryMistakeBy()
    {
        return $this->belongsTo(DeliveryMistake::class);
    }

    public function deliveryMistakeConfirmBy()
    {
        return $this->belongsToMany(User::class, 'delivery_mistake_confirmation', 'id_delivery_mistake', 'id_inventaris');
    }

    public function deliveryMistakeItem(){
        return $this->belongsToMany(Item::class, 'delivery_mistakes_items', 'id_delivery_mistake','id_item');
    }
}
