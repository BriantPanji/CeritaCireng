<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMistakeConfirmation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'delivery_mistake_confirmations';

    protected $fillable = [
        'id_delivery_mistake',
        'id_inventaris',
        'confirmed_at',
    ];

    public function inventaris()
    {
        return $this->belongsTo(User::class, 'id_inventaris');
    }

    public function mistake()
    {
        return $this->belongsTo(DeliveryMistake::class, 'id_delivery_mistake');
    }
}
