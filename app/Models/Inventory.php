<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    public $timestamps = false;

    protected $fillable = [
        'id_item',
        'stock',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }
}
