<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    use HasFactory;

    protected $table = 'returns';

    public function deliverer(){
        return $this->belongsTo(User::class, 'id_deliverer');
    }
    public function staff(){
        return $this->belongsTo(User::class, 'id_staff');
    }

    public function returnConfirmations(){
        return $this->hasMany(ReturnConfirmation::class, 'id_return');
    }

    public function returnItem(){
        return $this->belongsToMany(Item::class, 'return_items', 'id_return', 'id_item');
    }
}
