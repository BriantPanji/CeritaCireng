<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;
    protected $fillable = [
        'location',
        'name',
        'status',
    ];

    protected $table = 'outlets';

    public function staff()
    {
        return $this->hasMany(User::class, 'id_outlet', 'id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'outlet_item_settings', 'id_outlet', 'id_item');
    }
}
