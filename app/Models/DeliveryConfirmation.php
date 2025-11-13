<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryConfirmation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'delivery_confirmations';

    protected $fillable = [
        'id_delivery',
        'id_staff',
        'received_at',
        'notes',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'id_staff');
    }
    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'id_delivery');
    }
}
