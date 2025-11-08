<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function deliveryItem(){
        return $this->belongsToMany(Delivery::class, 'delivery_items', 'id_item', 'id_delivery');
    }

    public function mistakeItem(){
        return $this->belongsToMany(DeliveryMistake::class, 'delivery_mistakes_items', 'id_item', 'id_delivery_mistake');
    }

    public function returnItem(){
        return $this->belongsToMany(ReturnModel::class, 'return_items', 'id_item', 'id_return');
    }

    public function product(){
        return $this->belongsToMany(Product::class, 'product_items', 'id_item', 'id_product');
    }
}
