<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    protected $table = 'returns';

    public function courierBy(){
        return $this->belongsTo(User::class, 'id_deliverer');
    }
    public function staffBy(){
        return $this->belongsTo(User::class, 'id_staff');
    }

    public function returnConfirmBy(){
        return $this->belongsToMany(User::class, 'return_confirmation', 'id_return', 'id_inventaris');
    }

    public function returnItem(){
        return $this->belongsToMany(Item::class, 'return_items', 'id_return', 'id_item');
    }
}
