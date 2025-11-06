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
}
