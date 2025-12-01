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

    public function hasStaff()
    {
        return $this->hasMany(User::class, 'outlet_id', 'id');
    }

    public function hasItemSetting()
    {
        return $this->belongsToMany(Item::class, 'outlet_item_settings', 'id_outlet', 'id_item')->withPivot('quantity');
    }

    public function delivery()
    {
        return $this->hasMany(Delivery::class, "id_outlet");
    }
}
