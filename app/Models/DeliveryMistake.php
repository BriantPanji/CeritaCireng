<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMistake extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id_delivery',
        'id_staff',
        'photo_url',
        'notes',
        'reported_at',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'id_staff');
    }

    public function deliveryMistakeItem()
    {
        return $this->belongsToMany(Item::class, 'delivery_mistake_items', 'id_delivery_mistake', 'id_item');
    }

    public function deliveryMistakeConfirmation()
    {
        return $this->hasOne(DeliveryMistakeConfirmation::class, 'id_delivery_mistake');
    }

    public function staff()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_staff');
    }

    public function items()
    {
        return $this->hasMany(DeliveryMistakeItem::class, 'id_delivery_mistake');
    }

    public function mistakeItems() 
    { 
        return $this->hasMany(DeliveryMistakeItem::class, 'id_delivery_mistake'); 
    }

}
