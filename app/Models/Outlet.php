<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    public function hasUser()
    {
        return $this->belongsToMany(User::class, 'staff_outlets', 'id_outlet', 'id_user');
    }

    public function hasDelivery()
    {
        return $this->hasMany(Delivery::class);
    }
}
