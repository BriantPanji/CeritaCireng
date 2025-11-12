<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $fillable = [
        'location',
        'name',
        'status',
    ];

    protected $table = 'outlets';

    public function hasStaff()
    {
        return $this->hasMany(User::class, 'id_outlet', 'id');
    }

    public function hasItemSetting() {
        return $this->belongsToMany(Item::class, 'outlet_item_settings', 'id_outlet', 'id_item');
    }
}
