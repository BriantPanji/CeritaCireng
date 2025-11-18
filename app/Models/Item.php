<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'cost',
        'unit',
        'type',
        'image',
    ];

    public function deliveryItem()
    {
        return $this->belongsToMany(Delivery::class, 'delivery_items', 'id_item', 'id_delivery');
    }

    public function mistakeItem()
    {
        return $this->belongsToMany(DeliveryMistake::class, 'delivery_mistake_items', 'id_item', 'id_delivery_mistake');
    }

    public function returnItem()
    {
        return $this->belongsToMany(ReturnModel::class, 'return_items', 'id_item', 'id_return');
    }

    public function product()
    {
        return $this->belongsToMany(Product::class, 'product_items', 'id_item', 'id_product');
    }

    public function outletSetting()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_item_settings', 'id_item', 'id_outlet');
    }

    public function stock()
    {
        return $this->hasOne(Inventory::class, 'id_item');
    }
}
