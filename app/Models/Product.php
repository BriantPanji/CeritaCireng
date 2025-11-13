<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function hasItem(){
        return $this->belongsToMany(Item::class, 'product_items', 'id_product', 'id_item');
    }
}
